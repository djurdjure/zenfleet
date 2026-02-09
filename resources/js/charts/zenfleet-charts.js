import ApexCharts from 'apexcharts';
import { getChartPayload, getChartSignature, isPieLikeType } from './chart-contract';
import { buildThemeOptions } from './chart-theme';

const CHART_SELECTOR = '[data-zenfleet-chart]';

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

function buildChartOptions(payload) {
    const { chart, labels, series, options } = payload;
    const renderStart = { value: 0 };
    const userEvents = options?.chart?.events ?? {};

    const events = {
        beforeMount: (...args) => {
            renderStart.value = performance.now();
            if (typeof userEvents.beforeMount === 'function') {
                userEvents.beforeMount(...args);
            }
        },
        mounted: (chartContext, config) => {
            const duration = performance.now() - renderStart.value;
            window.ZenFleet?.metrics?.track?.('chart_render', {
                chartId: chartContext?.opts?.chart?.id ?? chart.id,
                duration,
            });

            if (typeof userEvents.mounted === 'function') {
                userEvents.mounted(chartContext, config);
            }
        },
        updated: (chartContext, config) => {
            window.ZenFleet?.metrics?.track?.('chart_updated', {
                chartId: chartContext?.opts?.chart?.id ?? chart.id,
            });

            if (typeof userEvents.updated === 'function') {
                userEvents.updated(chartContext, config);
            }
        },
    };

    const baseOptions = buildThemeOptions(payload, events);
    const mergedOptions = mergeDeep(baseOptions, options);
    mergedOptions.series = series;

    if (isPieLikeType(chart.type)) {
        delete mergedOptions.xaxis;
    }

    return mergedOptions;
}

class ZenFleetChartsManager {
    constructor() {
        this.instances = new Map();
    }

    renderOne(element) {
        if (!element) return;

        const payload = getChartPayload(element);
        const signature = getChartSignature(payload);
        const previousSignature = element.dataset.zfChartSignature;
        const previousChart = this.instances.get(element);

        if (previousChart && signature === previousSignature) {
            return;
        }

        if (previousChart) {
            previousChart.destroy();
            this.instances.delete(element);
        }

        const options = buildChartOptions(payload);

        // Accessibility baseline for chart containers.
        element.setAttribute('role', 'img');
        if (!element.getAttribute('aria-label')) {
            element.setAttribute('aria-label', payload.chart.ariaLabel || 'Graphique ZenFleet');
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
    let observer = null;
    const scheduleRefresh = () => {
        if (rafId !== null) {
            cancelAnimationFrame(rafId);
        }

        rafId = requestAnimationFrame(() => {
            zenFleetChartsManager.refresh(document);
            rafId = null;
        });
    };

    const containsChartNode = (node) => {
        if (!(node instanceof Element)) {
            return false;
        }

        return node.matches(CHART_SELECTOR) || Boolean(node.querySelector(CHART_SELECTOR));
    };

    const initDomObserver = () => {
        if (observer || !document.body) {
            return;
        }

        observer = new MutationObserver((mutations) => {
            const shouldRefresh = mutations.some((mutation) => {
                if (containsChartNode(mutation.target)) {
                    return true;
                }

                return (
                    Array.from(mutation.addedNodes).some(containsChartNode) ||
                    Array.from(mutation.removedNodes).some(containsChartNode)
                );
            });

            if (shouldRefresh) {
                scheduleRefresh();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initDomObserver();
            scheduleRefresh();
        }, { once: true });
    } else {
        initDomObserver();
        scheduleRefresh();
    }

    document.addEventListener('livewire:initialized', () => {
        initDomObserver();
        scheduleRefresh();
    });
    document.addEventListener('livewire:navigated', scheduleRefresh);
    document.addEventListener('dashboard:data-updated', scheduleRefresh);

    return zenFleetChartsManager;
}

export { zenFleetChartsManager, setupZenFleetChartsLifecycle };
