<?php

namespace App\View\Components;

use Illuminate\View\Component;

class News extends Component
{
    public $publications;

    /**
     * Crear una nueva instancia del componente.
     *
     * @return void
     */
    public function __construct($publications)
    {
        $this->publications = $publications;
    }

    /**
     * Obtener la vista o el contenido que representa el componente.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.news');
    }
}