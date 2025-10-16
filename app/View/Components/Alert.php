<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Alert extends Component
{
    public string $type;
    public ?string $title;
    public bool $dismissible;
    public bool $showIcon;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $type = 'info',
        ?string $title = null,
        bool $dismissible = false,
        bool $showIcon = true
    ) {
        $this->type = $type;
        $this->title = $title;
        $this->dismissible = $dismissible;
        $this->showIcon = $showIcon;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.alert');
    }

    /**
     * Get alert classes based on type
     */
    public function getClasses(): string
    {
        $baseClasses = 'rounded-r-lg border-l-4 p-4';
        
        return match($this->type) {
            'success' => "$baseClasses bg-green-50 border-green-500",
            'error' => "$baseClasses bg-red-50 border-red-500",
            'warning' => "$baseClasses bg-amber-50 border-amber-500",
            'info' => "$baseClasses bg-cyan-50 border-cyan-500",
            default => "$baseClasses bg-cyan-50 border-cyan-500",
        };
    }

    /**
     * Get icon name based on type
     */
    public function getIcon(): string
    {
        return match($this->type) {
            'success' => 'check-circle',
            'error' => 'x-circle',
            'warning' => 'exclamation-triangle',
            'info' => 'information-circle',
            default => 'information-circle',
        };
    }

    /**
     * Get icon color based on type
     */
    public function getIconColor(): string
    {
        return match($this->type) {
            'success' => 'text-green-600',
            'error' => 'text-red-600',
            'warning' => 'text-amber-600',
            'info' => 'text-cyan-600',
            default => 'text-cyan-600',
        };
    }

    /**
     * Get title color based on type
     */
    public function getTitleColor(): string
    {
        return match($this->type) {
            'success' => 'text-green-800',
            'error' => 'text-red-800',
            'warning' => 'text-amber-800',
            'info' => 'text-cyan-800',
            default => 'text-cyan-800',
        };
    }

    /**
     * Get text color based on type
     */
    public function getTextColor(): string
    {
        return match($this->type) {
            'success' => 'text-green-700',
            'error' => 'text-red-700',
            'warning' => 'text-amber-700',
            'info' => 'text-cyan-700',
            default => 'text-cyan-700',
        };
    }
}
