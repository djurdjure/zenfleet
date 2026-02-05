@props(['priority'])

@php
$priorityConfig = [
 'low' => ['label' => 'Basse', 'class' => 'bg-green-50 text-green-700 border border-green-200'],
 'medium' => ['label' => 'Moyenne', 'class' => 'bg-yellow-50 text-yellow-700 border border-yellow-200'],
 'high' => ['label' => 'Haute', 'class' => 'bg-orange-50 text-orange-700 border border-orange-200'],
 'urgent' => ['label' => 'Urgente', 'class' => 'bg-red-50 text-red-700 border border-red-200']
];

$priorityInfo = $priorityConfig[$priority] ?? ['label' => ucfirst($priority), 'class' => 'bg-gray-50 text-gray-700 border border-gray-200'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $priorityInfo['class']]) }}>
 {{ $priorityInfo['label'] }}
</span>