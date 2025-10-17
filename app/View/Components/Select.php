<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Select extends Component
{
    public string $name;
    public ?string $label;
    public ?string $error;
    public ?string $helpText;
    public bool $required;
    public bool $disabled;
    public $options;
    public $selected;

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
        $selected = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->error = $error;
        $this->helpText = $helpText;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->options = $options;
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.select');
    }

    /**
     * Get select classes based on state (Flowbite-inspired)
     */
    public function getClasses(): string
    {
        $baseClasses = 'bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white';

        if ($this->error) {
            return "$baseClasses border-red-300 focus:ring-red-500 focus:border-red-500 dark:border-red-600";
        }

        if ($this->disabled) {
            return "$baseClasses border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed opacity-60";
        }

        return "$baseClasses border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-500 dark:focus:border-primary-500";
    }

    /**
     * Get ID for select
     */
    public function getId(): string
    {
        return $this->name ?: 'select-' . uniqid();
    }
}
