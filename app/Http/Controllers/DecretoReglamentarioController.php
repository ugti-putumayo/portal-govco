<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DecretoReglamentario;

class DecretoReglamentarioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $decretos = DecretoReglamentario::when($search, function ($query, $search) {
            return $query->where('decreto_aplicable', 'like', "%{$search}%")
                         ->orWhere('objetivo', 'like', "%{$search}%")
                         ->orWhere('ambitos_regulados', 'like', "%{$search}%");
        })->paginate(10);

        return view('transparencia.subelements.decretoreglamentario', compact('decretos', 'search'));
    }
}
