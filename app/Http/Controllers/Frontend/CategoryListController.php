<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryListController extends Controller
{
    public function show(Category $category, Request $request)
    {
        if ((int) $category->status !== 1) {
            abort(404);
        }

        $category->load('media');

        // ✅ Use banner collection ONLY
        $banner = optional($category->getFirstMedia('category_banner'))->getUrl();

        $products = Product::where('status', 1)
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->with('media')
            ->orderBy('created_at', 'desc')
            ->paginate(12)
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
                'id'     => $category->id,
                'title'  => $category->title,
                'slug'   => $category->slug,
                'banner' => $banner, // ✅ IMPORTANT
            ],
            'products' => $products,
        ]);
    }
}
