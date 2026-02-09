@if($errors->any())
<div class="mb-6 rounded-xl border border-red-200 bg-red-50/70 p-5 shadow-sm" data-form-error-summary>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full border border-red-200 bg-white">
                <x-iconify icon="lucide:alert-triangle" class="h-5 w-5 text-red-600" />
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-semibold text-red-900">
                        {{ $errors->count() === 1 ? 'Une erreur détectée' : $errors->count() . ' erreurs détectées' }}
                    </h3>
                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">
                        {{ $errors->count() }}
                    </span>
                </div>
                <p class="text-sm text-red-700">
                    Corrigez les champs signalés ci-dessous avant d'enregistrer.
                </p>
            </div>
        </div>

        <button
            type="button"
            onclick="scrollToFirstError()"
            class="inline-flex items-center justify-center gap-2 rounded-md border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
            <x-iconify icon="lucide:search" class="h-4 w-4" />
            Aller à la première erreur
        </button>
    </div>

    <div class="mt-4 space-y-2">
        @foreach($errors->getBag('default')->toArray() as $field => $fieldErrors)
        @php
            $displayField = preg_replace('/\\.\\d+$/', '', $field);
        @endphp
        <div class="flex items-start gap-2 text-sm">
            <x-iconify icon="lucide:arrow-right" class="mt-0.5 h-4 w-4 text-red-400" />
            <div>
                <span class="font-medium text-red-900 capitalize">{{ str_replace('_', ' ', $displayField) }}:</span>
                @foreach($fieldErrors as $error)
                <span class="text-red-700">{{ $error }}</span>
                @if(!$loop->last), @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    function scrollToFirstError() {
        const firstErrorField = document.querySelector('[aria-invalid="true"], .zenfleet-invalid');
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (typeof firstErrorField.focus === 'function') {
                firstErrorField.focus({ preventScroll: true });
            }
        }
    }

    (function() {
        const errorMap = @json($errors->messages());
        const errorKeys = errorMap ? Object.keys(errorMap) : [];
        if (!errorKeys.length) {
            return;
        }

        const cssEscape = (value) => {
            if (window.CSS && typeof window.CSS.escape === 'function') {
                return window.CSS.escape(value);
            }
            return String(value).replace(/"/g, '\\"');
        };

        const resolveVisualElement = (element) => {
            if (!element) return element;

            if (element.classList && element.classList.contains('slimselect-field')) {
                const slimWrapper = element.parentElement ? element.parentElement.querySelector('.ss-main') : null;
                return slimWrapper || element;
            }

            if (element.type === 'checkbox' && typeof element.name === 'string' && element.name.endsWith('[]')) {
                const alpineContainer = element.closest('[x-data]');
                const triggerButton = alpineContainer ? alpineContainer.querySelector('button[type="button"]') : null;
                return triggerButton || element;
            }

            if (element.tagName === 'INPUT' && element.type === 'hidden') {
                const wrapper = element.parentElement;
                const displayInput = wrapper ? wrapper.querySelector('input[type="text"]') : null;
                return displayInput || element;
            }

            if (element.classList.contains('tomselect') && element.tomselect && element.tomselect.wrapper) {
                return element.tomselect.wrapper;
            }

            const next = element.nextElementSibling;
            if (next && next.classList && next.classList.contains('ts-wrapper')) {
                return next;
            }

            return element;
        };

        const applyErrorStyles = () => {
            const processed = new Set();

            errorKeys.forEach((field) => {
                const baseField = field.split('.')[0];
                const fieldVariants = new Set([field, baseField]);

                fieldVariants.forEach((variant) => {
                    const escaped = cssEscape(variant);
                    const selectors = [
                        `[name="${escaped}"]`,
                        `[name="${escaped}[]"]`,
                        `[name^="${escaped}["]`
                    ];

                    document.querySelectorAll(selectors.join(','))
                        .forEach((element) => {
                            if (processed.has(element)) return;
                            processed.add(element);

                            element.setAttribute('aria-invalid', 'true');
                            const visual = resolveVisualElement(element);
                            if (visual) {
                                visual.classList.add('zenfleet-invalid');
                                visual.setAttribute('aria-invalid', 'true');
                            }
                        });
                });
            });
        };

        const dispatchValidationToast = () => {
            const messages = Object.values(errorMap || {}).flat();
            if (!messages.length) return;

            const preview = messages.slice(0, 3).join(' • ');
            const suffix = messages.length > 3 ? ` (+${messages.length - 3} autres)` : '';

            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: 'error',
                    title: 'Erreurs de validation',
                    message: `${preview}${suffix}`
                }
            }));
        };

        const run = () => {
            applyErrorStyles();
            dispatchValidationToast();
            setTimeout(scrollToFirstError, 400);
        };

        document.addEventListener('DOMContentLoaded', run);
        document.addEventListener('livewire:navigated', run);
    })();
</script>
@endif
