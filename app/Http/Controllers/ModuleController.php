<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class ModuleController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        $this->authorizeCrud('modules');
    }

    public function index()
    {
        $modulesAdmin = Module::roots()
            ->ordered()
            ->with('childrenRecursive')
            ->get();

        return view('dashboard.administration.modules.index', compact('modulesAdmin'));
    }

    public function show(Request $request, $id)
    {
        $module = Module::with('childrenRecursive')->find($id);
        if (!$module) {
            return response()->json(['message' => 'Módulo no encontrado'], 404);
        }
        return view('dashboard.administration.modules.show', compact('module'));
    }

    public function create()
    {
        // $parents = Module::ordered()->get();
        return view('dashboard.administration.modules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'route'     => 'required|string|max:100',
            'icon'      => 'nullable|file|mimes:svg|max:2048',
            'parent_id' => 'nullable|exists:modules,id',
            'order'     => 'nullable|integer|min:0',
        ]);

        $module = new Module();
        $module->name = $request->name;
        $module->route = $request->route;
        $module->parent_id = $request->parent_id;
        $module->order = $request->input('order', 0);

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            if ($file->getClientOriginalExtension() !== 'svg') {
                return response()->json(['error' => 'El archivo debe ser un SVG'], 422);
            }
            $filename = time() . '-' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('icon'), $filename);
            $module->icon = 'icon/' . $filename;
        }

        $module->save();

        return response()->json([
            'message' => 'Módulo creado con éxito',
            'module'  => $module
        ]);
    }

    public function edit($id)
    {
        $module = Module::findOrFail($id);
        return response()->json($module);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'route'     => 'required|string|max:100',
            'icon'      => 'nullable|file|mimes:svg|max:2048',
            'parent_id' => 'nullable|exists:modules,id|not_in:'.$id,
            'order'     => 'nullable|integer|min:0',
        ]);

        $module = Module::findOrFail($id);

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            if ($file->getClientOriginalExtension() !== 'svg') {
                return response()->json(['error' => 'El archivo debe ser un SVG'], 422);
            }
            $filename = time() . '-' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('icon'), $filename);
            $module->icon = 'icon/' . $filename;
        }

        $module->name = $request->name;
        $module->route = $request->route;
        $module->parent_id = $request->parent_id;
        $module->order = $request->input('order', $module->order ?? 0);
        $module->save();

        return response()->json([
            'message' => 'Módulo actualizado con éxito',
            'module'  => $module
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $module = Module::withCount('children')->findOrFail($id);

        if ($module->children_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar: el módulo tiene elementos hijos.'
            ], 422);
        }

        $module->delete();

        return response()->json(['message' => 'Módulo eliminado con éxito'], 200);
    }
}