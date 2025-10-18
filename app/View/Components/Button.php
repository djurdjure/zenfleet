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
     * Get button classes based on variant and size (ZenFleet Blue Theme - Enterprise Grade)
     */
    public function getClasses(): string
    {
        $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed';

        $variantClasses = match($this->variant) {
            'primary' => 'text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 dark:active:bg-blue-800',
            'secondary' => 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 hover:text-blue-700 active:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:active:bg-gray-600',
            'danger' => 'text-white bg-red-600 hover:bg-red-700 active:bg-red-800 dark:bg-red-500 dark:hover:bg-red-600 dark:active:bg-red-700',
            'success' => 'text-white bg-green-600 hover:bg-green-700 active:bg-green-800 dark:bg-green-500 dark:hover:bg-green-600 dark:active:bg-green-700',
            'ghost' => 'text-gray-700 hover:bg-gray-100 active:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:active:bg-gray-600',
            default => 'text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 dark:active:bg-blue-800',
        };

        $sizeClasses = match($this->size) {
            'sm' => 'px-3 py-2 text-xs',
            'md' => 'px-5 py-2.5 text-sm',
            'lg' => 'px-6 py-3 text-base',
            default => 'px-5 py-2.5 text-sm',
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
