<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = ['title','slug','parent_id','status','featured','sort_order'];
    protected $table = 'categories';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_image')
            ->useDisk('public')
            ->singleFile();

        // ✅ NEW: banner image for category page top banner
        $this->addMediaCollection('category_banner')
            ->useDisk('public')
            ->singleFile();
    }

    public function parent(){ return $this->belongsTo(Category::class, 'parent_id'); }
    public function children(){ return $this->hasMany(Category::class, 'parent_id'); }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product')->withTimestamps();
    }

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }
}
