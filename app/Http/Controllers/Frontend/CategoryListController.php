<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CategoryListController extends Controller
{
    /**
     * Show products for a single category (12 per page: 3 x 4 grid).
     */
    public function show(Category $category, Request $request)
    {
        if ((int) $category->status !== 1) {
            abort(404);
        }

        // ✅ same as HomeController: category_image
        $category->load('media');

        $categoryBanner = optional($category->getFirstMedia('category_image'))->getUrl();

        /**
         * OPTIONAL FALLBACK:
         * If you also store image path in DB column like $category->image = "categories/banner.jpg"
         * under storage/app/public/categories/banner.jpg
         */
        if (empty($categoryBanner) && !empty($category->image)) {
            $categoryBanner = Str::startsWith($category->image, ['http://', 'https://'])
                ? $category->image
                : Storage::url($category->image); // -> /storage/...
        }

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
                'id'    => $category->id,
                'title' => $category->title,
                'slug'  => $category->slug,
                'image' => $categoryBanner, // ✅ now will pass correctly
            ],
            'products' => $products,
        ]);
    }
}
