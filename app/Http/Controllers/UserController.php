<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Module;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class UserController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        $this->authorizeCrud('users');
    }

    public function index()
    {
        $users = User::paginate(15);
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::all();
        $modules = Module::whereNull('parent_id')
            ->with([
                'permissions:id,module_id,key,name',
                'children.permissions:id,module_id,key,name',
                'children.children.permissions:id,module_id,key,name',
            ])
            ->orderBy('order')->get();

        return view('dashboard.administration.users.index', compact('users', 'roles', 'permissions', 'modules'));
    }

    public function show(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('dashboard.administration.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:250',
            'email'         => 'required|string|email|max:250|unique:users,email',
            'password'      => 'required|string|min:8|max:100',
            'rol_id'        => 'nullable|integer|exists:roles,id',
            'dependency_id' => 'nullable|integer|exists:dependencies,id',
        ]);

        $user = new User();
        $user->name          = trim($validated['name']);
        $user->email         = strtolower(trim($validated['email']));
        $user->password      = Hash::make($validated['password']);
        $user->rol_id        = $validated['rol_id'] ?? null;
        $user->dependency_id = $validated['dependency_id'] ?? null;
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Usuario creado con éxito',
                'user'    => $user,
            ]);
        }

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'Usuario creado con éxito');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:250',
            'email'         => [
                'required', 'string', 'email', 'max:250',
                Rule::unique('users')->ignore($user->id),
            ],
            'password'      => 'nullable|string|min:8|max:100',
            'rol_id'        => 'nullable|integer|exists:roles,id',
            'dependency_id' => 'nullable|integer|exists:dependencies,id',
        ]);

        $user->name  = trim($validated['name']);
        $user->email = strtolower(trim($validated['email']));

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->rol_id        = $validated['rol_id'] ?? $user->rol_id;
        $user->dependency_id = $validated['dependency_id'] ?? $user->dependency_id;
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Usuario actualizado con éxito',
                'user'    => $user,
            ]);
        }

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'Usuario actualizado con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado con éxito'], 200);
    }

    public function getChildrenByModule($module_id)
    {
        $children = \App\Models\Module::where('parent_id', $module_id)->orderBy('order')->get();
        return response()->json($children);
    }

    public function getBossesArea()
    {
        $jefes = User::where('rol_id', 2)->select('id', 'name')->get();
        return response()->json($jefes);
    }

    public function effectivePermissionKeys(): Collection
    {
        $roleKeys = collect();
        if ($this->relationLoaded('role')) {
            $this->loadMissing('role.permissions');
        }
        if ($this->role) {
            $roleKeys = $this->role->permissions->pluck('key');
        }

        $this->loadMissing('directPermissions.permission');
        $directKeys = $this->directPermissions
            ->pluck('permission.key')
            ->filter();

        return $roleKeys->merge($directKeys)->unique()->values();
    }

    public function effectiveModuleIds(): Collection
    {
        $keys = $this->effectivePermissionKeys();
        if ($keys->isEmpty()) {
            return $this->markedModules()->pluck('module_id')->unique();
        }

        $permToMod = Permission::whereIn('key', $keys)->pluck('module_id', 'key');
        $fromPerms = $keys->map(fn($k) => $permToMod[$k] ?? null)->filter()->unique();

        $explicit = $this->markedModules()->pluck('module_id')->unique();

        return $fromPerms->merge($explicit)->unique()->values();
    }

    public function canPerm(string $permissionKey): bool
    {
        if ($this->isAdmin()) return true;
        return $this->effectivePermissionKeys()->contains($permissionKey);
    }

    public function canOn(string $moduleKey, string $action): bool
    {
        return $this->canPerm("$moduleKey.$action");
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Contraseña actualizada con éxito.',
            ]);
        }

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'Contraseña actualizada con éxito.');
    }
}