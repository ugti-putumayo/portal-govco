<?php

namespace App\Http\Controllers;

use App\Models\Subsubmenu;
use Illuminate\Http\Request;

class SubsubmenuController extends Controller
{
    public function show($id)
    {
        $subsubmenu = Subsubmenu::with('submenu.menu')->findOrFail($id);

        $breadcrumbItems = [
            ['url' => route('home'), 'label' => 'Inicio'],
            ['url' => route('menu.show', $subsubmenu->submenu->menu->id), 'label' => $subsubmenu->submenu->menu->name],
            ['url' => route('submenu.show', $subsubmenu->submenu->id), 'label' => $subsubmenu->submenu->name],
            ['url' => route('subsubmenu.show', $subsubmenu->id), 'label' => $subsubmenu->name],
        ];

        return view('subsubmenu.show', compact('subsubmenu', 'breadcrumbItems'));
    }
}