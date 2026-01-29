<?php
namespace App\Http\Controllers;

use App\Models\PlantOfficial;
use App\Models\ContentPage;
use App\Models\Audit;
use Illuminate\Support\Facades\Log;

class GovernorateController extends Controller
{
    public function publicGovernorIndex($typeCharge)
    {
        $governor = PlantOfficial::where('charge', $typeCharge)->get();

        if ($governor->isEmpty()) {
            return view('public.governorate.governor.governor', [
                'governor' => collect([]),
                'typeCharge' => ucfirst($typeCharge),
                'message' => "No se encontraron funcionarios para el cargo especificado: $typeCharge. Asegúrate de que el nombre es correcto en la base de datos."
            ]);
        }

        return view('public.governorate.governor.governor', compact('governor', 'typeCharge'));
    }

    public function publicGovernorShow($typeCharge, $id)
    {
        $governor = PlantOfficial::find($id);

        if (!$governor) {
            return redirect()->route('cabinet.index', $typeCharge)->with('error', 'Funcionario no encontrado');
        }

        return view('public.governorate.governor.governor', compact('governor', 'typeCharge'));
    }

    public function indexPublicOrganizationChart()
    {
        return view('public.governorate.organization-chart');
    }

    // MICROSITE - DEPENDENCIES
    public function indexPublicMicrositeTreasury()
    {
        return view('public.governorate.secretaries-offices.microsite-treasury.index');
    }

    public function aboutPublicMicrositeTreasury()
    {
        return view('public.governorate.secretaries-offices.microsite-treasury.about');
    }

    public function contactPublicMicrositeTreasury()
    {
        return view('public.governorate.secretaries-offices.microsite-treasury.contact');
    }

    public function sliderPublicMicrositeTreasury()
    {
        return view('public.governorate.secretaries-offices.microsite-treasury.slider');
    }

    public function auditPublicMicrositeTreasury()
    {
        try {
            $audits = Audit::all();

            if ($audits->isEmpty()) {
                Log::info('No hay registros en la tabla fiscalizaciones.');
            } else {
                Log::info('Registros recuperados: ', $audits->toArray());
            }
            return view('public.governorate.secretaries-offices.microsite-treasury.audit', compact('fiscalizaciones'));
        } catch (\Exception $e) {
            Log::error('Error al obtener fiscalizaciones: ' . $e->getMessage());
            return view('public.governorate.secretaries-offices.microsite-treasury.audit')->with('error', 'No se pudo conectar con la base de datos.');
        }
    } 

    public function integratedManagementSystem()
    {
        $page = ContentPage::with([
            'items' => fn ($q) => $q->orderBy('ordering')
        ])
            ->active()
            ->where('slug', 'integrated-management-system')
            ->firstOrFail();

        return view('public.governorate.sig.integrated-management-system', [
            'page'  => $page,
            'items' => $page->items,
        ]);
    }

    public function planningSecretariat()
    {
        $page = ContentPage::with([
            'items' => fn ($q) => $q->orderBy('ordering')
        ])
            ->active()
            ->where('slug', 'planning-secretariat')
            ->firstOrFail();

        $structure = $page->items->groupBy(function ($item) {
            return $item->extra['category'] ?? $this->detectCategory($item->title);
        })->map(function ($itemsByCategory) {
            return $itemsByCategory->groupBy(function ($item) {
                return $item->extra['year'] ?? $item->created_at->format('Y');
            })->sortKeysDesc();
        })->sortKeys();

        return view('public.governorate.planning.planning-secretariat', [
            'page'  => $page,
            'micrositeData' => $structure,
        ]);
    }

    private function detectCategory($title)
    {
        $title = mb_strtolower($title);
        if (str_contains($title, 'poai')) return 'POAI - Plan Operativo';
        if (str_contains($title, 'empalme')) return 'Informes de Empalme';
        if (str_contains($title, 'rendición')) return 'Rendición de Cuentas';
        if (str_contains($title, 'gestión')) return 'Informes de Gestión';
        return 'Documentación General';
    }
}