<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Button extends Component
{
    public string $variant;
    public string $size;
    public ?string $icon;
    public string $iconPosition;
    public ?string $href;
    public string $type;
    public bool $disabled;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $variant = 'primary',
        string $size = 'md',
        ?string $icon = null,
        string $iconPosition = 'left',
        ?string $href = null,
        string $type = 'button',
        bool $disabled = false
    ) {
        $this->variant = $variant;
        $this->size = $size;
        $this->icon = $icon;
        $this->iconPosition = $iconPosition;
        $this->href = $href;
        $this->type = $type;
        $this->disabled = $disabled;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.button');
    }

    /**
     * Get button classes based on variant and size
     */
    public function getClasses(): string
    {
        $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed';

        $variantClasses = match($this->variant) {
            'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500/50',
            'secondary' => 'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-500/50',
            'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500/50',
            'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500/50',
            'ghost' => 'text-gray-700 hover:bg-gray-100 focus:ring-gray-500/50',
            default => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500/50',
        };

        $sizeClasses = match($this->size) {
            'sm' => 'px-3 py-1.5 text-sm',
            'md' => 'px-4 py-2 text-base',
            'lg' => 'px-6 py-3 text-lg',
            default => 'px-4 py-2 text-base',
        };

        return "$baseClasses $variantClasses $sizeClasses";
    }

    /**
     * Get icon size based on button size
     */
    public function getIconSize(): string
    {
        return match($this->size) {
            'sm' => 'w-4 h-4',
            'md' => 'w-5 h-5',
            'lg' => 'w-6 h-6',
            default => 'w-5 h-5',
        };
    }
}
