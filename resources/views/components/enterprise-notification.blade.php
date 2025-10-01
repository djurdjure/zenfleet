{{--
|--------------------------------------------------------------------------
| 🔔 Enterprise Notification Component
|--------------------------------------------------------------------------
| Composant de notifications ultra-professionnel avec animations avancées
| Supports : success, error, warning, info, loading
--}}

@props([
    'type' => 'info',
    'title' => '',
    'message' => '',
    'dismissible' => true,
    'timeout' => 5000,
    'actions' => [],
    'icon' => null,
    'gradient' => true,
    'sound' => false
])

@php
$notificationConfig = [
    'success' => [
        'icon' => 'check-circle',
        'colors' => 'from-emerald-500 to-green-600',
        'bgColors' => 'from-emerald-50 to-green-50',
        'textColors' => 'text-emerald-800',
        'borderColors' => 'border-emerald-200',
        'sound' => 'success'
    ],
    'error' => [
        'icon' => 'x-circle',
        'colors' => 'from-red-500 to-rose-600',
        'bgColors' => 'from-red-50 to-rose-50',
        'textColors' => 'text-red-800',
        'borderColors' => 'border-red-200',
        'sound' => 'error'
    ],
    'warning' => [
        'icon' => 'exclamation-triangle',
        'colors' => 'from-amber-500 to-orange-600',
        'bgColors' => 'from-amber-50 to-orange-50',
        'textColors' => 'text-amber-800',
        'borderColors' => 'border-amber-200',
        'sound' => 'warning'
    ],
    'info' => [
        'icon' => 'information-circle',
        'colors' => 'from-blue-500 to-indigo-600',
        'bgColors' => 'from-blue-50 to-indigo-50',
        'textColors' => 'text-blue-800',
        'borderColors' => 'border-blue-200',
        'sound' => 'info'
    ],
    'loading' => [
        'icon' => 'loading',
        'colors' => 'from-gray-500 to-slate-600',
        'bgColors' => 'from-gray-50 to-slate-50',
        'textColors' => 'text-gray-800',
        'borderColors' => 'border-gray-200',
        'sound' => null
    ]
];

$config = $notificationConfig[$type] ?? $notificationConfig['info'];
$iconName = $icon ?? $config['icon'];
$notificationId = 'notification-' . Str::random(8);
@endphp

<div
    id="{{ $notificationId }}"
    class="enterprise-notification fixed top-4 right-4 max-w-md w-full z-50 transform translate-x-full opacity-0 transition-all duration-500 ease-out"
    x-data="{
        show: false,
        dismissed: false,
        timeout: {{ $timeout }},
        timeoutId: null,
        progressWidth: 100,
        progressInterval: null,

        init() {
            this.$nextTick(() => {
                this.show = true;
                this.$el.classList.remove('translate-x-full', 'opacity-0');
                this.$el.classList.add('translate-x-0', 'opacity-100');

                if (this.timeout > 0) {
                    this.startProgress();
                    this.timeoutId = setTimeout(() => {
                        this.dismiss();
                    }, this.timeout);
                }

                @if($sound && $config['sound'])
                    this.playNotificationSound('{{ $config['sound'] }}');
                @endif
            });
        },

        dismiss() {
            if (this.dismissed) return;
            this.dismissed = true;

            if (this.timeoutId) clearTimeout(this.timeoutId);
            if (this.progressInterval) clearInterval(this.progressInterval);

            this.$el.classList.add('translate-x-full', 'opacity-0');
            this.$el.classList.remove('translate-x-0', 'opacity-100');

            setTimeout(() => {
                this.$el.remove();
            }, 500);
        },

        startProgress() {
            if (this.timeout <= 0) return;

            const step = 100 / (this.timeout / 100);
            this.progressInterval = setInterval(() => {
                this.progressWidth -= step;
                if (this.progressWidth <= 0) {
                    clearInterval(this.progressInterval);
                }
            }, 100);
        },

        playNotificationSound(type) {
            // Intégration optionnelle des sons de notification
            const sounds = {
                success: '/sounds/notification-success.mp3',
                error: '/sounds/notification-error.mp3',
                warning: '/sounds/notification-warning.mp3',
                info: '/sounds/notification-info.mp3'
            };

            if (sounds[type]) {
                const audio = new Audio(sounds[type]);
                audio.volume = 0.3;
                audio.play().catch(() => {});
            }
        }
    }"
    x-init="init()"
    role="alert"
    aria-live="assertive"
    data-animate="slide-in-right"
>
    {{-- Notification Card --}}
    <div class="relative bg-white rounded-2xl shadow-2xl border {{ $config['borderColors'] }} overflow-hidden backdrop-blur-sm {{ $gradient ? 'bg-gradient-to-br ' . $config['bgColors'] : '' }}">

        {{-- Progress Bar --}}
        @if($timeout > 0)
        <div class="absolute top-0 left-0 h-1 bg-gradient-to-r {{ $config['colors'] }} transition-all duration-100 ease-linear"
             :style="`width: ${progressWidth}%`"></div>
        @endif

        {{-- Main Content --}}
        <div class="p-6">
            <div class="flex items-start space-x-4">

                {{-- Icon --}}
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-r {{ $config['colors'] }} flex items-center justify-center shadow-lg animate-pulse-glow">
                        @if($iconName === 'loading')
                            <svg class="w-6 h-6 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        @elseif($iconName === 'check-circle')
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($iconName === 'x-circle')
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($iconName === 'exclamation-triangle')
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    @if($title)
                    <h4 class="text-lg font-bold {{ $config['textColors'] }} mb-1">
                        {{ $title }}
                    </h4>
                    @endif

                    @if($message)
                    <p class="text-sm {{ $config['textColors'] }}/80 leading-relaxed">
                        {{ $message }}
                    </p>
                    @endif

                    {{-- Slot for custom content --}}
                    {{ $slot }}

                    {{-- Actions --}}
                    @if(count($actions) > 0)
                    <div class="flex items-center space-x-3 mt-4">
                        @foreach($actions as $action)
                        <button
                            type="button"
                            onclick="{{ $action['onclick'] ?? '' }}"
                            class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-105 {{ $action['class'] ?? 'bg-white/80 hover:bg-white ' . $config['textColors'] . ' border border-current/20' }}"
                        >
                            @if(isset($action['icon']))
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $action['icon'] !!}
                            </svg>
                            @endif
                            {{ $action['label'] }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Dismiss Button --}}
                @if($dismissible)
                <div class="flex-shrink-0">
                    <button
                        type="button"
                        @click="dismiss()"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $config['textColors'] }}/60 hover:{{ $config['textColors'] }} hover:bg-white/50 transition-all duration-200 hover:scale-110"
                        aria-label="Fermer la notification"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Decorative Elements --}}
        <div class="absolute -top-2 -right-2 w-20 h-20 bg-gradient-to-r {{ $config['colors'] }} rounded-full opacity-10 animate-pulse"></div>
        <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-gradient-to-r {{ $config['colors'] }} rounded-full opacity-5 animate-float"></div>
    </div>
</div>

{{-- Custom CSS --}}
@once
@push('styles')
<style>
.enterprise-notification {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.enterprise-notification .animate-float {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-10px) rotate(3deg); }
    66% { transform: translateY(5px) rotate(-2deg); }
}

.enterprise-notification .animate-pulse-glow {
    animation: pulse-glow 2s ease-in-out infinite;
}

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 0 40px rgba(59, 130, 246, 0.6);
        transform: scale(1.05);
    }
}
</style>
@endpush
@endonce

{{-- JavaScript Enhancement --}}
@once
@push('scripts')
<script>
// Notification Stack Management
class EnterpriseNotificationStack {
    constructor() {
        this.notifications = new Map();
        this.maxVisible = 5;
        this.spacing = 16;
    }

    add(notificationId) {
        const element = document.getElementById(notificationId);
        if (!element) return;

        this.notifications.set(notificationId, element);
        this.repositionAll();

        // Auto-remove after animation
        element.addEventListener('transitionend', () => {
            if (element.classList.contains('translate-x-full')) {
                this.notifications.delete(notificationId);
                this.repositionAll();
            }
        });
    }

    repositionAll() {
        const visibleNotifications = Array.from(this.notifications.values())
            .filter(el => !el.classList.contains('translate-x-full'))
            .slice(-this.maxVisible);

        visibleNotifications.forEach((notification, index) => {
            const offset = index * (notification.offsetHeight + this.spacing);
            notification.style.top = `${16 + offset}px`;
            notification.style.zIndex = 50 - index;
        });

        // Hide excess notifications
        const excessNotifications = Array.from(this.notifications.values())
            .slice(0, -this.maxVisible);

        excessNotifications.forEach(notification => {
            if (notification.__x) {
                notification.__x.dismiss();
            }
        });
    }
}

window.enterpriseNotificationStack = new EnterpriseNotificationStack();

// Auto-register new notifications
document.addEventListener('DOMContentLoaded', () => {
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.classList && node.classList.contains('enterprise-notification')) {
                    window.enterpriseNotificationStack.add(node.id);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Helper function to create notifications programmatically
window.showEnterpriseNotification = function(options) {
    const {
        type = 'info',
        title = '',
        message = '',
        timeout = 5000,
        dismissible = true,
        actions = []
    } = options;

    const notificationHtml = `
        <x-enterprise-notification
            type="${type}"
            title="${title}"
            message="${message}"
            :timeout="${timeout}"
            :dismissible="${dismissible}"
            :actions="${JSON.stringify(actions)}"
        />
    `;

    // This would need server-side rendering in real implementation
    console.log('Notification requested:', options);
};
</script>
@endpush
@endonce