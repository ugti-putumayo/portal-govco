<?php

namespace App\Http\Controllers;

use App\Models\Presupuestal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresupuestalController extends Controller
{
    public function index()
    {
        $years = Presupuestal::select(DB::raw('YEAR(expedition_date) as year'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year');
        
        return view('transparencia.subelements.presupuestal', compact('years'));
    }

    public function showByYear($year)
    {
        // Obtiene las ejecuciones del año
        $ejecucionesDelAno = Presupuestal::whereYear('expedition_date', $year)
                                      ->orderBy('expedition_date', 'desc')
                                      ->get();

        // Agrupa la colección por el nombre del mes en español
        $ejecucionesAgrupadas = $ejecucionesDelAno->groupBy(function($item) {
            return $item->expedition_date->translatedFormat('F');
        });

        // Envía los datos ya agrupados a la vista
        return view('transparencia.subelements.presupuestal', [
            'ejecuciones' => $ejecucionesAgrupadas,
            'year' => $year
        ]);
    }
}