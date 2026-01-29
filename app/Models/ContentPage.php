<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    protected $fillable = [
        'module',
        'title',
        'slug',
        'image',
        'state',
        'ordering',
        'meta'
    ];
    protected $casts = ['state'=>'boolean','meta'=>'array'];

    public function items() {
        return $this->hasMany(ContentItem::class)->orderBy('ordering');
    }

    public function scopeModule($q, string $module) { return $q->where('module', $module); }
    public function scopeActive($q) { return $q->where('state', true); }
}