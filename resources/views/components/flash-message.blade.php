@props(['type' => 'success', 'message'])

@php
 $isVisible = $message ? true : false;
 $bgColor = [
 'success' => 'bg-green-100 border-green-500',
 'error' => 'bg-red-100 border-red-500',
 'warning' => 'bg-yellow-100 border-yellow-500',
 'info' => 'bg-blue-100 border-blue-500',
 ][$type];
 $textColor = [
 'success' => 'text-green-700',
 'error' => 'text-red-700',
 'warning' => 'text-yellow-700',
 'info' => 'text-blue-700',
 ][$type];
@endphp

<div x-data="{ show: @json($isVisible) }"
 x-show="show"
 x-transition
 x-init="setTimeout(() => show = false, 5000)"
 {{ $attributes->merge(['class' => 'mb-4 border-l-4 p-4 ' . $bgColor . ' ' . $textColor]) }}
 role="alert">
 <p class="font-bold">{{ $message }}</p>
</div>
