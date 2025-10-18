<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Stepper extends Component
{
    public array $steps;
    public string $currentStepVar;

    /**
     * Create a new component instance.
     *
     * @param array $steps Array of steps with keys: 'label', 'icon'
     * @param string $currentStepVar Alpine.js variable name for current step (default: 'currentStep')
     */
    public function __construct(
        array $steps = [],
        string $currentStepVar = 'currentStep'
    ) {
        $this->steps = $steps;
        $this->currentStepVar = $currentStepVar;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.stepper');
    }
}
