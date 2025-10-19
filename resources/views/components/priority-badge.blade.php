@props(['priority'])

@php
$priorityConfig = [
 'low' => ['label' => 'Basse', 'class' => 'bg-green-100 text-green-800'],
 'medium' => ['label' => 'Moyenne', 'class' => 'bg-yellow-100 text-yellow-800'],
 'high' => ['label' => 'Haute', 'class' => 'bg-orange-100 text-orange-800'],
 'urgent' => ['label' => 'Urgente', 'class' => 'bg-red-100 text-red-800']
];

$priorityInfo = $priorityConfig[$priority] ?? ['label' => ucfirst($priority), 'class' => 'bg-gray-100 text-gray-800'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $priorityInfo['class']]) }}>
 {{ $priorityInfo['label'] }}
</span>