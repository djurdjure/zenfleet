<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Badge extends Component
{
    public string $type;
    public string $size;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $type = 'gray',
        string $size = 'md'
    ) {
        $this->type = $type;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.badge');
    }

    /**
     * Get badge classes based on type and size
     */
    public function getClasses(): string
    {
        $baseClasses = 'inline-flex items-center rounded-full font-medium';
        
        $typeClasses = match($this->type) {
            'success' => 'bg-green-100 text-green-800',
            'error' => 'bg-red-100 text-red-800',
            'warning' => 'bg-amber-100 text-amber-800',
            'info' => 'bg-cyan-100 text-cyan-800',
            'primary' => 'bg-blue-100 text-blue-800',
            'gray' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };

        $sizeClasses = match($this->size) {
            'sm' => 'px-2 py-0.5 text-xs',
            'md' => 'px-2.5 py-0.5 text-xs',
            'lg' => 'px-3 py-1 text-sm',
            default => 'px-2.5 py-0.5 text-xs',
        };

        return "$baseClasses $typeClasses $sizeClasses";
    }
}
