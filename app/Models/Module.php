<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';
    protected $fillable = ['name', 'route', 'icon', 'parent_id', 'order'];
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'module_user');
    }

    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id')
            ->orderBy('order')
            ->orderBy('name');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    public function getIsLeafAttribute(): bool
    {
        return !$this->relationLoaded('children') ? !$this->children()->exists() : $this->children->isEmpty();
    }

    public function permissions()
    {
        return $this->hasMany(\App\Models\Permission::class, 'module_id');
    }
}