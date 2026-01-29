<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Regulation;

class RegulationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Consultar los datos de la tabla 'regulations' con búsqueda y paginación
        $regulations = Regulation::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('tipo', 'like', "%{$search}%")
                         ->orWhere('theme', 'like', "%{$search}%");
        })->paginate(10);

        return view('regulations', compact('regulations', 'search'));
    }
}
