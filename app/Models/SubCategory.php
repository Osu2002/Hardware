<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SubCategory extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $table = 'subcategories';

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'status',
        'sort_order',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('subcategory_image')->useDisk('public');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
