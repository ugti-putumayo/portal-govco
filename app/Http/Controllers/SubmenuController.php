<?php

namespace App\Http\Controllers;

use App\Models\Submenu;
use Illuminate\Http\Request;

class SubmenuController extends Controller
{
    public function show($id)
    {
        $submenu = Submenu::with('menu', 'subsubmenus')->findOrFail($id);

        $breadcrumbItems = [
            ['url' => route('home'), 'label' => 'Inicio'],
            ['url' => route('menu.show', $submenu->menu->id), 'label' => $submenu->menu->name],
            ['url' => route('submenu.show', $submenu->id), 'label' => $submenu->name],
        ];

        return view('submenu.show', compact('submenu', 'breadcrumbItems'));
    }
}