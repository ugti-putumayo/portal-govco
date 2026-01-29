<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class RolController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        $this->authorizeCrud('roles');
    }

    public function index()
    {
        $roles = Role::paginate(15);
        return view('dashboard.administration.roles.index', compact('roles'));
    }

    public function show(Request $request, $id)
    {
        $rol = Role::find($id);
        if (!$rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }
        return view('dashboard.administration.roles.show', compact('rol'));
    }

    public function create()
    {
        return view('dashboard.administration.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40'
        ]);

        $rol = Role::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Rol creado con éxito',
                'area' => $rol
            ]);
        }

        return redirect()->route('dashboard.roles.index')->with('success', 'Rol creado con éxito');
    }

    public function edit($id)
    {
        $rol = Role::findOrFail($id);
        return response()->json($rol);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:40',
        ]);

        $rol = Role::findOrFail($id);
        $rol->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Rol actualizado con éxito',
                'area' => $rol
            ]);
        }

        return redirect()->route('dashboard.roles.index')->with('success', 'Rol actualizado con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $rol = Role::findOrFail($id);
        $rol->delete();

        return response()->json(['message' => 'Rol eliminado con éxito'], 200);
    }
}