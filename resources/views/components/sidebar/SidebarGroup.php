<?php

namespace App\View\Components\Sidebar;

use Illuminate\View\Component;
use Illuminate\Support\Collection;

class SidebarGroup extends Component
{
    public string $title;
    public ?string $icon;
    public bool $active;
    public bool $collapsed;
    public ?string $permission;
    public Collection $routes;

    /**
     * Créer une nouvelle instance du composant
     */
    public function __construct(
        string $title = '',
        ?string $icon = null,
        bool $active = false,
        bool $collapsed = false,
        ?string $permission = null,
        array $routes = []
    ) {
        $this->title = $title;
        $this->icon = $icon;
        $this->active = $active;
        $this->collapsed = $collapsed;
        $this->permission = $permission;
        $this->routes = collect($routes);
    }

    /**
     * Déterminer si le groupe est actif basé sur la route actuelle
     */
    public function isActiveGroup(): bool
    {
        if ($this->active) {
            return true;
        }

        return $this->routes->contains(function ($route) {
            return request()->routeIs($route);
        });
    }

    /**
     * Obtenir les classes CSS du groupe
     */
    public function getGroupClasses(): string
    {
        $classes = ['sidebar-group'];
        
        if ($this->isActiveGroup()) {
            $classes[] = 'active';
        }
        
        if ($this->collapsed) {
            $classes[] = 'collapsed';
        }

        return implode(' ', $classes);
    }

    /**
     * Render du composant
     */
    public function render()
    {
        return view('components.sidebar.sidebar-group');
    }
}
