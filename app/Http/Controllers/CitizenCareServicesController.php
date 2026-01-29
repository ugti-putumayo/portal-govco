<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\EntityService;
use App\Models\Publication;
use App\Services\GesdocApi;
use App\Models\ContentPage;
use App\Models\DataProtection;

class CitizenCareServicesController extends Controller
{
    public function indexPublicJudicialNotices(Request $request)
    {
        $search = $request->input('search');
        $query = Publication::activeOfType(5);
        $query->when($search, function ($q) use ($search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('title', 'LIKE', "%{$search}%")
                     ->orWhere('description', 'LIKE', "%{$search}%");
            });
        });

        $judicial_notices = $query->orderBy('date', 'desc')
            ->paginate(12)
            ->appends($request->input());

        return view('public.citizen-care-services.notifications-orders-edicts', compact('judicial_notices'));
    }

    public function indexPublicEntityServices(Request $request)
    {
        $services = EntityService::where('status', 1)
                ->orderBy('order_index', 'asc')
                ->get();
        return view('public.citizen-care-services.services-entity', compact('services'));
    }

    // PQRDS Reports
    public function indexPublicPqrdsReport(Request $request, GesdocApi $gesdoc)
    {
        if (session('error')) {
            return view('public.citizen-care-services.pqrds-reports', [
                'year'            => $request->input('year', date('Y')),
                'trimester'       => $request->input('trimester', $this->getCurrentTrimester()),
                'isExternal'      => false,
                'deptLabels'      => collect(),
                'deptRadicadas'   => collect(),
                'deptTramitadas'  => collect(),
                'tipoLabels'      => collect(),
                'tipoRadicadas'   => collect(),
                'tipoTramitadas'  => collect(),
                'mediosLabels'    => collect(),
                'mediosData'      => collect(),
                'mesLabels'       => collect(),
                'mesData'         => collect(),
                'pivotDocTypes'   => collect(),
                'pivotTable'      => collect(),
                'pivotColumnTotals' => collect(),
                'pivotGrandTotal' => 0,
            ]);
        }
        return $this->reportPqrds($request, $gesdoc);
    }

    private function findRelatedDocument($year, $trimester)
    {
        $page = ContentPage::with(['items' => fn($q) => $q->orderBy('ordering')])
            ->active()
            ->where('slug', 'pqrds-quarterly-reports')
            ->first();

        if (!$page || $page->items->isEmpty()) {
            return null;
        }
        $keywords = match ($trimester) {
            'Q1' => ['primer', ' I ', '(I)', ' 1 '], 
            'Q2' => ['segundo', ' II ', '(II)', ' 2 '],
            'Q3' => ['tercer', ' III ', '(III)', ' 3 '],
            'Q4' => ['cuarto', ' IV ', '(IV)', ' 4 '],
            default => []
        };

        return $page->items->first(function ($item) use ($year, $keywords) {
            $title = strtolower($item->title); 
            $searchYear = (string) $year;

            if (!str_contains($title, $searchYear)) {
                return false;
            }

            foreach ($keywords as $word) {
                if (str_contains($title, trim(strtolower($word)))) {
                    return true;
                }
            }

            return false;
        });
    }

    public function reportPqrds(Request $request, GesdocApi $gesdoc)
    {
        $year      = (int) $request->input('year', date('Y'));
        $trimester = strtoupper($request->input('trimester', $this->getCurrentTrimester()));
        
        [$fecInicio, $fecFin] = $this->datesForTrimester($year, $trimester);

        $filtro = [
            'fecInicio' => $fecInicio,
            'fecFin'    => $fecFin,
            // (Opcional) Pasar otros filtros si los añades al formulario
            // 'oficina'   => $request->input('oficina'), 
        ];

        try {
            $summary = $gesdoc->reporteResumen($filtro);
        } catch (\Exception $e) {
            Log::error("Error al conectar con GesDoc API: " . $e->getMessage());
            return redirect()->route('pqrds-reports')
                ->with('error', 'No se pudo conectar con el servicio de GesDoc. Por favor, intente más tarde.');
        }

        $deptData = collect($summary['by_department'] ?? []);
        $deptLabels = $deptData->pluck('label');
        $deptRadicadas = $deptData->pluck('radicadas');
        $deptTramitadas = $deptData->pluck('tramitadas');

        $tipoData = collect($summary['by_doc_type'] ?? []);
        $tipoLabels = $tipoData->pluck('label');
        $tipoRadicadas = $tipoData->pluck('radicadas');
        $tipoTramitadas = $tipoData->pluck('tramitadas');

        $mediosData = collect($summary['by_medium'] ?? []);
        $mediosLabels = $mediosData->pluck('label');
        $mediosTotales = $mediosData->pluck('total');
        
        $mesData = collect($summary['by_month'] ?? []);
        $mesLabels = $mesData->pluck('label');
        $mesTotales = $mesData->pluck('total');

        $pivotData = collect($summary['full_pivot_data'] ?? []);

        $pivotDocTypes = $pivotData->pluck('tipo_doc')->unique()->sort()->values();

        $pivotTable = $pivotData->groupBy('departamento')
            ->map(function ($rowsInDept, $departmentName) use ($pivotDocTypes) {
                $row = [
                    'departamento' => $departmentName,
                    'counts' => [],
                    'total_fila' => $rowsInDept->sum('radicadas')
                ];

                foreach ($pivotDocTypes as $tipo) {
                    $row['counts'][$tipo] = $rowsInDept->where('tipo_doc', $tipo)->sum('radicadas');
                }
                
                return $row;
            })->sortBy('departamento');

        $pivotColumnTotals = [];
        foreach ($pivotDocTypes as $tipo) {
            $pivotColumnTotals[$tipo] = $pivotData->where('tipo_doc', $tipo)->sum('radicadas');
        }
        
        $pivotGrandTotal = $pivotData->sum('radicadas');
        $relatedDocument = $this->findRelatedDocument($year, $trimester);

        return view('public.citizen-care-services.pqrds-reports', [
            'year'            => $year,
            'trimester'       => $trimester,
            'isExternal'      => true,

            'relatedDocument' => $relatedDocument,

            'deptLabels'      => $deptLabels,
            'deptRadicadas'   => $deptRadicadas,
            'deptTramitadas'  => $deptTramitadas,
            
            'tipoLabels'      => $tipoLabels,
            'tipoRadicadas'   => $tipoRadicadas,
            'tipoTramitadas'  => $tipoTramitadas,

            'mediosLabels'    => $mediosLabels,
            'mediosData'      => $mediosTotales,

            'mesLabels'       => $mesLabels,
            'mesData'         => $mesTotales,
            
            'pivotDocTypes'       => $pivotDocTypes,
            'pivotTable'          => $pivotTable,
            'pivotColumnTotals'   => $pivotColumnTotals,
            'pivotGrandTotal'     => $pivotGrandTotal,
        ]);
    }

    private function getCurrentTrimester(): string
    {
        $month = date('n');
        return match (true) {
            $month <= 3 => 'Q1',
            $month <= 6 => 'Q2',
            $month <= 9 => 'Q3',
            default => 'Q4',
        };
    }

    private function datesForTrimester(int $year, string $q): array
    {
        return match ($q) {
            'Q1' => ["$year-01-01", "$year-03-31"],
            'Q2' => ["$year-04-01", "$year-06-30"],
            'Q3' => ["$year-07-01", "$year-09-30"],
            'Q4' => ["$year-10-01", "$year-12-31"],
            default => ["$year-01-01", "$year-12-31"],
        };
    }

    public function userSatisfactionReport(?string $slug = 'user-satisfaction-reports')
    {
        $page = ContentPage::with(['items' => fn($q) => $q->orderBy('ordering')])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.citizen-care-services.user-satisfaction-report', [
            'page' => $page,
        ]);
    }
    // FIN PQRDS REPORT

    public function indexPublicDataProtection()
    {
        $dataProtection = DataProtection::first();
        return view('public.citizen-care-services.data-protection', compact('dataProtection'));
    }

}
