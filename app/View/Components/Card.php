<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Card extends Component
{
    public ?string $title;
    public ?string $icon;
    public ?string $description;
    public string $padding;
    public string $margin;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $title = null,
        ?string $icon = null,
        ?string $description = null,
        string $padding = 'p-6',
        string $margin = 'mb-6'
    ) {
        $this->title = $title;
        $this->icon = $icon;
        $this->description = $description;
        $this->padding = $padding;
        $this->margin = $margin;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.card');
    }

    /**
     * Get card classes (Flowbite-inspired)
     */
    public function getClasses(): string
    {
        return "bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 {$this->padding} {$this->margin}";
    }
}
