<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Decision;

class DecisionsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $decisions = Decision::when($search, function ($query, $search) {
                            return $query->where('name', 'like', "%{$search}%");
                        })
                        ->paginate(12);

        return view('decisions', compact('decisions', 'search'));
    }
}
