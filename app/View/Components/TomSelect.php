<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TomSelect extends Component
{
    public string $name;
    public ?string $label;
    public ?string $error;
    public ?string $helpText;
    public bool $required;
    public bool $disabled;
    public $options;
    public $selected;
    public ?string $placeholder;
    public bool $multiple;
    public bool $clearable;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name = '',
        ?string $label = null,
        ?string $error = null,
        ?string $helpText = null,
        bool $required = false,
        bool $disabled = false,
        $options = [],
        $selected = null,
        ?string $placeholder = 'Rechercher...',
        bool $multiple = false,
        bool $clearable = true
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->error = $error;
        $this->helpText = $helpText;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->options = $options;
        $this->selected = $selected;
        $this->placeholder = $placeholder;
        $this->multiple = $multiple;
        $this->clearable = $clearable;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.tom-select');
    }

    /**
     * Get ID for select
     */
    public function getId(): string
    {
        return $this->name ?: 'tomselect-' . uniqid();
    }
}
