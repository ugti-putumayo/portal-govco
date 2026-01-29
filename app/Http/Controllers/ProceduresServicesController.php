<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntityService;

class ProceduresServicesController extends Controller
{
    public function indexPublicServicesEntity(Request $request)
    {
        $services = EntityService::where('status', 1)
                ->orderBy('order_index', 'asc')
                ->get();
        return view('public.transparency.procedures-services.services-entity', compact('services'));
    }
}