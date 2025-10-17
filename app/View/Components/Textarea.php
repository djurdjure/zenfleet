<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Textarea extends Component
{
    public string $name;
    public ?string $label;
    public ?string $placeholder;
    public ?string $error;
    public ?string $helpText;
    public bool $required;
    public bool $disabled;
    public ?string $value;
    public int $rows;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name = '',
        ?string $label = null,
        ?string $placeholder = null,
        ?string $error = null,
        ?string $helpText = null,
        bool $required = false,
        bool $disabled = false,
        ?string $value = null,
        int $rows = 4
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->error = $error;
        $this->helpText = $helpText;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->value = $value;
        $this->rows = $rows;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.textarea');
    }

    /**
     * Get textarea classes based on state (Flowbite-inspired)
     */
    public function getClasses(): string
    {
        $baseClasses = 'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500';

        if ($this->error) {
            return "$baseClasses border-red-300 focus:ring-red-500 focus:border-red-500 dark:border-red-600";
        }

        if ($this->disabled) {
            return "$baseClasses border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed opacity-60";
        }

        return "$baseClasses border-gray-300";
    }

    /**
     * Get ID for textarea
     */
    public function getId(): string
    {
        return $this->name ?: 'textarea-' . uniqid();
    }
}
