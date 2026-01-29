<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Law;
use App\Models\ProgramPlan;
use App\Models\RegulatoryAgenda;
use App\Models\RegulatoryDecree;


class RegulationsController extends Controller
{
    public function indexPublicLaws(Request $request)
    {
        $query = Law::query();
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $field = $request->category ?? 'number';

            $allowedFields = ['number', 'name', 'topic'];
            if (in_array($field, $allowedFields)) {
                $query->where($field, 'LIKE', "%$search%");
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('number', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('topic', 'LIKE', "%$search%");
                });
            }
        }

        $laws = $query->paginate(15)->withQueryString();
        return view('public.transparency.regulations.laws', compact('laws'));
    }

    public function indexPublicProgramPlan(Request $request)
    {
        $search = $request->input('search');
        $programPlans = ProgramPlan::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('tipo', 'like', "%{$search}%")
                         ->orWhere('theme', 'like', "%{$search}%");
        })->paginate(5);

        return view('public.transparency.regulations.program-plans', compact('programPlans', 'search'));
    }

    public function indexPublicRegulatoryAgenda(Request $request)
    {
        $search = $request->input('search');
        $query = RegulatoryAgenda::query();
        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $regulatories = $query->orderBy('estimated_date', 'desc')->paginate(5);

        return view('public.transparency.regulations.regulatory-agenda', compact('regulatories', 'search'));
    }

    public function indexPublicRegulatoryDecree(Request $request)
    {
        $search = $request->input('search');

        $decrees = RegulatoryDecree::when($search, function ($query, $search) {
            return $query->where('applicable_decree', 'like', "%{$search}%")
                         ->orWhere('objective', 'like', "%{$search}%")
                         ->orWhere('regulated_areas', 'like', "%{$search}%");
        })->paginate(10);

        return view('public.transparency.regulations.regulatory-decree', compact('decrees', 'search'));
    }

    public function indexPublicNeedDiagnosis()
    {
        return view('public.transparency.regulations.needs-diagnosis');
    }

    public function indexPublicAnticorruptionHotline()
    {
        return view('public.transparency.regulations.anticorruption-hotline');
    }

    public function indexPublicWomen()
    {
        return view('public.transparency.regulations.women');
    }
}