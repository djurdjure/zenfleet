import ApexCharts from 'apexcharts';

const DEFAULT_COLORS = ['#2563eb', '#0ea5e9', '#14b8a6', '#22c55e', '#f59e0b', '#ef4444'];
const CHART_SELECTOR = '[data-zenfleet-chart]';

function parseJsonAttribute(value, fallback) {
    if (!value) return fallback;

    try {
        return JSON.parse(value);
    } catch (error) {
        console.warn('[ZenFleetCharts] Invalid JSON attribute:', value, error);
        return fallback;
    }
}

function mergeDeep(target = {}, source = {}) {
    const output = { ...target };

    Object.keys(source).forEach((key) => {
        if (
            source[key] &&
            typeof source[key] === 'object' &&
            !Array.isArray(source[key]) &&
            typeof output[key] === 'object' &&
            !Array.isArray(output[key])
        ) {
            output[key] = mergeDeep(output[key], source[key]);
            return;
        }

        output[key] = source[key];
    });

    return output;
}

function getChartPayload(element) {
    const type = element.dataset.chartType || 'line';
    const labels = parseJsonAttribute(element.dataset.chartLabels, []);
    const series = parseJsonAttribute(element.dataset.chartSeries, []);
    const options = parseJsonAttribute(element.dataset.chartOptions, {});
    const height = Number(element.dataset.chartHeight || 320);
    const chartId = element.dataset.chartId || element.id || `zenfleet-chart-${Date.now()}`;

    return { type, labels, series, options, height, chartId };
}

function normalizeSeries(type, series) {
    const pieLikeTypes = ['pie', 'donut', 'radialBar', 'polarArea'];

    if (pieLikeTypes.includes(type)) {
        if (Array.isArray(series) && series.every((item) => typeof item === 'number')) {
            return series;
        }

        if (Array.isArray(series) && series[0] && Array.isArray(series[0].data)) {
            return series[0].data;
        }

        return [];
    }

    if (!Array.isArray(series)) {
        return [];
    }

    return series.map((entry, index) => {
        if (entry && Array.isArray(entry.data)) {
            return {
                name: entry.name ?? entry.label ?? `Serie ${index + 1}`,
                data: entry.data,
            };
        }

        if (Array.isArray(entry)) {
            return {
                name: `Serie ${index + 1}`,
                data: entry,
            };
        }

        return {
            name: `Serie ${index + 1}`,
            data: [],
        };
    });
}

function buildChartOptions(element, payload) {
    const { type, labels, series, options, height, chartId } = payload;
    const normalizedSeries = normalizeSeries(type, series);
    const renderStart = { value: 0 };
    const userEvents = options?.chart?.events ?? {};

    const baseOptions = {
        chart: {
            id: chartId,
            type,
            height,
            toolbar: {
                show: false,
            },
            animations: {
                easing: 'easeinout',
                speed: 350,
            },
            events: {
                beforeMount: (...args) => {
                    renderStart.value = performance.now();
                    if (typeof userEvents.beforeMount === 'function') {
                        userEvents.beforeMount(...args);
                    }
                },
                mounted: (chartContext, config) => {
                    const duration = performance.now() - renderStart.value;
                    window.ZenFleet?.metrics?.track?.('chart_render', {
                        chartId: chartContext?.opts?.chart?.id ?? chartId,
                        duration,
                    });

                    if (typeof userEvents.mounted === 'function') {
                        userEvents.mounted(chartContext, config);
                    }
                },
                updated: (chartContext, config) => {
                    window.ZenFleet?.metrics?.track?.('chart_updated', {
                        chartId: chartContext?.opts?.chart?.id ?? chartId,
                    });

                    if (typeof userEvents.updated === 'function') {
                        userEvents.updated(chartContext, config);
                    }
                },
            },
            fontFamily: 'Inter, sans-serif',
            background: 'transparent',
        },
        series: normalizedSeries,
        labels,
        colors: DEFAULT_COLORS,
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: 'smooth',
            width: 2,
        },
        legend: {
            position: 'bottom',
        },
        xaxis: labels.length > 0 ? { categories: labels } : {},
        tooltip: {
            theme: 'light',
        },
        noData: {
            text: 'Aucune donnée disponible',
        },
    };

    const mergedOptions = mergeDeep(baseOptions, options);

    if (['pie', 'donut', 'radialBar', 'polarArea'].includes(type)) {
        delete mergedOptions.xaxis;
    }

    return mergedOptions;
}

class ZenFleetChartsManager {
    constructor() {
        this.instances = new Map();
    }

    getSignature(element) {
        return JSON.stringify({
            type: element.dataset.chartType || '',
            labels: element.dataset.chartLabels || '',
            series: element.dataset.chartSeries || '',
            options: element.dataset.chartOptions || '',
            height: element.dataset.chartHeight || '',
        });
    }

    renderOne(element) {
        if (!element) return;

        const signature = this.getSignature(element);
        const previousSignature = element.dataset.zfChartSignature;
        const previousChart = this.instances.get(element);

        if (previousChart && signature === previousSignature) {
            return;
        }

        if (previousChart) {
            previousChart.destroy();
            this.instances.delete(element);
        }

        const payload = getChartPayload(element);
        const options = buildChartOptions(element, payload);

        // Accessibility baseline for chart containers.
        element.setAttribute('role', 'img');
        if (!element.getAttribute('aria-label')) {
            element.setAttribute('aria-label', element.dataset.chartAriaLabel || 'Graphique ZenFleet');
        }

        const chart = new ApexCharts(element, options);
        chart.render();

        this.instances.set(element, chart);
        element.dataset.zfChartSignature = signature;
    }

    pruneDetached() {
        Array.from(this.instances.entries()).forEach(([element, chart]) => {
            if (!document.body.contains(element)) {
                chart.destroy();
                this.instances.delete(element);
            }
        });
    }

    refresh(root = document) {
        this.pruneDetached();
        root.querySelectorAll(CHART_SELECTOR).forEach((element) => this.renderOne(element));
    }

    destroyAll() {
        Array.from(this.instances.values()).forEach((chart) => chart.destroy());
        this.instances.clear();
    }
}

const zenFleetChartsManager = new ZenFleetChartsManager();

function setupZenFleetChartsLifecycle() {
    if (window.__zenfleetChartsLifecycleReady) {
        return zenFleetChartsManager;
    }

    window.__zenfleetChartsLifecycleReady = true;
    window.ZenFleetCharts = zenFleetChartsManager;

    let rafId = null;
    const scheduleRefresh = () => {
        if (rafId !== null) {
            cancelAnimationFrame(rafId);
        }

        rafId = requestAnimationFrame(() => {
            zenFleetChartsManager.refresh(document);
            rafId = null;
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleRefresh, { once: true });
    } else {
        scheduleRefresh();
    }

    document.addEventListener('livewire:initialized', scheduleRefresh);
    document.addEventListener('livewire:navigated', scheduleRefresh);
    document.addEventListener('dashboard:data-updated', scheduleRefresh);

    return zenFleetChartsManager;
}

export { zenFleetChartsManager, setupZenFleetChartsLifecycle };

