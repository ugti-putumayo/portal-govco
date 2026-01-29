<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller
{
    public function index()
    {
        return response()->json(Area::all(), 200);
    }

    public function show($id)
    {
        $area = Area::find($id);

        if (!$area) {
            return response()->json(['message' => 'Área no encontrada'], 404);
        }

        return response()->json($area, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'shortname' => 'required|string|max:250'
        ]);

        $area = Area::create($request->all());

        return response()->json(['message' => 'Área creada con éxito', 'area' => $area], 201);
    }

    public function update(Request $request, $id)
    {
        $area = Area::find($id);

        if (!$area) {
            return response()->json(['message' => 'Área no encontrada'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:250',
            'shortname' => 'required|string|max:250'
        ]);

        $area->update($request->all());

        return response()->json(['message' => 'Área actualizada con éxito', 'area' => $area], 200);
    }

    public function destroy($id)
    {
        $area = Area::find($id);

        if (!$area) {
            return response()->json(['message' => 'Área no encontrada'], 404);
        }

        $area->delete();

        return response()->json(['message' => 'Área eliminada con éxito'], 200);
    }
}
