{{-- Composant Toast Notification Enterprise Ultra-Moderne --}}
@props([
    'type' => 'info', // success, error, warning, info
    'title' => null,
    'message' => null,
    'duration' => 5000,
    'position' => 'top-right', // top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
    'closeable' => true,
    'progressBar' => true,
    'icon' => null,
    'actions' => null,
])

@php
$positionClasses = [
    'top-right' => 'top-4 right-4',
    'top-left' => 'top-4 left-4',
    'bottom-right' => 'bottom-4 right-4',
    'bottom-left' => 'bottom-4 left-4',
    'top-center' => 'top-4 left-1/2 -translate-x-1/2',
    'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2',
];

$typeStyles = [
    'success' => [
        'bg' => 'bg-gradient-to-r from-success-50 to-success-100/50',
        'border' => 'border-success-200',
        'icon-bg' => 'bg-success-100',
        'icon-color' => 'text-success-600',
        'title-color' => 'text-success-900',
        'message-color' => 'text-success-700',
        'progress-bg' => 'bg-success-200',
        'progress-fill' => 'bg-success-500',
    ],
    'error' => [
        'bg' => 'bg-gradient-to-r from-danger-50 to-danger-100/50',
        'border' => 'border-danger-200',
        'icon-bg' => 'bg-danger-100',
        'icon-color' => 'text-danger-600',
        'title-color' => 'text-danger-900',
        'message-color' => 'text-danger-700',
        'progress-bg' => 'bg-danger-200',
        'progress-fill' => 'bg-danger-500',
    ],
    'warning' => [
        'bg' => 'bg-gradient-to-r from-warning-50 to-warning-100/50',
        'border' => 'border-warning-200',
        'icon-bg' => 'bg-warning-100',
        'icon-color' => 'text-warning-600',
        'title-color' => 'text-warning-900',
        'message-color' => 'text-warning-700',
        'progress-bg' => 'bg-warning-200',
        'progress-fill' => 'bg-warning-500',
    ],
    'info' => [
        'bg' => 'bg-gradient-to-r from-info-50 to-info-100/50',
        'border' => 'border-info-200',
        'icon-bg' => 'bg-info-100',
        'icon-color' => 'text-info-600',
        'title-color' => 'text-info-900',
        'message-color' => 'text-info-700',
        'progress-bg' => 'bg-info-200',
        'progress-fill' => 'bg-info-500',
    ],
];

$style = $typeStyles[$type];

$defaultIcons = [
    'success' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    'error' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    'warning' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
    'info' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
];

$displayIcon = $icon ?? $defaultIcons[$type];
@endphp

<div
    x-data="{
        show: false,
        progress: 100,
        timer: null,
        duration: {{ $duration }},
        pauseOnHover: false,
        startTimer() {
            if (this.duration <= 0) return;
            
            const interval = 50;
            const decrement = (interval / this.duration) * 100;
            
            this.timer = setInterval(() => {
                if (!this.pauseOnHover) {
                    this.progress -= decrement;
                    if (this.progress <= 0) {
                        this.close();
                    }
                }
            }, interval);
        },
        close() {
            clearInterval(this.timer);
            this.show = false;
            setTimeout(() => {
                $el.remove();
            }, 300);
        },
        init() {
            this.show = true;
            this.startTimer();
        }
    }"
    x-init="init()"
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @mouseenter="pauseOnHover = true"
    @mouseleave="pauseOnHover = false"
    class="fixed {{ $positionClasses[$position] }} z-50 w-full max-w-sm"
>
    <div class="{{ $style['bg'] }} {{ $style['border'] }} backdrop-blur-sm border rounded-xl shadow-2xl overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                {{-- Icon --}}
                <div class="flex-shrink-0">
                    <div class="{{ $style['icon-bg'] }} {{ $style['icon-color'] }} w-10 h-10 rounded-lg flex items-center justify-center">
                        {!! $displayIcon !!}
                    </div>
                </div>
                
                {{-- Content --}}
                <div class="ml-3 flex-1">
                    @if($title)
                    <h3 class="text-sm font-semibold {{ $style['title-color'] }}">
                        {{ $title }}
                    </h3>
                    @endif
                    
                    @if($message)
                    <p class="mt-1 text-sm {{ $style['message-color'] }}">
                        {{ $message }}
                    </p>
                    @endif
                    
                    {{-- Actions --}}
                    @if($actions || isset($slot))
                    <div class="mt-3 flex items-center gap-2">
                        {{ $actions ?? $slot }}
                    </div>
                    @endif
                </div>
                
                {{-- Close button --}}
                @if($closeable)
                <div class="ml-4 flex-shrink-0">
                    <button
                        @click="close()"
                        class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-white/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @endif
            </div>
        </div>
        
        {{-- Progress bar --}}
        @if($progressBar && $duration > 0)
        <div class="h-1 {{ $style['progress-bg'] }} relative overflow-hidden">
            <div 
                class="h-full {{ $style['progress-fill'] }} transition-all duration-50 ease-linear"
                :style="`width: ${progress}%`"
            ></div>
        </div>
        @endif
    </div>
</div>

{{-- Script pour gérer les notifications globales --}}
@once
@push('scripts')
<script>
window.Toast = {
    success(message, title = 'Succès', options = {}) {
        this.show('success', message, title, options);
    },
    
    error(message, title = 'Erreur', options = {}) {
        this.show('error', message, title, options);
    },
    
    warning(message, title = 'Attention', options = {}) {
        this.show('warning', message, title, options);
    },
    
    info(message, title = 'Information', options = {}) {
        this.show('info', message, title, options);
    },
    
    show(type, message, title, options = {}) {
        const container = document.getElementById('toast-container') || this.createContainer();
        
        const toast = document.createElement('div');
        toast.innerHTML = `
            <x-enterprise.toast 
                type="${type}" 
                title="${title}" 
                message="${message}"
                duration="${options.duration || 5000}"
                position="${options.position || 'top-right'}"
            />
        `;
        
        container.appendChild(toast.firstElementChild);
    },
    
    createContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed inset-0 pointer-events-none z-50';
        container.innerHTML = '<div class="relative h-full"><div id="toast-stack" class="space-y-2"></div></div>';
        document.body.appendChild(container);
        return container;
    }
};

// Écouter les événements Livewire
document.addEventListener('DOMContentLoaded', () => {
    Livewire.on('notify', (data) => {
        Toast[data.type](data.message, data.title, data.options || {});
    });
});
</script>
@endpush
@endonce
