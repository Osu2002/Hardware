<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;

class ProductViewController extends Controller
{
    public function show(string $slug)
    {
        // Fetch product by slug
        $product = Product::where('slug', $slug)
            ->where('status', 1)
            ->with(['media', 'brand', 'categories', 'attributeSet.attributes',])
            ->firstOrFail();

        // --- Price + discount calculation (same logic style as in HomeController) ---
        $basePrice      = $product->price ?? 0;
        $oldPrice       = $basePrice;   // original price
        $currentPrice   = $basePrice;
        $discountLabel  = null;
        $values = $product->attributes_json ?? [];

$attrs = collect();
if ($product->attributeSet && $product->attributeSet->attributes) {
    $attrs = $product->attributeSet->attributes
        ->sortBy(fn($a) => (int)($a->pivot->sort_order ?? 0))
        ->values();
}

$displayAttributes = $attrs->map(function ($a) use ($values) {
    $code = (string) $a->code;

    if (!array_key_exists($code, $values)) return null;

    $val = $values[$code];
    if ($val === null || $val === '') return null;

    // basic formatting
    if ($a->type === 'boolean') {
        $val = ((int)$val === 1) ? 'Yes' : 'No';
    }

    return [
        'code'  => $code,
        'label' => (string) $a->name,
        'value' => $val,
        'unit'  => $a->unit,
        'type'  => (string) $a->type,
    ];
})->filter()->values();


$brandLogo = null;

// Option 1: Brand uses Spatie MediaLibrary (recommended)
// collection name example: 'brand_logo' (change if yours differs)
if ($product->brand && method_exists($product->brand, 'getFirstMediaUrl')) {
    $brandLogo = $product->brand->getFirstMediaUrl('brand_logo') ?: null;
}

// Option 2: Brand has a DB column like `logo` or `logo_url`
if (!$brandLogo && $product->brand) {
    $brandLogo = $product->brand->logo_url ?? $product->brand->logo ?? null;
}


        if ($product->discount_status && $product->discount_type && $product->discounted_amount > 0) {
            if ($product->discount_type === 'percent') {
                $currentPrice = max(0, $basePrice * (1 - ($product->discounted_amount / 100)));
                $discountLabel = 'DISCOUNT ' . rtrim(rtrim($product->discounted_amount, '0'), '.') . '%';
            } elseif ($product->discount_type === 'amount') {
                $currentPrice = max(0, $basePrice - $product->discounted_amount);

                if ($basePrice > 0) {
                    $percent = round(($product->discounted_amount / $basePrice) * 100);
                    $discountLabel = 'DISCOUNT ' . $percent . '%';
                } else {
                    $discountLabel = 'SAVE Rs. ' . number_format($product->discounted_amount, 2);
                }
            }
        }

        // --- Media / gallery ---
        $primaryImage = optional($product->getFirstMedia('product_images'))->getUrl();

        $gallery = $product->getMedia('product_images')
    ->map(function ($media) {
        return [
            'id'  => $media->id,
            'url' => $media->getUrl(), // only original URL, no 'thumb'
        ];
    })
    ->values();


        // --- Main product payload for Vue ---
        $productPayload = [
            'id'    => $product->id,
            'name'  => $product->name,
            'slug'  => $product->slug,

            'brand' => optional($product->brand)->title,
             'brandLogo' => $brandLogo, 

            'categories' => $product->categories
                ->map(fn ($c) => [
                    'id'    => $c->id,
                    'title' => $c->title,
                    'slug'  => $c->slug,
                ])
                ->values(),

            'primaryImage' => $primaryImage,
            'gallery'      => $gallery,

            'price'         => round($currentPrice, 2),
            'oldPrice'      => $oldPrice > $currentPrice ? round($oldPrice, 2) : null,
            'discountLabel' => $discountLabel,

            'short_description' => $product->short_description,
            'description'       => $product->description,

            'attributes' => $displayAttributes,
    'inStock'        => $product->in_stock,         // 1 / 0 / null
    'stockCount'     => $product->stock_count,      // int / null
    'warrantyPeriod' => $product->warranty_period,  // int / null
    'warrantyType'   => $product->warranty_type, 

            'is_new' => $product->created_at
                ? $product->created_at->gt(now()->subDays(30))
                : false,
        ];

       $categoryIds = $product->categories->pluck('id')->all();

$related = Product::where('status', 1)
    ->where('id', '!=', $product->id)
    ->when(!empty($categoryIds), function ($q) use ($categoryIds) {
        $q->whereHas('categories', fn ($cq) => $cq->whereIn('categories.id', $categoryIds));
    }, function ($q) {
        // no category => no related
        $q->whereRaw('1=0');
    })
    ->with('media')
    ->orderBy('created_at', 'desc')
    ->take(8)
    ->get()
    ->map(function ($p) {
        $thumb = optional($p->getFirstMedia('product_images'))->getUrl();
        $base  = $p->price ?? 0;
        $current = $p->sale_price ?? $base;

        return [
            'id'    => $p->id,
            'name'  => $p->name,
            'slug'  => $p->slug,
            'image' => $thumb,
            'price' => round($current, 2),
        ];
    })
    ->values();


        return Inertia::render('CategoryProductView/index', [
            'product'         => $productPayload,
            'relatedProducts' => $related,
        ]);
    }
}
