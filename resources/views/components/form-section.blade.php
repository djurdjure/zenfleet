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
        <div class="absolute left-[1.375rem] top-5 bottom-5 w-px rounded-full bg-gradient-to-b from-gray-200 via-gray-300/80 to-gray-200"></div>
    @endif

    <div class="relative pl-14">
        @if($icon)
            <div class="absolute left-0 top-4.5">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-gray-50 border border-gray-200 shadow-sm">
                    <x-iconify :icon="$icon" class="w-6 h-6 text-[#0c90ee]" />
                </span>
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 px-6 py-4 bg-slate-50/70 border-b border-slate-200">
                <div>
                    @if($eyebrow)
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $eyebrow }}</p>
                    @endif
                    <h3 class="text-m font-semibold text-slate-600">{{ $title }}</h3>
                    @if($subtitle)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $subtitle }}</p>
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
