@props(['status', 'type' => 'default'])

@php
$statusConfig = [
 'repair' => [
 'en_attente' => ['label' => 'En attente', 'class' => 'bg-yellow-100 text-yellow-800'],
 'accord_initial' => ['label' => 'Accord initial', 'class' => 'bg-blue-100 text-blue-800'],
 'accordee' => ['label' => 'Accordée', 'class' => 'bg-green-100 text-green-800'],
 'refusee' => ['label' => 'Refusée', 'class' => 'bg-red-100 text-red-800'],
 'en_cours' => ['label' => 'En cours', 'class' => 'bg-indigo-100 text-indigo-800'],
 'terminee' => ['label' => 'Terminée', 'class' => 'bg-gray-100 text-gray-800'],
 'annulee' => ['label' => 'Annulée', 'class' => 'bg-gray-100 text-gray-800']
 ],
 'expense' => [
 'pending' => ['label' => 'En attente', 'class' => 'bg-yellow-100 text-yellow-800'],
 'approved' => ['label' => 'Approuvée', 'class' => 'bg-green-100 text-green-800'],
 'rejected' => ['label' => 'Rejetée', 'class' => 'bg-red-100 text-red-800'],
 'paid' => ['label' => 'Payée', 'class' => 'bg-blue-100 text-blue-800']
 ],
 'supplier' => [
 'active' => ['label' => 'Actif', 'class' => 'bg-green-100 text-green-800'],
 'inactive' => ['label' => 'Inactif', 'class' => 'bg-gray-100 text-gray-800'],
 'suspended' => ['label' => 'Suspendu', 'class' => 'bg-red-100 text-red-800']
 ],
 'default' => [
 'active' => ['label' => 'Actif', 'class' => 'bg-green-100 text-green-800'],
 'inactive' => ['label' => 'Inactif', 'class' => 'bg-gray-100 text-gray-800'],
 'pending' => ['label' => 'En attente', 'class' => 'bg-yellow-100 text-yellow-800']
 ]
];

$config = $statusConfig[$type] ?? $statusConfig['default'];
$statusInfo = $config[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $statusInfo['class']]) }}>
 {{ $statusInfo['label'] }}
</span>