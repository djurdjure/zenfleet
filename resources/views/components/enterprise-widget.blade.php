{{--
|--------------------------------------------------------------------------
| 📊 Enterprise Widget Component
|--------------------------------------------------------------------------
| Widget ultra-professionnel pour dashboard enterprise avec :
| - Métriques animées
| - Graphiques intégrés
| - Alertes intelligentes
| - Actions rapides
| - Design responsive
--}}

@props([
    'title' => '',
    'subtitle' => '',
    'value' => null,
    'previousValue' => null,
    'type' => 'metric', // metric, chart, list, activity, alert
    'trend' => null, // up, down, stable
    'trendLabel' => '',
    'icon' => 'chart-bar',
    'iconColor' => 'indigo',
    'size' => 'default', // small, default, large
    'gradient' => true,
    'loading' => false,
    'refreshable' => false,
    'expandable' => false,
    'actions' => [],
    'alertLevel' => null, // success, warning, danger
    'data' => [],
    'chartType' => 'line' // line, bar, doughnut, area
])

@php
$widgetId = 'widget-' . Str::random(8);

$colorConfig = [
    'indigo' => [
        'bg' => 'from-indigo-500 to-purple-600',
        'light' => 'from-indigo-50 to-purple-50',
        'text' => 'text-indigo-600',
        'border' => 'border-indigo-200'
    ],
    'blue' => [
        'bg' => 'from-blue-500 to-cyan-600',
        'light' => 'from-blue-50 to-cyan-50',
        'text' => 'text-blue-600',
        'border' => 'border-blue-200'
    ],
    'green' => [
        'bg' => 'from-green-500 to-emerald-600',
        'light' => 'from-green-50 to-emerald-50',
        'text' => 'text-green-600',
        'border' => 'border-green-200'
    ],
    'orange' => [
        'bg' => 'from-orange-500 to-amber-600',
        'light' => 'from-orange-50 to-amber-50',
        'text' => 'text-orange-600',
        'border' => 'border-orange-200'
    ],
    'red' => [
        'bg' => 'from-red-500 to-rose-600',
        'light' => 'from-red-50 to-rose-50',
        'text' => 'text-red-600',
        'border' => 'border-red-200'
    ],
    'gray' => [
        'bg' => 'from-gray-500 to-slate-600',
        'light' => 'from-gray-50 to-slate-50',
        'text' => 'text-gray-600',
        'border' => 'border-gray-200'
    ]
];

$colors = $colorConfig[$iconColor] ?? $colorConfig['indigo'];

$sizeConfig = [
    'small' => 'p-4',
    'default' => 'p-6',
    'large' => 'p-8'
];

$currentSize = $sizeConfig[$size] ?? $sizeConfig['default'];

$alertLevelColors = [
    'success' => 'from-green-500 to-emerald-600',
    'warning' => 'from-yellow-500 to-orange-600',
    'danger' => 'from-red-500 to-rose-600'
];
@endphp

<div
    id="{{ $widgetId }}"
    class="enterprise-widget bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl border {{ $colors['border'] }}/20 overflow-hidden hover:shadow-2xl transition-all duration-500 hover:scale-[1.02] {{ $currentSize }}"
    x-data="enterpriseWidget({
        type: '{{ $type }}',
        value: {{ json_encode($value) }},
        previousValue: {{ json_encode($previousValue) }},
        data: {{ json_encode($data) }},
        chartType: '{{ $chartType }}',
        refreshable: {{ $refreshable ? 'true' : 'false' }},
        loading: {{ $loading ? 'true' : 'false' }}
    })"
    data-animate="scale-in"
    data-animate-delay="200"
>
    {{-- Alert Level Indicator --}}
    @if($alertLevel)
    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r {{ $alertLevelColors[$alertLevel] ?? $alertLevelColors['warning'] }}"></div>
    @endif

    {{-- Loading Overlay --}}
    <div x-show="loading" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-10" x-transition>
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 {{ $colors['text'] }}"></div>
            <p class="mt-2 text-sm text-gray-600">Actualisation...</p>
        </div>
    </div>

    {{-- Widget Content --}}
    <div class="relative">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-start space-x-4">
                {{-- Icon --}}
                <div class="flex-shrink-0 p-3 bg-gradient-to-r {{ $colors['bg'] }} rounded-xl shadow-lg animate-float">
                    @if($icon === 'chart-bar')
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    @elseif($icon === 'users')
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    @elseif($icon === 'truck')
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    @elseif($icon === 'credit-card')
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    @elseif($icon === 'exclamation-triangle')
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    @else
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    @endif
                </div>

                {{-- Title & Subtitle --}}
                <div class="flex-1 min-w-0">
                    @if($title)
                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                    <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center space-x-2">
                {{-- Refresh Button --}}
                @if($refreshable)
                <button @click="refresh()"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
                        title="Actualiser">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                @endif

                {{-- Expand Button --}}
                @if($expandable)
                <button @click="toggleExpanded()"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
                        title="Développer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </button>
                @endif

                {{-- Custom Actions --}}
                @foreach($actions as $action)
                <button @click="{{ $action['action'] }}()"
                        class="p-2 text-gray-400 hover:{{ $colors['text'] }} hover:bg-{{ $iconColor }}-50 rounded-lg transition-all"
                        title="{{ $action['title'] ?? '' }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $action['icon'] !!}
                    </svg>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Widget Type Specific Content --}}
        @if($type === 'metric')
            {{-- Metric Widget --}}
            <div class="space-y-4">
                @if($value !== null)
                <div class="flex items-end space-x-4">
                    <div class="flex-1">
                        <div class="text-3xl font-bold text-gray-900"
                             x-text="formatValue(currentValue)"
                             data-metric="{{ $value }}"
                             data-duration="2000">
                        </div>

                        {{-- Trend Indicator --}}
                        @if($trend && $trendLabel)
                        <div class="flex items-center mt-2">
                            @if($trend === 'up')
                            <svg class="h-4 w-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            @elseif($trend === 'down')
                            <svg class="h-4 w-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                            @else
                            <svg class="h-4 w-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            @endif
                            <span class="text-sm {{ $trend === 'up' ? 'text-green-600' : ($trend === 'down' ? 'text-red-600' : 'text-gray-600') }}">
                                {{ $trendLabel }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

        @elseif($type === 'chart')
            {{-- Chart Widget --}}
            <div class="h-64">
                <canvas x-ref="chartCanvas" class="w-full h-full"></canvas>
            </div>

        @elseif($type === 'list')
            {{-- List Widget --}}
            <div class="space-y-3">
                @foreach($data as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center space-x-3">
                        @if(isset($item['icon']))
                        <div class="flex-shrink-0 w-8 h-8 {{ $colors['text'] }}">
                            {!! $item['icon'] !!}
                        </div>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $item['label'] ?? '' }}</div>
                            @if(isset($item['description']))
                            <div class="text-xs text-gray-600">{{ $item['description'] }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="text-sm font-bold {{ $colors['text'] }}">
                        {{ $item['value'] ?? '' }}
                    </div>
                </div>
                @endforeach
            </div>

        @elseif($type === 'activity')
            {{-- Activity Feed Widget --}}
            <div class="space-y-4 max-h-64 overflow-y-auto custom-scrollbar">
                @foreach($data as $activity)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 {{ $colors['bg'] }} rounded-full mt-2"></div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-gray-900">{{ $activity['message'] ?? '' }}</div>
                        @if(isset($activity['time']))
                        <div class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

        @elseif($type === 'alert')
            {{-- Alert Widget --}}
            <div class="bg-gradient-to-r {{ $colors['light'] }} border {{ $colors['border'] }} rounded-xl p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        @if(isset($data['message']))
                        <div class="text-sm font-medium {{ $colors['text'] }}">{{ $data['message'] }}</div>
                        @endif
                        @if(isset($data['description']))
                        <div class="text-xs text-gray-600 mt-1">{{ $data['description'] }}</div>
                        @endif
                    </div>
                    @if(isset($data['action']))
                    <button class="text-xs font-medium {{ $colors['text'] }} hover:underline">
                        {{ $data['action'] }}
                    </button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Custom Slot Content --}}
        {{ $slot }}
    </div>

    {{-- Decorative Elements --}}
    @if($gradient)
    <div class="absolute -top-2 -right-2 w-16 h-16 bg-gradient-to-r {{ $colors['bg'] }} rounded-full opacity-10 animate-pulse"></div>
    <div class="absolute -bottom-4 -left-4 w-12 h-12 bg-gradient-to-r {{ $colors['bg'] }} rounded-full opacity-5 animate-float"></div>
    @endif
</div>

{{-- Alpine.js Component --}}
@push('scripts')
<script>
function enterpriseWidget(config) {
    return {
        // Data
        type: config.type,
        originalValue: config.value,
        currentValue: 0,
        previousValue: config.previousValue,
        data: config.data,
        chartType: config.chartType,

        // State
        loading: config.loading,
        refreshable: config.refreshable,
        expanded: false,
        chart: null,

        init() {
            if (this.originalValue !== null) {
                this.animateValue();
            }

            if (this.type === 'chart') {
                this.$nextTick(() => {
                    this.initChart();
                });
            }
        },

        animateValue() {
            const duration = 2000;
            const startTime = performance.now();
            const targetValue = this.originalValue;

            const animate = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Easing function
                const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                this.currentValue = Math.floor(targetValue * easeOutQuart);

                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            };

            requestAnimationFrame(animate);
        },

        formatValue(value) {
            if (typeof value === 'number') {
                return new Intl.NumberFormat('fr-FR').format(value);
            }
            return value;
        },

        refresh() {
            this.loading = true;

            // Simulate API call
            setTimeout(() => {
                this.loading = false;
                // Trigger refresh event
                this.$dispatch('widget-refreshed', {
                    widgetId: this.$el.id,
                    type: this.type
                });
            }, 1500);
        },

        toggleExpanded() {
            this.expanded = !this.expanded;
            this.$dispatch('widget-toggled', {
                widgetId: this.$el.id,
                expanded: this.expanded
            });
        },

        initChart() {
            if (!this.$refs.chartCanvas || this.data.length === 0) return;

            const ctx = this.$refs.chartCanvas.getContext('2d');

            // Basic chart configuration
            const chartConfig = {
                type: this.chartType,
                data: {
                    labels: this.data.map(item => item.label || ''),
                    datasets: [{
                        data: this.data.map(item => item.value || 0),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(139, 92, 246, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            };

            // Simple chart creation (would use Chart.js in real implementation)
            this.drawSimpleChart(ctx, this.data);
        },

        drawSimpleChart(ctx, data) {
            const canvas = ctx.canvas;
            const width = canvas.width;
            const height = canvas.height;

            // Clear canvas
            ctx.clearRect(0, 0, width, height);

            if (data.length === 0) return;

            // Draw a simple line chart
            const maxValue = Math.max(...data.map(d => d.value || 0));
            const padding = 40;
            const chartWidth = width - 2 * padding;
            const chartHeight = height - 2 * padding;

            // Draw axes
            ctx.strokeStyle = '#e5e7eb';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(padding, padding);
            ctx.lineTo(padding, height - padding);
            ctx.lineTo(width - padding, height - padding);
            ctx.stroke();

            // Draw data points and line
            ctx.strokeStyle = '#3b82f6';
            ctx.fillStyle = '#3b82f6';
            ctx.lineWidth = 2;
            ctx.beginPath();

            data.forEach((point, index) => {
                const x = padding + (index / (data.length - 1)) * chartWidth;
                const y = height - padding - ((point.value || 0) / maxValue) * chartHeight;

                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }

                // Draw point
                ctx.beginPath();
                ctx.arc(x, y, 4, 0, 2 * Math.PI);
                ctx.fill();
            });

            ctx.stroke();
        }
    }
}
</script>
@endpush