<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Iconify extends Component
{
    public string $icon;
    public bool $inline;

    /**
     * Create a new component instance.
     *
     * @param string $icon Nom de l'icône Iconify (ex: "heroicons:truck")
     * @param bool $inline Mode inline (true) ou block (false)
     */
    public function __construct(
        string $icon = '',
        bool $inline = false
    ) {
        $this->icon = $icon;
        $this->inline = $inline;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.iconify');
    }
}
