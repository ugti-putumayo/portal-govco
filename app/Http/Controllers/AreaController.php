<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class AreaController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('areas');
    }

    public function index()
    {
        $areas = Area::paginate(15);
        return view('areas.index', compact('areas'));
    }

    public function show(Request $request, $id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json(['message' => 'Área no encontrada'], 404);
        }
        return view('areas.show', compact('area'));
    }

    public function create()
    {
        return view('dashboard.areas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'shortname' => 'required|string|max:250'
        ]);

        $area = Area::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Área creada con éxito',
                'area' => $area
            ]);
        }

        return redirect()->route('dashboard.areas.index')->with('success', 'Área creada con éxito');
    }

    public function edit($id)
    {
        $area = Area::findOrFail($id);
        return response()->json($area);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'shortname' => 'required|string|max:250'
        ]);

        $area = Area::findOrFail($id);
        $area->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Área actualizada con éxito',
                'area' => $area
            ]);
        }

        return redirect()->route('dashboard.areas.index')->with('success', 'Área actualizada con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json(['message' => 'Área eliminada con éxito'], 200);
    }
}