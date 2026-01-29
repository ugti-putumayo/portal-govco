<?php
namespace App\Http\Controllers;

use App\Models\Participate;
use Illuminate\Http\Request;

class ParticipateController extends Controller
{
    public function index()
    {
        $participates = Participate::all();
        return view('public.participate.participate', compact('participates'));
    }

    public function show($id)
    {
        $participate = Participate::with('sections')->findOrFail($id);
        return view('public.participate.show', compact('participate'));
    }
}
