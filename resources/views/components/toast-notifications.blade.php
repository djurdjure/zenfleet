{{-- 🔔 TOAST NOTIFICATIONS SYSTEM - Enterprise Ultra-Pro --}}
<div x-data="toastNotifications()" 
     @toast.window="addToast($event.detail)"
     class="fixed top-4 right-4 z-[9999] space-y-3 pointer-events-none">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             @click="removeToast(toast.id)"
             class="pointer-events-auto cursor-pointer max-w-sm w-full bg-white shadow-2xl rounded-xl overflow-hidden 
                    ring-1 ring-black ring-opacity-5 hover:shadow-3xl transition-shadow duration-200">
            
            <div class="flex items-start p-4">
                {{-- Icon selon le type --}}
                <div class="flex-shrink-0">
                    <template x-if="toast.type === 'success'">
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </template>
                    
                    <template x-if="toast.type === 'error'">
                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </template>
                    
                    <template x-if="toast.type === 'warning'">
                        <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center">
                            <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </template>
                    
                    <template x-if="toast.type === 'info'">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </template>
                </div>
                
                {{-- Content --}}
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-semibold text-gray-900" x-text="toast.title || 'Notification'"></p>
                    <p class="mt-1 text-sm text-gray-600" x-text="toast.message"></p>
                </div>
                
                {{-- Close button --}}
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click.stop="removeToast(toast.id)"
                            class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none 
                                   focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" 
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" 
                                  clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            
            {{-- Progress bar --}}
            <div class="h-1 bg-gray-100">
                <div class="h-full transition-all duration-100 ease-linear"
                     :class="{
                         'bg-green-500': toast.type === 'success',
                         'bg-red-500': toast.type === 'error',
                         'bg-amber-500': toast.type === 'warning',
                         'bg-blue-500': toast.type === 'info'
                     }"
                     :style="`width: ${toast.progress}%`">
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function toastNotifications() {
    return {
        toasts: [],
        nextId: 1,
        
        addToast(config) {
            const toast = {
                id: this.nextId++,
                type: config.type || 'info',
                title: config.title || '',
                message: config.message || '',
                duration: config.duration || 4000,
                visible: false,
                progress: 100
            };
            
            this.toasts.push(toast);
            
            // Show toast after a small delay for animation
            setTimeout(() => {
                const index = this.toasts.findIndex(t => t.id === toast.id);
                if (index !== -1) {
                    this.toasts[index].visible = true;
                    this.startProgressBar(toast.id, toast.duration);
                }
            }, 10);
            
            // Auto remove after duration
            setTimeout(() => {
                this.removeToast(toast.id);
            }, toast.duration);
        },
        
        startProgressBar(id, duration) {
            const steps = 100;
            const interval = duration / steps;
            let currentStep = steps;
            
            const timer = setInterval(() => {
                currentStep--;
                const index = this.toasts.findIndex(t => t.id === id);
                
                if (index === -1 || currentStep <= 0) {
                    clearInterval(timer);
                    return;
                }
                
                this.toasts[index].progress = (currentStep / steps) * 100;
            }, interval);
        },
        
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    }
}
</script>
