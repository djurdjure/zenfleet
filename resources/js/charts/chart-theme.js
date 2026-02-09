const DEFAULT_CHART_COLORS = ['#2563eb', '#0891b2', '#0f766e', '#65a30d', '#d97706', '#dc2626'];

const ZENFLEET_CHART_THEME = {
    colors: DEFAULT_CHART_COLORS,
    fontFamily: 'Inter, sans-serif',
    grid: {
        borderColor: '#e2e8f0',
        strokeDashArray: 4,
    },
    axis: {
        labelColor: '#475569',
        titleColor: '#334155',
    },
    legend: {
        labelColor: '#334155',
    },
    tooltip: {
        theme: 'light',
    },
};

function buildThemeOptions(payload, events = {}) {
    const { chart, labels } = payload;

    return {
        chart: {
            id: chart.id,
            type: chart.type,
            height: chart.height,
            toolbar: {
                show: false,
            },
            animations: {
                easing: 'easeinout',
                speed: 350,
            },
            events,
            fontFamily: ZENFLEET_CHART_THEME.fontFamily,
            background: 'transparent',
        },
        colors: ZENFLEET_CHART_THEME.colors,
        labels,
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: 'smooth',
            width: 2,
        },
        grid: {
            borderColor: ZENFLEET_CHART_THEME.grid.borderColor,
            strokeDashArray: ZENFLEET_CHART_THEME.grid.strokeDashArray,
        },
        legend: {
            position: 'bottom',
            labels: {
                colors: ZENFLEET_CHART_THEME.legend.labelColor,
            },
        },
        xaxis: labels.length > 0 ? {
            categories: labels,
            labels: {
                style: {
                    colors: ZENFLEET_CHART_THEME.axis.labelColor,
                },
            },
        } : {},
        yaxis: {
            labels: {
                style: {
                    colors: ZENFLEET_CHART_THEME.axis.labelColor,
                },
            },
            title: {
                style: {
                    color: ZENFLEET_CHART_THEME.axis.titleColor,
                },
            },
        },
        tooltip: {
            theme: ZENFLEET_CHART_THEME.tooltip.theme,
        },
        noData: {
            text: 'Aucune donnée disponible',
        },
    };
}

export { ZENFLEET_CHART_THEME, buildThemeOptions };
