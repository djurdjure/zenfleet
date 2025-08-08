<div
    x-data="{
        show: false,
        type: 'success',
        message: '',
        description: '',
        timer: null,
        init() {
            window.addEventListener('toast', event => {
                this.type = event.detail.type || 'success';
                this.message = event.detail.message || 'Action réussie.';
                this.description = event.detail.description || '';
                this.show = true;
                clearTimeout(this.timer);
                this.timer = setTimeout(() => {
                    this.show = false;
                }, 5000);
            });
        },
        typeClasses() {
            switch (this.type) {
                case 'success': return 'bg-green-50 border-green-400 text-green-700';
                case 'error': return 'bg-red-50 border-red-400 text-red-700';
                case 'warning': return 'bg-yellow-50 border-yellow-400 text-yellow-700';
                default: return 'bg-blue-50 border-blue-400 text-blue-700';
            }
        },
        iconClasses() {
            switch (this.type) {
                case 'success': return 'text-green-500';
                case 'error': return 'text-red-500';
                case 'warning': return 'text-yellow-500';
                default: return 'text-blue-500';
            }
        }
    }"
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed top-5 right-5 z-[100] w-full max-w-sm"
    style="display: none;"
>
    <div class="rounded-lg shadow-lg border-l-4 p-4" :class="typeClasses()">
        <div class="flex items-start">
            <div class="flex-shrink-0" :class="iconClasses()">
                <template x-if="type === 'success'"><x-heroicon-s-check-circle class="h-6 w-6" /></template>
                <template x-if="type === 'error'"><x-heroicon-s-x-circle class="h-6 w-6" /></template>
                <template x-if="type === 'warning'"><x-heroicon-s-exclamation-triangle class="h-6 w-6" /></template>
                <template x-if="type === 'info'"><x-heroicon-s-information-circle class="h-6 w-6" /></template>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-bold" x-text="message"></p>
                <p class="mt-1 text-sm" x-show="description" x-text="description"></p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button @click="show = false" class="inline-flex rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <x-heroicon-s-x-mark class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</div>