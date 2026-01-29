<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contractor;
use App\Models\Execution;
use App\Models\HiringAnual;

class HiringController extends Controller
{
    public function indexPublicContractual(Request $request)
    {
        $query = Contractor::query();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('contractor', 'LIKE', "%{$search}%")
                    ->orWhere('contract_number', 'LIKE', "%{$search}%")
                    ->orWhere('object', 'LIKE', "%{$search}%")
                    ->orWhere('dependency', 'LIKE', "%{$search}%")
                    ->orWhere('supervision', 'LIKE', "%{$search}%");
            });
        }

        $contractors = $query
            ->orderByDesc('year_contract')
            ->orderByDesc('month_contract')
            ->orderBy('contract_number')
            ->paginate(20)
            ->withQueryString();

        return view('public.transparency.hiring.list-of-contracts', [
            'contractors' => $contractors,
        ]);
    }

    public function indexPublicExecution(Request $request)
    {
        $search = $request->input('search');
        $executions = Execution::when($search, function ($query, $search) {
            return $query->where('contract_number', 'like', "%{$search}%");
        })->paginate(10);

        return view('public.transparency.hiring.execution', compact('executions'));
    }

    public function indexPublicHiringAnual(Request $request)
    {
        $search = $request->input('search');

        $hiringAnuals = HiringAnual::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                         ->orWhere('tipo', 'like', '%' . $search . '%');
        })->paginate(10);

        return view('public.transparency.hiring.hiring-anual', compact('hiringAnuals', 'search'));
    }
}