<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\Subsubmenu;

class BreadcrumbComposer
{
    public function compose(View $view)
    {
        // Iniciar el breadcrumb con el enlace a Inicio
        $breadcrumbItems = [
            ['url' => route('home'), 'label' => 'Inicio'],
        ];

        // Agregar breadcrumb para Menús
        if (request()->routeIs('menu.show')) {
            $menu = Menu::find(request()->route('id'));
            if ($menu) {
                $breadcrumbItems[] = ['url' => route('menu.show', $menu->id), 'label' => $menu->name];
            }
        }

        // Agregar breadcrumb para Submenús
        if (request()->routeIs('submenu.show')) {
            $submenu = Submenu::find(request()->route('id'));
            if ($submenu && $submenu->menu) {
                $breadcrumbItems[] = ['url' => route('menu.show', $submenu->menu->id), 'label' => $submenu->menu->name];
                $breadcrumbItems[] = ['url' => route('submenu.show', $submenu->id), 'label' => $submenu->name];
            }
        }

        // Agregar breadcrumb para Sub-submenús
        if (request()->routeIs('subsubmenu.show')) {
            $subsubmenu = Subsubmenu::find(request()->route('id'));
            if ($subsubmenu && $subsubmenu->submenu) {
                $breadcrumbItems[] = ['url' => route('menu.show', $subsubmenu->submenu->menu->id), 'label' => $subsubmenu->submenu->menu->name];
                $breadcrumbItems[] = ['url' => route('submenu.show', $subsubmenu->submenu->id), 'label' => $subsubmenu->submenu->name];
                $breadcrumbItems[] = ['url' => route('subsubmenu.show', $subsubmenu->id), 'label' => $subsubmenu->name];
            }
        }

        // Compartir el breadcrumb con la vista
        $view->with('breadcrumbItems', $breadcrumbItems);
    }
}