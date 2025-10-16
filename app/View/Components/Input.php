<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Input extends Component
{
    public string $type;
    public string $name;
    public ?string $label;
    public ?string $placeholder;
    public ?string $error;
    public ?string $helpText;
    public ?string $icon;
    public bool $required;
    public bool $disabled;
    public ?string $value;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $type = 'text',
        string $name = '',
        ?string $label = null,
        ?string $placeholder = null,
        ?string $error = null,
        ?string $helpText = null,
        ?string $icon = null,
        bool $required = false,
        bool $disabled = false,
        ?string $value = null
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->error = $error;
        $this->helpText = $helpText;
        $this->icon = $icon;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.input');
    }

    /**
     * Get input classes based on state
     */
    public function getClasses(): string
    {
        $baseClasses = 'block w-full rounded-lg border px-4 py-2 text-sm transition-all duration-200 focus:outline-none focus:ring-4';
        
        if ($this->error) {
            return "$baseClasses border-red-500 focus:border-red-500 focus:ring-red-500/50";
        }
        
        if ($this->disabled) {
            return "$baseClasses border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed";
        }
        
        return "$baseClasses border-gray-300 focus:border-blue-500 focus:ring-blue-500/50";
    }

    /**
     * Get ID for input
     */
    public function getId(): string
    {
        return $this->name ?: 'input-' . uniqid();
    }
}
