<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transparency;
use App\Models\ContentPage;

class TransparenciaController extends Controller
{
    public function index()
    {
        $secciones = Transparency::where('tipo', 'seccion')
                                 ->orderBy('orden')
                                 ->get();

        $breadcrumbItems = [
            ['url' => route('home'), 'label' => 'Inicio'],
            ['url' => route('transparencia.index'), 'label' => 'Transparencia'],
        ];

        return view('public.transparency.index', compact('secciones', 'breadcrumbItems'));
    }

    public function show($id)
    {
        $seccionData = Transparency::where('tipo', 'seccion')->find($id);
        if (!$seccionData) {
            abort(404);
        }
        $subElementos = Transparency::where('tipo', 'subelemento')
                                     ->where('id_padre', $seccionData->id)
                                     ->orderBy('orden')
                                     ->get();

        return view('public.transparency.show', compact('seccionData', 'subElementos'));
    }

    public function planAction()
    {
        $page = ContentPage::with([
            'items' => fn ($q) => $q->orderBy('ordering')
        ])
            ->active()
            ->where('slug', 'plan-action')
            ->firstOrFail();

        return view('public.transparency.planning-budgeting-reporting.plan-action.plan-action', [
            'page'  => $page,
            'items' => $page->items,
        ]);
    }

    public function fiscalFramework(?string $slug = 'fiscal-framework')
    {
        $page = ContentPage::with(['items' => fn($q) => $q->orderBy('ordering')])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.transparency.planning-budgeting-reporting.fiscal-framework', [
            'page' => $page,
        ]);
    }

    private function showPlan(string $slug)
    {
        $page = ContentPage::with(['items' => fn($q) => $q->orderBy('ordering', 'asc')])
            ->active()
            ->where('slug', $slug)
            ->first();

        if (!$page) {
            $page = new \App\Models\ContentPage();
            $page->title = 'Información no disponible';
            $page->description = 'Este plan institucional aún no ha sido cargado o publicado en el sistema.';
            $page->body = '<p class="text-center text-muted">Estamos actualizando la información. Por favor intente nuevamente más tarde.</p>';
            $page->items = collect([]);
        }

        return view('public.transparency.planning-budgeting-reporting.plan-action.show', [
            'page' => $page
        ]);
    }

    // 1. PINAR
    public function institutionalArchivesPlan()
    {
        return $this->showPlan('institutional-archives-plan');
    }
    
    // 2. TP
    public function transparencyPlan()
    {
        return $this->showPlan('transparency-plan');
    }

    // 3. PAA
    public function annualAcquisitionsPlan()
    {
        return $this->showPlan('annual-acquisitions-plan');
    }

    // 4. Vacantes
    public function annualVacanciesPlan()
    {
        return $this->showPlan('annual-vacancies-plan');
    }

    // 5. Previsión RH
    public function hrForecastingPlan()
    {
        return $this->showPlan('hr-forecasting-plan');
    }

    // 6. Talento Humano
    public function strategicHumanTalentPlan()
    {
        return $this->showPlan('strategic-human-talent-plan');
    }

    // 7. Capacitación (PIC)
    public function institutionalTrainingPlan()
    {
        return $this->showPlan('institutional-training-plan');
    }

    // 8. Incentivos
    public function institutionalIncentivesPlan()
    {
        return $this->showPlan('institutional-incentives-plan');
    }

    // 9. SST
    public function occupationalHealthSafetyPlan()
    {
        return $this->showPlan('occupational-health-safety-plan');
    }

    // 10. Anticorrupción
    public function antiCorruptionPlan()
    {
        return $this->showPlan('anti-corruption-plan');
    }

    // 11. PETI
    public function itStrategicPlan()
    {
        return $this->showPlan('it-strategic-plan');
    }

    // 12. Riesgos Seguridad
    public function riskTreatmentPlan()
    {
        return $this->showPlan('security-privacy-risk-treatment-plan');
    }

    // 13. Seguridad y Privacidad
    public function securityPrivacyPlan()
    {
        return $this->showPlan('information-security-privacy-plan');
    }

    // 14. Plan de acción
    public function declarationPETP()
    {
        return $this->showPlan('declaration-ptep');
    }
}