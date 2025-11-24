<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name','slug','sku','status','sort_order','brand_id','uom_id',
        'attribute_set_id','primary_category_id','price','sale_price',
        'short_description','description','discount_status','discount_type','discounted_amount', 
    ];

    // Media collection (optional)
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images')->useDisk('public');
    }

    // Relations
    public function brand(){ return $this->belongsTo(Brand::class); }
    public function uom(){ return $this->belongsTo(Uom::class); }
    public function attributeSet(){ return $this->belongsTo(AttributeSet::class); }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product')->withTimestamps();
    }

    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
    
   
}
