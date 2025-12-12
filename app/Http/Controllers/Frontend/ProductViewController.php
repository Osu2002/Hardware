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
            ->with(['media', 'brand', 'categories'])
            ->firstOrFail();

        // --- Price + discount calculation (same logic style as in HomeController) ---
        $basePrice      = $product->price ?? 0;
        $oldPrice       = $basePrice;   // original price
        $currentPrice   = $basePrice;
        $discountLabel  = null;

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

            'is_new' => $product->created_at
                ? $product->created_at->gt(now()->subDays(30))
                : false,
        ];

        // --- (Optional) related products from same primary category ---
        $related = Product::where('status', 1)
            ->where('id', '!=', $product->id)
            ->when($product->primary_category_id, function ($q) use ($product) {
                $q->where('primary_category_id', $product->primary_category_id);
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
