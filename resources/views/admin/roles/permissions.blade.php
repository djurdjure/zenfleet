<x-app-layout>
 <x-slot name="header">
 <h2 class="font-semibold text-xl text-gray-800 leading-tight">
 {{ __('Matrice des Permissions') }}
 </h2>
 </x-slot>

 <div class="py-12">
 <div class="max-w-full mx-auto sm:px-6 lg:px-8">
 @livewire('admin.permission-matrix')
 </div>
 </div>
</x-app-layout>
