<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Consecutives\Consecutives;
use App\Models\Dependency;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $table = 'users';

    protected $fillable = ['name','email','password', 'dependency_id', 'rol_id'];

    protected $hidden = ['password','remember_token','two_factor_recovery_codes','two_factor_secret'];

    protected $casts = ['email_verified_at' => 'datetime','rol_id' => 'integer'];

    protected $appends = ['profile_photo_url'];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'user_module_permissions', 'user_id', 'module_id')
                    ->withPivot('permission_id')
                    ->distinct();
    }

    public function permissionsByModule()
    {
        return $this->hasMany(UserModulePermission::class)
                    ->with(['module:id,parent_id,name,route', 'permission:id,key,name']);
    }

    public function modulePermissions()
    {
        return $this->hasMany(UserModulePermission::class);
    }

    public function role() { return $this->belongsTo(Role::class, 'rol_id'); }

    public function markedModules()
    {
        return $this->hasMany(UserModulePermission::class)
                    ->whereNull('permission_id')
                    ->with('module:id,parent_id,name,route');
    }

    public function directPermissions()
    {
        return $this->hasMany(UserModulePermission::class)
                    ->whereNotNull('permission_id')
                    ->with('permission:id,module_id,key,name');
    }

    public function isAdmin(): bool
    {
        return (int) $this->rol_id === 1;
    }

    public function hasRole($roles): bool
    {
        $this->loadMissing('role');
        if (!$this->role) return false;

        $roles = is_array($roles) ? $roles : explode(',', $roles);
        foreach ($roles as $r) {
            $r = trim($r);
            if ($r === '') continue;

            if (ctype_digit($r) && (int)$r === (int)$this->rol_id) return true;
            if (strcasecmp($this->role->name ?? '', $r) === 0) return true;
            if (isset($this->role->slug) && strcasecmp($this->role->slug, $r) === 0) return true;
        }
        return false;
    }

    public function effectivePermissionKeys(): array
    {
        $this->loadMissing('role.permissions');
        $roleKeys = $this->role ? $this->role->permissions->pluck('key')->all() : [];

        $this->loadMissing('directPermissions.permission');
        $userKeys = $this->directPermissions
            ->pluck('permission.key')
            ->filter()
            ->all();

        return array_values(array_unique(array_map('strtolower', array_merge($roleKeys, $userKeys))));
    }

    public function effectiveModuleIds(): array
    {
        $keys = $this->effectivePermissionKeys();
        if (empty($keys)) {
            return $this->markedModules()->pluck('module_id')->unique()->all();
        }

        $permToMod = Permission::whereIn('key', $keys)->pluck('module_id', 'key');
        $fromPerms = collect($keys)->map(fn($k) => $permToMod[$k] ?? null)->filter()->unique()->values()->all();
        $marked    = $this->markedModules()->pluck('module_id')->unique()->all();

        return array_values(array_unique(array_merge($fromPerms, $marked)));
    }

    public function canPerm(string $permissionKey): bool
    {
        if ($this->isAdmin()) return true;

        $needle = strtolower($permissionKey);
        return in_array($needle, $this->effectivePermissionKeys(), true);
    }

    public function canOn(string $moduleKey, string $action): bool
    {
        return $this->canPerm("$moduleKey.$action");
    }

    public function visibleModuleIds(): array
    {
        $keys = $this->effectivePermissionKeys();
        if (empty($keys)) return [];

        $permModuleIds = Permission::whereIn('key', $keys)
            ->pluck('module_id')
            ->unique()
            ->values()
            ->all();

        if (empty($permModuleIds)) return [];

        $parents = Module::query()->get(['id','parent_id'])
            ->pluck('parent_id', 'id');

        $all = $permModuleIds;
        foreach ($permModuleIds as $id) {
            $p = $parents[$id] ?? null;
            while ($p) {
                $all[] = (int)$p;
                $p = $parents[$p] ?? null;
            }
        }

        return array_values(array_unique($all));
    }

    public function consecutives()
    {
        return $this->hasMany(Consecutives::class, 'user_id');
    }

    public function canceledConsecutives()
    {
        return $this->hasMany(Consecutives::class, 'canceled_by_user_id');
    }

    public function dependency()
    {
        return $this->belongsTo(Dependency::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}