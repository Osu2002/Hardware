<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class FeaturedProductListController extends Controller
{
    public function index(Request $request)
    {
        // ✅ per page: default 16 (desktop), UI will send 8 for mobile/tablet
        $perPage = (int) $request->input('per_page', 16);
        $perPage = in_array($perPage, [8, 16], true) ? $perPage : 16;

        // Filters
        $subId = $request->filled('sub') ? (int) $request->input('sub') : null;

        $brandIds = array_values(array_filter(
            Arr::wrap($request->input('brands', [])),
            fn ($v) => is_numeric($v) && (int) $v > 0
        ));
        $brandIds = array_map('intval', $brandIds);

        $sort = $request->input('sort', 'latest'); // latest | price_asc | price_desc

        // Base query: featured + active
        $q = Product::query()
            ->where('status', 1)
            ->with(['media']);

        // featured column safety
        if (Schema::hasColumn('products', 'isfeatured')) {
            $q->where('isfeatured', 1);
        } else {
            // if column not exists yet, show empty list
            $q->whereRaw('1=0');
        }

        // Subcategory filter
        if ($subId) {
            if (Schema::hasColumn('products', 'subcategory_id')) {
                $q->where('subcategory_id', $subId);
            } elseif (Schema::hasColumn('products', 'sub_category_id')) {
                $q->where('sub_category_id', $subId);
            }
        }

        // Brand filter
        if (!empty($brandIds) && Schema::hasColumn('products', 'brand_id')) {
            $q->whereIn('brand_id', $brandIds);
        }

        // price ordering by "effective price"
        $hasSale = Schema::hasColumn('products', 'sale_price');
        $hasDiscountCols =
            Schema::hasColumn('products', 'discount_status') &&
            Schema::hasColumn('products', 'discount_type') &&
            Schema::hasColumn('products', 'discounted_amount');

        $orderExpr = 'price';
        if ($hasSale) {
            $orderExpr = "COALESCE(NULLIF(sale_price, 0), price)";
        } elseif ($hasDiscountCols) {
            $orderExpr = "
                CASE
                    WHEN discount_status = 1 AND discount_type = 'percent'
                        THEN GREATEST(0, price * (1 - (discounted_amount / 100)))
                    WHEN discount_status = 1 AND discount_type = 'amount'
                        THEN GREATEST(0, price - discounted_amount)
                    ELSE price
                END
            ";
        }

        if ($sort === 'price_asc') {
            $q->orderByRaw("($orderExpr) asc");
        } elseif ($sort === 'price_desc') {
            $q->orderByRaw("($orderExpr) desc");
        } else {
            $q->orderBy('created_at', 'desc');
        }

        $products = $q->paginate($perPage)->withQueryString()->through(function ($p) use ($hasSale, $hasDiscountCols) {
            $thumb = optional($p->getFirstMedia('product_images'))->getUrl();

            $basePrice = (float) ($p->price ?? 0);
            $currentPrice = $basePrice;
            $discountPercent = 0;

            if ($hasSale && !empty($p->sale_price) && (float)$p->sale_price > 0 && (float)$p->sale_price < $basePrice) {
                $currentPrice = (float)$p->sale_price;
                $discountPercent = $basePrice > 0 ? (int) round((($basePrice - $currentPrice) / $basePrice) * 100) : 0;
            } elseif ($hasDiscountCols && (int)$p->discount_status === 1 && $p->discount_type && (float)$p->discounted_amount > 0) {
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

                'regular_price'    => round($basePrice, 2),
                'price'            => round($currentPrice, 2),
                'discount_percent' => $discountPercent,
            ];
        });

        // Sidebar: subcategories for featured products
        $subcategories = collect([]);
        if (Schema::hasColumn('products', 'subcategory_id')) {
            $subIds = Product::where('status', 1)
                ->when(Schema::hasColumn('products', 'isfeatured'), fn($qq) => $qq->where('isfeatured', 1))
                ->whereNotNull('subcategory_id')
                ->distinct()
                ->pluck('subcategory_id')
                ->filter()
                ->values();

            if ($subIds->count()) {
                $subcategories = SubCategory::where('status', 1)
                    ->whereIn('id', $subIds)
                    ->orderBy('sort_order')
                    ->orderBy('title')
                    ->get(['id', 'title', 'slug'])
                    ->map(fn($s) => [
                        'id' => $s->id,
                        'title' => $s->title,
                        'slug' => $s->slug,
                    ])->values();
            }
        }

        // Sidebar: brands for featured products
        $brands = collect([]);
        if (Schema::hasColumn('products', 'brand_id')) {
            $brandIdsInFeatured = Product::where('status', 1)
                ->when(Schema::hasColumn('products', 'isfeatured'), fn($qq) => $qq->where('isfeatured', 1))
                ->whereNotNull('brand_id')
                ->distinct()
                ->pluck('brand_id')
                ->filter()
                ->values();

            if ($brandIdsInFeatured->count()) {
                $brands = Brand::where('status', 1)
                    ->whereIn('id', $brandIdsInFeatured)
                    ->orderBy('title')
                    ->get()
                    ->map(fn($b) => [
                        'id' => $b->id,
                        'title' => $b->title,
                        'slug' => $b->slug,
                    ])->values();
            }
        }

        return Inertia::render('Featuredproducts/index', [
            'page' => [
                'title' => 'Featured Products',
            ],
            'products' => $products,
            'subcategories' => $subcategories,
            'brands' => $brands,
            'filters' => [
                'sub' => $subId,
                'brands' => $brandIds,
                'sort' => $sort,
                'per_page' => $perPage,
            ],
        ]);
    }
}
