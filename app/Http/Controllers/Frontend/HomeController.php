<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeSet;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Homebanner;


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
             'subcategories' => fn($q) => $q->where('status', 1)->orderBy('sort_order')->orderBy('title'),
            'products' => function ($q) {
                $q->where('status', 1)
                    ->with('media')
                    ->orderBy('created_at', 'desc')
                    ->take(10);
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

                 'subcategories' => $c->subcategories->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'slug' => $s->slug,
                'category_id' => $s->category_id,
            ])->values(),

                // 8 products for this category
                'products' => $c->products->map(function ($p) {
    $thumb = optional($p->getFirstMedia('product_images'))->getUrl();

    $basePrice = (float) ($p->price ?? 0);   // original price
    $currentPrice = $basePrice;             // discounted price
    $discountPercent = 0;

    // SAME discount logic as SpecialOffers
    if ((int) $p->discount_status === 1 && $p->discount_type && (float) $p->discounted_amount > 0) {
        if ($p->discount_type === 'percent') {
            $discountPercent = (int) round((float) $p->discounted_amount);
            $currentPrice = max(0, $basePrice * (1 - ($discountPercent / 100)));
        } elseif ($p->discount_type === 'amount') {
            $amount = (float) $p->discounted_amount;
            $currentPrice = max(0, $basePrice - $amount);
            $discountPercent = $basePrice > 0 ? (int) round(($amount / $basePrice) * 100) : 0;
        }
    }

    return [
        'id'   => $p->id,
        'name' => $p->name,
        'slug' => $p->slug,
        'image'=> $thumb,

        // IMPORTANT: send old & new like CategoryNav expects
        'regular_price'    => round($basePrice, 2),       // OLD price
        'price'            => round($currentPrice, 2),    // NEW price
        'discount_percent' => $discountPercent,

        // optional (useful if you want later)
        'discount_status'   => (int) $p->discount_status,
        'discount_type'     => $p->discount_type,
        'discounted_amount' => (float) $p->discounted_amount,

        'is_new' => $p->created_at ? $p->created_at->gt(now()->subDays(30)) : false,
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

        // Home banners for slider
$banners = Homebanner::where('status', 1)
    ->with('media')
    ->orderBy('created_at', 'desc')
    ->get()
    ->map(function ($b) {
        $img = optional($b->getFirstMedia('Home_Banner_Image'))->getUrl();

        return [
            'src' => $img,          // image URL
            'alt' => $b->name,      // alt text
            'caption' => [
                'title' => $b->name,  // banner title
                'text'  => null,      // no description (you said you don't want it)
            ],
            // 'link' => null,       // optional: add a landing URL later if you want
        ];
    })
    ->filter(fn ($b) => !empty($b['src'])) // only keep ones that actually have an image
    ->values();


   $specialOffers = Product::where('status', 1)
        ->where('discount_status', 1)
        ->with('media')
        ->orderBy('created_at', 'desc')
        ->take(12) // how many to show in slider
        ->get()
        ->map(function ($p) {
            $thumb = optional($p->getFirstMedia('product_images'))->getUrl();

            $basePrice = $p->price ?? 0;
            $oldPrice  = $basePrice;   // original price
            $currentPrice  = $basePrice;
            $discountLabel = null;

            if ($p->discount_status && $p->discount_type && $p->discounted_amount > 0) {
                if ($p->discount_type === 'percent') {
                    // percentage discount
                    $currentPrice = max(0, $basePrice * (1 - ($p->discounted_amount / 100)));
                    // label like "DISCOUNT 10%"
                    $discountLabel = 'DISCOUNT ' . rtrim(rtrim($p->discounted_amount, '0'), '.') . '%';
                } elseif ($p->discount_type === 'amount') {
                    // fixed amount discount
                    $currentPrice = max(0, $basePrice - $p->discounted_amount);

                    // optional: compute percent for label
                    if ($basePrice > 0) {
                        $percent = round(($p->discounted_amount / $basePrice) * 100);
                        $discountLabel = 'DISCOUNT ' . $percent . '%';
                    } else {
                        $discountLabel = 'SAVE Rs. ' . number_format($p->discounted_amount, 2);
                    }
                }
            }

            return [
                'id'            => $p->id,
                'name'          => $p->name,
                'slug'          => $p->slug,
                'image'         => $thumb,
                'price'         => round($currentPrice, 2),       // current (discounted) price
                'oldPrice'      => $oldPrice > $currentPrice
                                    ? round($oldPrice, 2)
                                    : null,                       // only show old price if higher
                'discountLabel' => $discountLabel,                 // e.g. "DISCOUNT 10%"
            ];
        })
        ->values();

    return Inertia::render('Home/index', [
        'categories'    => $categories,
        'brands'        => $brands,
        'attributes'    => $attributes,
        'attributeSets' => $attributeSets,
        'specialOffers' => $specialOffers,   // ← NEW
        'banners'       => $banners,  
    ]);
}
}
