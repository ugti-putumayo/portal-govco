<?php
namespace App\Http\Controllers;

use App\Models\ContentPage;

class OpenDataController extends Controller
{
    public function indexPublicStatisticalInformationManagement(?string $slug = 'gestion-informacion-estadistica')
    {
        if ($slug === 'gestion-informacion-estadistica') {
            $pages = ContentPage::with(['items' => fn($q) => $q->orderBy('ordering')])
                ->active()
                ->where('slug', 'like', 'gestion-informacion-estadistica-%')
                ->orderBy('ordering')
                ->get();

            return view('public.transparency.open-data.statistical-information-management', [
                'pages' => $pages,
            ]);
        }

        $page = ContentPage::with('items')
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.transparency.open-data.statistical-information-management', [
            'page' => $page,
        ]);
    }
}