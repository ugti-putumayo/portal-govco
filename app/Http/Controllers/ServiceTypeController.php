<?php
namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class ServiceTypeController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        $this->authorizeCrud('servicetype');
    }

    public function indexData()
    {
        $services = ServiceType::all();
        return response()->json($services);
    }

    public function index()
    {
        $types = ServiceType::paginate(15);
        return view('dashboard.entityservice.service-type', compact('types'));
    }

    public function show(Request $request, $id)
    {
        $type = ServiceType::find($id);
        if (!$type) {
            return response()->json(['message' => 'Tipo de servicio no encontrado'], 404);
        }
        return view('dashboard.entityservice.show', compact('type'));
    }

    public function create()
    {
        return view('dashboard.servicetype.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $type = ServiceType::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Tipo de servicio creado con éxito',
                'type' => $type
            ]);
        }

        return redirect()->route('dashboard.servicetype.index')->with('success', 'Tipo de servicio creado con éxito');
    }

    public function edit($id)
    {
        $type = ServiceType::findOrFail($id);
        return response()->json($type);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type = ServiceType::findOrFail($id);
        $type->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Tipo de servicio actualizado con éxito',
                'type' => $type
            ]);
        }

        return redirect()->route('dashboard.servicetype.index')->with('success', 'Tipo de servicio actualizado con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $type = ServiceType::findOrFail($id);
        $type->delete();
        return response()->json(['message' => 'Tipo de servicio eliminado con éxito'], 200);
    }
}