<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeSet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function maintain()
    {
        return Inertia::render('Home/maintain');
    }

   public function index(Request $request)
{
    // Categories + 8 latest products for each
    $categories = Category::where('status', 1)
        ->with([
            'media',
            'products' => function ($q) {
                $q->where('status', 1)
                    ->with('media')
                    ->orderBy('created_at', 'desc')
                    ->take(8);
            },
        ])
        ->get()
        ->map(function ($c) {
            $img = optional($c->getFirstMedia('category_image'))->getUrl();

            return [
                'id'       => $c->id,
                'title'    => $c->title,
                'slug'     => $c->slug,
                'featured' => (bool) $c->featured,
                'image'    => $img,

                // 8 products for this category
                'products' => $c->products->map(function ($p) {
                    $thumb = optional($p->getFirstMedia('product_images'))->getUrl();

                    return [
                        'id'           => $p->id,
                        'name'         => $p->name,
                        'slug'         => $p->slug,
                        'image'        => $thumb,
                        'price'        => $p->sale_price ?? $p->price,
                        'regular_price'=> $p->price,
                        'sale_price'   => $p->sale_price,
                        'is_new'       => $p->created_at
                            ? $p->created_at->gt(now()->subDays(30))
                            : false,
                    ];
                })->values(),
            ];
        })
        ->values();

    // Brands / attributes stay as you already have:
    $brands = Brand::where('status', 1)
        ->with('media')
        ->get()
        ->map(function ($b) {
            $logo   = optional($b->getFirstMedia('brand_logo'))->getUrl();
            $banner = optional($b->getFirstMedia('brand_banner'))->getUrl();
            return [
                'id'                => $b->id,
                'title'             => $b->title,
                'slug'              => $b->slug,
                'featured'          => (bool) $b->featured,
                'website_url'       => $b->website_url,
                'support_email'     => $b->support_email,
                'hotline_phone'     => $b->hotline_phone,
                'country'           => $b->country,
                'founded_year'      => $b->founded_year,
                'short_description' => $b->short_description,
                'seo_title'         => $b->seo_title,
                'seo_description'   => $b->seo_description,
                'logo'              => $logo,
                'banner'            => $banner,
            ];
        })
        ->values();

    $attributes = Attribute::where('status', 1)
        ->with(['options'])
        ->get()
        ->map(function ($a) {
            return [
                'id'                => $a->id,
                'code'              => $a->code,
                'name'              => $a->name,
                'type'              => $a->type,
                'unit'              => $a->unit,
                'is_filterable'     => (bool) $a->is_filterable,
                'is_variant_option' => (bool) $a->is_variant_option,
                'options'           => $a->type === 'select'
                    ? $a->options->map(fn ($o) => [
                        'id'    => $o->id,
                        'value' => $o->value,
                        'hex'   => $o->hex,
                    ])->values()
                    : [],
            ];
        })
        ->values();

    $attributeSets = AttributeSet::where('status', 1)
        ->with(['attributes'])
        ->get()
        ->map(function ($s) {
            return [
                'id'   => $s->id,
                'name' => $s->name,
                'attributes' => $s->attributes->map(function ($a) {
                    return [
                        'id'          => $a->id,
                        'code'        => $a->code,
                        'name'        => $a->name,
                        'type'        => $a->type,
                        'unit'        => $a->unit,
                        'is_required' => (bool) ($a->pivot->is_required ?? false),
                    ];
                })->values(),
            ];
        })
        ->values();

    return Inertia::render('Home/index', [
        'categories'    => $categories,
        'brands'        => $brands,
        'attributes'    => $attributes,
        'attributeSets' => $attributeSets,
    ]);
}
}
