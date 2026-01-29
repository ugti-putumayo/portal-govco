<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transparency;

class SidebarController extends Controller
{
    public function index()
    {
        $secciones = Transparency::where('tipo', 'seccion')->orderBy('orden')->get();
        foreach ($secciones as $seccion) {
            $seccion->subElementos = Transparency::where('tipo', 'subelemento')
                ->where('id_padre', $seccion->id)
                ->orderBy('orden')
                ->get();
        }

        return view('partials.sidebar', compact('secciones'));
    }
}