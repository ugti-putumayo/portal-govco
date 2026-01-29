<?php

namespace App\View\Components\Administration\Association;

use Illuminate\View\Component;

class ModalCreateAssociation extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.administration.association.modal-create-association');
    }
}
