<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryListController extends Controller
{
    /**
     * Show products for a single category (12 per page: 3 x 4 grid).
     */
    public function show(Category $category, Request $request)
    {
        // Ensure category is active; otherwise 404
        if ((int) $category->status !== 1) {
            abort(404);
        }

        // Products that belong to this category
        $products = Product::where('status', 1)
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->with('media')
            ->orderBy('created_at', 'desc')
            ->paginate(12) // 3 cards x 4 rows = 12 per page
            ->withQueryString()
            ->through(function ($p) {
                $thumb = optional($p->getFirstMedia('product_images'))->getUrl();
                $basePrice = $p->price ?? 0;
                $current   = $p->sale_price ?? $basePrice;

                return [
                    'id'            => $p->id,
                    'name'          => $p->name,
                    'slug'          => $p->slug,
                    'image'         => $thumb,
                    'price'         => round($current, 2),
                    'regular_price' => $p->price,
                    'sale_price'    => $p->sale_price,
                ];
            });

        return Inertia::render('CategoryListView/index', [
            'category' => [
                'id'    => $category->id,
                'title' => $category->title,
                'slug'  => $category->slug, // can be null; just pass through
            ],
            'products' => $products,
        ]);
    }
}
