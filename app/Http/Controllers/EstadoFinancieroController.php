<?php
namespace App\Http\Controllers;

use App\Models\EstadoFinanciero;
use Illuminate\Support\Facades\DB;

class EstadoFinancieroController extends Controller
{
    public function index()
    {
        $years = EstadoFinanciero::select(DB::raw('YEAR(expedition_date) as year'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year');
        
        return view('transparencia.subelements.estados_financieros', compact('years'));
    }

    public function showByYear($year)
    {
        $registrosDelAno = EstadoFinanciero::whereYear('expedition_date', $year)
                                      ->orderBy('expedition_date', 'desc')
                                      ->get();

        $registrosAgrupados = $registrosDelAno->groupBy(function($item) {
            return $item->expedition_date->translatedFormat('F');
        });

        return view('transparencia.subelements.estados_financieros', [
            'registros' => $registrosAgrupados,
            'year' => $year
        ]);
    }
}