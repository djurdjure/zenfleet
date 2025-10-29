@props(['field'])

@if(isset($errors) && $errors->has($field))
    <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
        <x-iconify icon="lucide:alert-circle" class="w-3 h-3" />
        {{ $errors->first($field) }}
    </p>
@endif
