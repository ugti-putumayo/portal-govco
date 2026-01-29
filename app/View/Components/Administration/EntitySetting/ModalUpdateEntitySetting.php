<?php

namespace App\View\Components\Administration\EntitySetting;

use Illuminate\View\Component;

class ModalUpdateEntitySetting extends Component
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
        return view('components.administration.entity-setting.modal-update-entity-setting');
    }
}
