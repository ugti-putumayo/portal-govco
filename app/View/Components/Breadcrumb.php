<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $breadcrumbItems;

    /**
     * Create a new component instance.
     *
     * @param array $breadcrumbItems
     */
    public function __construct($breadcrumbItems = [])
    {
        // Pasar las rutas dinámicas a la vista
        $this->breadcrumbItems = $breadcrumbItems;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('components.breadcrumb');
    }
}