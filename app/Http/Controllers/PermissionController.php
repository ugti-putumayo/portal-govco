<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Module;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class PermissionController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        $this->authorizeCrud('permissions');
    }

    public function index()
    {
        $permissions = Permission::with('module')
            ->orderBy('module_id')
            ->orderBy('key')
            ->paginate(15);

        $modules = Module::orderBy('name')->get();

        return view('dashboard.administration.permissions.index', compact('permissions', 'modules'));
    }
    public function show(Request $request, $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }
        return view('dashboard.administration.permissions.show', compact('permission'));
    }

    public function create()
    {
        return view('dashboard.administration.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module_id' => ['nullable', 'exists:modules,id'],
            'key'       => ['required', 'string', 'max:150', 'unique:permissions,key'],
            'name'      => ['required', 'string', 'max:100'],
        ]);

        $permission = Permission::create($data);

        if ($request->ajax()) {
            return response()->json([
                'message'    => 'Permiso creado con éxito',
                'permission' => $permission,
            ]);
        }

        return redirect()
            ->route('dashboard.permissions.index')
            ->with('success', 'Permiso creado con éxito');
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json($permission);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $data = $request->validate([
            'module_id' => ['nullable', 'exists:modules,id'],
            'key'       => [
                'required',
                'string',
                'max:150',
                Rule::unique('permissions', 'key')->ignore($permission->id),
            ],
            'name'      => ['required', 'string', 'max:100'],
        ]);

        $permission->update($data);

        if ($request->ajax()) {
            return response()->json([
                'message'    => 'Permiso actualizado con éxito',
                'permission' => $permission,
            ]);
        }

        return redirect()
            ->route('dashboard.permissions.index')
            ->with('success', 'Permiso actualizado con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json(['message' => 'Permiso eliminado con éxito'], 200);
    }

    public function routes()
    {
        $routes = collect(\Route::getRoutes())
            ->filter(function ($route) {
                $name = $route->getName();
                return $name && str_starts_with($name, 'dashboard.');
            })
            ->map(function ($route) {
                return [
                    'name'   => $route->getName(),                 // ej: dashboard.users.index
                    'uri'    => $route->uri(),                     // ej: dashboard/users
                    'method' => implode('|', $route->methods()),   // GET|POST...
                ];
            })
            ->values();

        return response()->json($routes);
    }
}