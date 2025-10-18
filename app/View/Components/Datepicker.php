<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Datepicker extends Component
{
    public string $name;
    public ?string $label;
    public ?string $error;
    public ?string $helpText;
    public bool $required;
    public bool $disabled;
    public ?string $value;
    public ?string $minDate;
    public ?string $maxDate;
    public string $format;
    public ?string $placeholder;

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
        ?string $minDate = null,
        ?string $maxDate = null,
        string $format = 'd/m/Y',
        ?string $placeholder = 'Sélectionner une date'
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->error = $error;
        $this->helpText = $helpText;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->value = $value;
        $this->minDate = $minDate;
        $this->maxDate = $maxDate;
        $this->format = $format;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.datepicker');
    }

    /**
     * Get ID for input
     */
    public function getId(): string
    {
        return $this->name ?: 'datepicker-' . uniqid();
    }
}
