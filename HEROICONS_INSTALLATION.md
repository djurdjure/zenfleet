# 🎨 INSTALLATION HEROICONS

## Commande à exécuter

```bash
composer require blade-ui-kit/blade-heroicons
```

## Configuration

Après installation, les icônes seront disponibles via:

```blade
{{-- Outline (ligne fine) --}}
<x-heroicon-o-check class="w-5 h-5 text-green-600" />
<x-heroicon-o-x-mark class="w-5 h-5 text-red-600" />
<x-heroicon-o-truck class="w-6 h-6 text-blue-600" />

{{-- Solid (rempli) --}}
<x-heroicon-s-check-circle class="w-5 h-5 text-green-600" />
<x-heroicon-s-exclamation-triangle class="w-5 h-5 text-amber-600" />

{{-- Mini (20x20, compact) --}}
<x-heroicon-m-plus class="w-4 h-4" />
```

## Vérification

Pour vérifier que l'installation a réussi:

```bash
php artisan vendor:publish --tag=blade-heroicons
```

✅ Les icônes seront disponibles immédiatement après `composer require`
