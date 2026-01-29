<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contractual;

class ContractualController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $contracts = Contractual::when($search, function($query, $search) {
                            return $query->where('name', 'like', "%{$search}%")
                                         ->orWhere('description', 'like', "%{$search}%");
                        })
                        ->orderBy('name')
                        ->paginate(15);

        return view('public.transparency.contractual', compact('contracts', 'search'));
    }
}