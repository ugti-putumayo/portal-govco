<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\UserModulePermission;
use App\Models\User;
use App\Models\Module;
use App\Models\Permission;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class UserModulePermissionController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        $this->authorizeCrud('usermodules');
        $this->middleware('perm:usermodules.assign')->only('assignPermission');
        $this->middleware('perm:usermodules.permissions.read')->only('getUserPermissions');
        $this->middleware('perm:usermodules.revoke')->only('revokePermission');
        $this->middleware('perm:usermodules.sync')->only(['syncPermissions', 'syncModules']);
    }

    public function assignPermission(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'permissions' => 'required|array',
        ]);

        foreach ($request->permissions as $key => $permissionIds) {
            if (Str::startsWith($key, 'module_')) {
                $moduleId = (int) Str::after($key, 'module_');
                UserModulePermission::updateOrCreate([
                    'user_id'      => $request->user_id,
                    'module_id'    => $moduleId,
                    'permission_id'=> null,
                ], []);
                continue;
            }

            $moduleId = (int) $key;
            if (!Module::whereKey($moduleId)->exists()) {
                continue;
            }

            foreach ((array)$permissionIds as $pid) {
                if ($pid === null || $pid === 'null' || $pid === '') {
                    UserModulePermission::updateOrCreate([
                        'user_id'      => $request->user_id,
                        'module_id'    => $moduleId,
                        'permission_id'=> null,
                    ], []);
                } else {
                    $permId = (int)$pid;
                    if (!Permission::whereKey($permId)->exists()) continue;

                    UserModulePermission::updateOrCreate([
                        'user_id'      => $request->user_id,
                        'module_id'    => $moduleId,
                        'permission_id'=> $permId,
                    ], []);
                }
            }
        }

        return response()->json(['message' => 'Permisos asignados correctamente']);
    }

    public function getUserPermissions($userId)
    {
        $user = User::find($userId);
        if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);

        $rows = UserModulePermission::with(['module','permission'])
            ->where('user_id', $userId)
            ->get();

        $permissions = $rows->map(fn($r) => [
            'module_id'     => $r->module_id,
            'permission_id' => $r->permission_id,
        ]);

        $modules = $rows->pluck('module')->filter()->unique('id')->values()->map(fn($m)=>[
            'id'         => $m->id,
            'name'       => $m->name,
            'parent_id'  => $m->parent_id,
            'route'      => $m->route,
        ]);

        return response()->json([
            'modules'     => $modules,
            'permissions' => $permissions,
        ]);
    }

    public function revokePermission(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'module_id'    => 'required|exists:modules,id',
            'permission_id'=> 'nullable|exists:permissions,id',
        ]);

        $deleted = UserModulePermission::where([
            'user_id'      => $request->user_id,
            'module_id'    => $request->module_id,
            'permission_id'=> $request->permission_id,
        ])->delete();

        return $deleted
            ? response()->json(['message' => 'Permiso revocado'])
            : response()->json(['message' => 'Permiso no encontrado'], 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:250',
            'shortname'     => 'required|string|max:250|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'module_id'     => 'required|exists:modules,id',
            'permission_id' => 'nullable|exists:permissions,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->shortname,
            'password' => bcrypt($request->password),
        ]);

        UserModulePermission::create([
            'user_id'      => $user->id,
            'module_id'    => $request->module_id,
            'permission_id'=> $request->permission_id,
        ]);

        return response()->json([
            'message' => 'Usuario creado y permiso asignado',
            'user'    => $user
        ]);
    }

    public function syncPermissions(Request $request)
    {
        $request->validate([
            'user_id'     => ['required', 'exists:users,id'],
            'permissions' => ['nullable', 'array'],
            'modules'     => ['nullable', 'array'],
        ]);

        $userId = (int) $request->user_id;

        $user = User::with('role.permissions')->findOrFail($userId);
        $rolePermIds = $user->role
            ? $user->role->permissions->pluck('id')->all()
            : [];


        $validRequestedPairs = [];
        $requestedPermIds     = [];

        foreach (($request->permissions ?? []) as $key => $permIds) {
            if (Str::startsWith($key, 'module_')) {
                continue;
            }

            $moduleId = (int) $key;
            if ($moduleId <= 0) continue;

            foreach ((array) $permIds as $pid) {
                if (!$pid) continue;
                $pid = (int) $pid;

                $exists = Permission::where('id', $pid)
                            ->where('module_id', $moduleId)
                            ->exists();
                if (!$exists) {
                    continue;
                }

                $validRequestedPairs[] = [
                    'module_id'     => $moduleId,
                    'permission_id' => $pid,
                ];
                $requestedPermIds[] = $pid;
            }
        }

        $requestedPermIds = array_values(array_unique($requestedPermIds));

        $onlyOverrides = array_values(array_diff($requestedPermIds, $rolePermIds));

        $currentDirect = UserModulePermission::where('user_id', $user->id)
            ->whereNotNull('permission_id')
            ->get(['permission_id']);
        $currentDirectIds = $currentDirect->pluck('permission_id')->all();

        $toDelete = array_values(array_diff($currentDirectIds, $onlyOverrides));
        if (!empty($toDelete)) {
            UserModulePermission::where('user_id', $user->id)
                ->whereIn('permission_id', $toDelete)
                ->delete();
        }

        $toInsert = array_values(array_diff($onlyOverrides, $currentDirectIds));
        if (!empty($toInsert)) {
            $perms = \App\Models\Permission::whereIn('id', $toInsert)->get(['id','module_id']);
            foreach ($perms as $p) {
                \App\Models\UserModulePermission::updateOrCreate([
                    'user_id'       => $user->id,
                    'module_id'     => $p->module_id,
                    'permission_id' => $p->id,
                ], []);
            }
        }

        if ($request->filled('modules')) {
            $modulesArr = collect($request->modules)->map(fn($m) => (int)$m)->filter()->unique()->values();
            $currentMarked = UserModulePermission::where('user_id', $user->id)
                ->whereNull('permission_id')
                ->get(['module_id']);
            $currentMarkedIds = $currentMarked->pluck('module_id')->all();

            $modsToDelete = array_values(array_diff($currentMarkedIds, $modulesArr->all()));
            if (!empty($modsToDelete)) {
                UserModulePermission::where('user_id', $user->id)
                    ->whereNull('permission_id')
                    ->whereIn('module_id', $modsToDelete)
                    ->delete();
            }

            $modsToInsert = array_values(array_diff($modulesArr->all(), $currentMarkedIds));
            foreach ($modsToInsert as $mid) {
                UserModulePermission::updateOrCreate([
                    'user_id'       => $user->id,
                    'module_id'     => (int)$mid,
                    'permission_id' => null,
                ], []);
            }
        }

        return response()->json(['message' => 'Permisos sincronizados correctamente']);
    }

    public function syncModules(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'modules'  => 'array',
        ]);

        $userId = $request->user_id;
        $current = UserModulePermission::where('user_id', $userId)
            ->whereNull('permission_id')
            ->get();

        $new = collect($request->modules ?? [])->map(fn($m)=>[
            'user_id'      => $userId,
            'module_id'    => (int)$m,
            'permission_id'=> null,
        ]);

        foreach ($current as $row) {
            $exists = $new->first(fn($n) => $n['module_id']==$row->module_id);
            if (!$exists) $row->delete();
        }

        foreach ($new as $n) {
            UserModulePermission::updateOrCreate($n, []);
        }

        return response()->json(['message' => 'Módulos actualizados correctamente']);
    }

    public function syncAll(Request $request)
    {
        $request->validate([
            'user_id'     => ['required', 'exists:users,id'],
            'role_id'     => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'modules'     => ['nullable', 'array'],
        ]);

        $user = \App\Models\User::with('role.permissions')->findOrFail((int)$request->user_id);

        $user->rol_id = (int) $request->role_id;
        $user->save();

        $user->load('role.permissions');
        $rolePermIds = $user->role
            ? $user->role->permissions->pluck('id')->all()
            : [];

        $validRequestedPairs = [];
        $requestedPermIds    = [];

        foreach (($request->permissions ?? []) as $key => $permIds) {
            if (Str::startsWith($key, 'module_')) {
                continue;
            }

            $moduleId = (int) $key;
            if ($moduleId <= 0) continue;

            foreach ((array) $permIds as $pid) {
                if (!$pid) continue;
                $pid = (int) $pid;

                $exists = Permission::where('id', $pid)
                            ->where('module_id', $moduleId)
                            ->exists();
                if (!$exists) {
                    continue;
                }

                $validRequestedPairs[] = [
                    'module_id'     => $moduleId,
                    'permission_id' => $pid,
                ];
                $requestedPermIds[] = $pid;
            }
        }

        $requestedPermIds = array_values(array_unique($requestedPermIds));

        $onlyOverrides = array_values(array_diff($requestedPermIds, $rolePermIds));

        $currentDirect = UserModulePermission::where('user_id', $user->id)
            ->whereNotNull('permission_id')
            ->get(['permission_id']);
        $currentDirectIds = $currentDirect->pluck('permission_id')->all();

        $toDelete = array_values(array_diff($currentDirectIds, $onlyOverrides));
        if (!empty($toDelete)) {
            UserModulePermission::where('user_id', $user->id)
                ->whereIn('permission_id', $toDelete)
                ->delete();
        }

        $toInsert = array_values(array_diff($onlyOverrides, $currentDirectIds));
        if (!empty($toInsert)) {
            $perms = Permission::whereIn('id', $toInsert)->get(['id','module_id']);
            foreach ($perms as $p) {
                UserModulePermission::updateOrCreate([
                    'user_id'       => $user->id,
                    'module_id'     => $p->module_id,
                    'permission_id' => $p->id,
                ], []);
            }
        }

        if ($request->filled('modules')) {
            $modulesArr = collect($request->modules)->map(fn($m) => (int)$m)->filter()->unique()->values();

            $currentMarked = UserModulePermission::where('user_id', $user->id)
                ->whereNull('permission_id')
                ->get(['module_id']);
            $currentMarkedIds = $currentMarked->pluck('module_id')->all();

            $modsToDelete = array_values(array_diff($currentMarkedIds, $modulesArr->all()));
            if (!empty($modsToDelete)) {
                UserModulePermission::where('user_id', $user->id)
                    ->whereNull('permission_id')
                    ->whereIn('module_id', $modsToDelete)
                    ->delete();
            }

            $modsToInsert = array_values(array_diff($modulesArr->all(), $currentMarkedIds));
            foreach ($modsToInsert as $mid) {
                UserModulePermission::updateOrCreate([
                    'user_id'       => $user->id,
                    'module_id'     => (int)$mid,
                    'permission_id' => null,
                ], []);
            }
        }

        return response()->json(['message' => 'Rol, permisos y módulos sincronizados correctamente']);
    }
}