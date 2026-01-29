<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModulePermission extends Model
{
    use HasFactory;

    protected $table = 'user_module_permissions';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'module_id',
        'permission_id',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}