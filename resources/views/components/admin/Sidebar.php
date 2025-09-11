<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public bool $collapsed;

    /**
     * Create a new component instance.
     */
    public function __construct(bool $collapsed = false)
    {
        $this->collapsed = $collapsed;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.admin.sidebar');
    }
}
