<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentItem extends Model
{
    protected $fillable = [
        'content_page_id',
        'title',
        'url',
        'image',
        'document',
        'description',
        'ordering',
        'extra'
    ];
    protected $casts = ['extra'=>'array'];

    public function page() {
        return $this->belongsTo(ContentPage::class);
    }
}