<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $table = 'brands';

    protected $fillable = [
        'title','slug','status','featured','website_url','hotline_phone','support_email',
        'country','founded_year','short_description','long_description',
        'seo_title','seo_description','sort_order'
    ];

    protected $casts = [
        'status' => 'integer',
        'featured' => 'integer',
        'founded_year' => 'integer',
        'sort_order' => 'integer',
    ];
}
