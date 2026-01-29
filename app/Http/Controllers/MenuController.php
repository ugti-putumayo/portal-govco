<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        // Cargar menús con sus submenús y sub-submenús
        $menus = Menu::with(['submenus.subsubmenus'])->orderBy('order')->get();
        dd($menus);

        // Pasar los menús a la vista
        return view('navbar');
    }

    public function show($id)
    {
        // Obtener el menú específico
        $menu = Menu::with(['submenus.subsubmenus'])->findOrFail($id);

        // Cargar breadcrumb
        $breadcrumbItems = [
            ['url' => route('home'), 'label' => 'Inicio'],
            ['url' => route('menu.show', $menu->id), 'label' => $menu->name],
        ];

        return view('menu.show', compact('menu', 'breadcrumbItems'));
    }
}
