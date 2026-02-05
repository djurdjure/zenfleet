@props(['status', 'type' => 'default'])

@php
$statusConfig = [
 'repair' => [
 'en_attente' => ['label' => 'En attente', 'class' => 'bg-yellow-50 text-yellow-700 border border-yellow-200'],
 'accord_initial' => ['label' => 'Accord initial', 'class' => 'bg-blue-50 text-blue-700 border border-blue-200'],
 'accordee' => ['label' => 'Accordée', 'class' => 'bg-green-50 text-green-700 border border-green-200'],
 'refusee' => ['label' => 'Refusée', 'class' => 'bg-red-50 text-red-700 border border-red-200'],
 'en_cours' => ['label' => 'En cours', 'class' => 'bg-indigo-50 text-indigo-700 border border-indigo-200'],
 'terminee' => ['label' => 'Terminée', 'class' => 'bg-gray-50 text-gray-700 border border-gray-200'],
 'annulee' => ['label' => 'Annulée', 'class' => 'bg-gray-50 text-gray-700 border border-gray-200']
 ],
 'expense' => [
 'pending' => ['label' => 'En attente', 'class' => 'bg-yellow-50 text-yellow-700 border border-yellow-200'],
 'approved' => ['label' => 'Approuvée', 'class' => 'bg-green-50 text-green-700 border border-green-200'],
 'rejected' => ['label' => 'Rejetée', 'class' => 'bg-red-50 text-red-700 border border-red-200'],
 'paid' => ['label' => 'Payée', 'class' => 'bg-blue-50 text-blue-700 border border-blue-200']
 ],
 'supplier' => [
 'active' => ['label' => 'Actif', 'class' => 'bg-green-50 text-green-700 border border-green-200'],
 'inactive' => ['label' => 'Inactif', 'class' => 'bg-gray-50 text-gray-700 border border-gray-200'],
 'suspended' => ['label' => 'Suspendu', 'class' => 'bg-red-50 text-red-700 border border-red-200']
 ],
 'default' => [
 'active' => ['label' => 'Actif', 'class' => 'bg-green-50 text-green-700 border border-green-200'],
 'inactive' => ['label' => 'Inactif', 'class' => 'bg-gray-50 text-gray-700 border border-gray-200'],
 'pending' => ['label' => 'En attente', 'class' => 'bg-yellow-50 text-yellow-700 border border-yellow-200']
 ]
];

$config = $statusConfig[$type] ?? $statusConfig['default'];
$statusInfo = $config[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-50 text-gray-700 border border-gray-200'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $statusInfo['class']]) }}>
 {{ $statusInfo['label'] }}
</span>
