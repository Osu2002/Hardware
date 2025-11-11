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
        // Categories (no sort_order)
        $categories = Category::where('status', 1)
            ->with('media')
            ->get()
            ->map(function ($c) {
                $img = optional($c->getFirstMedia('category_image'))->getUrl();
                return [
                    'id'       => $c->id,
                    'title'    => $c->title,
                    'slug'     => $c->slug,
                    'featured' => (bool) $c->featured,
                    'image'    => $img,
                ];
            })
            ->values();

        // Brands (no sort_order)
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

        // Attributes (+ options), no sort_order anywhere
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

        // Attribute Sets (+ attributes), no pivot sort
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
