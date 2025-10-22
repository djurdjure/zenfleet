@props([
    'headers' => [],
    'zebraStripe' => true,
])

{{-- ====================================================================
 📋 DATA TABLE COMPONENT - ENTERPRISE-GRADE TABLE
 ====================================================================
 
 @usage
 <x-data-table 
     :headers="[
         ['label' => 'Chauffeur', 'icon' => 'heroicons:user'],
         ['label' => 'Téléphone', 'icon' => 'heroicons:phone'],
         ['label' => 'Statut', 'icon' => 'heroicons:signal'],
         ['label' => 'Actions', 'align' => 'center']
     ]"
     zebraStripe>
     <tr>
         <td class="px-6 py-4">John Doe</td>
         <td class="px-6 py-4">+1234567890</td>
         <td class="px-6 py-4"><span class="badge">Actif</span></td>
         <td class="px-6 py-4">...</td>
     </tr>
 </x-data-table>
 
 @version 1.0-Enterprise
 ==================================================================== --}}

<div class="zenfleet-card p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" 
                            class="px-6 py-4 text-{{ $header['align'] ?? 'left' }} text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2 {{ ($header['align'] ?? 'left') === 'center' ? 'justify-center' : (($header['align'] ?? 'left') === 'right' ? 'justify-end' : '') }}">
                                @if(isset($header['icon']))
                                    <x-iconify :icon="$header['icon']" class="w-4 h-4 text-gray-500" />
                                @endif
                                {{ $header['label'] }}
                                @if(isset($header['sortable']) && $header['sortable'])
                                    <x-iconify icon="heroicons:chevron-up-down" class="w-4 h-4 text-gray-400" />
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
    tbody tr {
        transition: all 0.2s ease-in-out;
    }
    
    @if($zebraStripe)
    tbody tr:nth-child(even) {
        background-color: rgba(249, 250, 251, 0.5);
    }
    @endif
    
    tbody tr:hover {
        background: linear-gradient(135deg, rgb(248, 250, 252) 0%, rgb(241, 245, 249) 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
</style>
@endpush
