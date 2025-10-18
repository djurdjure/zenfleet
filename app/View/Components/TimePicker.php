<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TimePicker extends Component
{
    public string $name;
    public ?string $label;
    public ?string $error;
    public ?string $helpText;
    public bool $required;
    public bool $disabled;
    public ?string $value;
    public ?string $placeholder;
    public bool $enableSeconds;

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
        ?string $value = null,
        ?string $placeholder = 'Sélectionner une heure',
        bool $enableSeconds = false
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->error = $error;
        $this->helpText = $helpText;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->enableSeconds = $enableSeconds;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.time-picker');
    }

    /**
     * Get ID for input
     */
    public function getId(): string
    {
        return $this->name ?: 'timepicker-' . uniqid();
    }
}
