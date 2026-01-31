@props([
    'title',
    'icon' => null,
    'subtitle' => null,
    'contentClass' => '',
    'showLine' => true,
    'eyebrow' => null
])

<div {{ $attributes->merge(['class' => 'relative']) }}>
    @if($showLine)
        <div class="absolute left-5 top-6 bottom-6 w-px bg-slate-200/80"></div>
    @endif

    <div class="relative pl-12">
        @if($icon)
            <div class="absolute left-1.5 top-6">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-blue-600 shadow-sm ring-2 ring-blue-100">
                    <x-iconify :icon="$icon" class="w-5 h-5" />
                </span>
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 px-6 py-4 bg-slate-50/70 border-b border-slate-200">
                <div>
                    @if($eyebrow)
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $eyebrow }}</p>
                    @endif
                    <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
                    @if($subtitle)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
                {{ $actions ?? '' }}
            </div>

            <div class="p-6 {{ $contentClass }}">
                {{ $slot }}
            </div>
        </section>
    </div>
</div>
