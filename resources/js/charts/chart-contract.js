const DEFAULT_TYPE = 'line';
const PIE_LIKE_TYPES = ['pie', 'donut', 'radialBar', 'polarArea'];
const SUPPORTED_TYPES = ['line', 'area', 'bar', 'pie', 'donut', 'radialBar', 'polarArea'];
const DEFAULT_VERSION = '1.0';

function parseJsonAttribute(value, fallback) {
    if (!value) return fallback;

    try {
        return JSON.parse(value);
    } catch (error) {
        console.warn('[ZenFleetCharts] Invalid JSON payload:', value, error);
        return fallback;
    }
}

function sanitizeType(rawType) {
    const type = `${rawType ?? ''}`.trim();
    return SUPPORTED_TYPES.includes(type) ? type : DEFAULT_TYPE;
}

function normalizeSeries(type, series) {
    if (PIE_LIKE_TYPES.includes(type)) {
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

function normalizeLegacyPayload(element) {
    const type = sanitizeType(element.dataset.chartType || DEFAULT_TYPE);
    const labels = parseJsonAttribute(element.dataset.chartLabels, []);
    const rawSeries = parseJsonAttribute(element.dataset.chartSeries, []);
    const options = parseJsonAttribute(element.dataset.chartOptions, {});
    const height = Number(element.dataset.chartHeight || 320);
    const id = element.dataset.chartId || element.id || `zenfleet-chart-${Date.now()}`;
    const ariaLabel = element.dataset.chartAriaLabel || 'Graphique ZenFleet';

    return {
        meta: {
            version: DEFAULT_VERSION,
            source: 'legacy-dataset',
        },
        chart: {
            id,
            type,
            height,
            ariaLabel,
        },
        labels: Array.isArray(labels) ? labels : [],
        series: normalizeSeries(type, rawSeries),
        options: options && typeof options === 'object' ? options : {},
    };
}

function normalizeContractPayload(element) {
    const raw = parseJsonAttribute(element.dataset.chartPayload, null);
    if (!raw || typeof raw !== 'object') {
        return null;
    }

    const chart = raw.chart && typeof raw.chart === 'object' ? raw.chart : {};
    const type = sanitizeType(chart.type || raw.type || DEFAULT_TYPE);
    const id = chart.id || raw.chartId || element.dataset.chartId || element.id || `zenfleet-chart-${Date.now()}`;
    const height = Number(chart.height || raw.height || element.dataset.chartHeight || 320);
    const ariaLabel = chart.ariaLabel || raw.ariaLabel || element.dataset.chartAriaLabel || 'Graphique ZenFleet';
    const labels = Array.isArray(raw.labels) ? raw.labels : [];
    const options = raw.options && typeof raw.options === 'object' ? raw.options : {};
    const rawSeries = raw.series ?? [];

    return {
        meta: {
            version: raw.meta?.version || DEFAULT_VERSION,
            source: raw.meta?.source || 'contract-dataset',
            generatedAt: raw.meta?.generatedAt || null,
        },
        chart: {
            id,
            type,
            height,
            ariaLabel,
        },
        labels,
        series: normalizeSeries(type, rawSeries),
        options,
    };
}

function getChartPayload(element) {
    return normalizeContractPayload(element) ?? normalizeLegacyPayload(element);
}

function getChartSignature(payload) {
    return JSON.stringify({
        type: payload.chart.type,
        labels: payload.labels,
        series: payload.series,
        options: payload.options,
        height: payload.chart.height,
    });
}

function isPieLikeType(type) {
    return PIE_LIKE_TYPES.includes(type);
}

export { getChartPayload, getChartSignature, isPieLikeType };
