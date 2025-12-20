<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class SpecialOffersController extends Controller
{
    public function index(Request $request)
    {
        // ✅ same filter behavior as Featured (sub, brands[], sort, per_page)
        $filters = [
            'sub'      => $request->input('sub'),
            'brands'   => $request->input('brands', []),
            'sort'     => $request->input('sort', 'latest'),
            'per_page' => (int) $request->input('per_page', 16),
        ];

        $perPage = $filters['per_page'] > 0 ? min($filters['per_page'], 48) : 16;

        $query = Product::query();

        // ✅ Only active products (if column exists)
        if (Schema::hasColumn('products', 'status')) {
            $query->where('status', 1);
        }

        // ✅ ONLY discounted products using YOUR columns
        // discount_status = 1 AND discounted_amount > 0
        if (Schema::hasColumn('products', 'discount_status')) {
            $query->where('discount_status', 1);
        }
        if (Schema::hasColumn('products', 'discounted_amount')) {
            $query->where('discounted_amount', '>', 0);
        }

        // ✅ Subcategory filter (same as featured UI: single sub id)
        $sub = $filters['sub'];
        if ($sub && Schema::hasColumn('products', 'subcategory_id')) {
            $query->where('subcategory_id', (int) $sub);
        }

        // ✅ Brand filter (same as featured UI: brands[] multiple)
        $brands = $filters['brands'];
        if (is_array($brands) && count($brands) && Schema::hasColumn('products', 'brand_id')) {
            $brandIds = array_values(array_filter(array_map('intval', $brands)));
            if (count($brandIds)) {
                $query->whereIn('brand_id', $brandIds);
            }
        }

        // ✅ Sorting (matches featured filter values)
        $sort = $filters['sort'] ?: 'latest';

        if ($sort === 'price_asc' && Schema::hasColumn('products', 'price')) {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc' && Schema::hasColumn('products', 'price')) {
            $query->orderBy('price', 'desc');
        } else {
            if (Schema::hasColumn('products', 'created_at')) {
                $query->latest('created_at');
            } else {
                $query->latest();
            }
        }

        // ✅ Sidebar data
       // ✅ Sidebar filter data: ONLY subcategories/brands that have offers

$offersBase = Product::query();

// only active (if exists)
if (Schema::hasColumn('products', 'status')) {
    $offersBase->where('status', 1);
}

// only discounted (your real columns)
if (Schema::hasColumn('products', 'discount_status')) {
    $offersBase->where('discount_status', 1);
}
if (Schema::hasColumn('products', 'discounted_amount')) {
    $offersBase->where('discounted_amount', '>', 0);
}

// --- Subcategories that have offers (respect current brand filter) ---
$subIds = collect([]);
if (Schema::hasColumn('products', 'subcategory_id')) {
    $q = (clone $offersBase);

    // if user selected brands, limit subcategories to those brands
    $brands = $filters['brands'];
    if (Schema::hasColumn('products', 'brand_id') && is_array($brands) && count($brands)) {
        $brandIds = array_values(array_filter(array_map('intval', $brands)));
        if (count($brandIds)) $q->whereIn('brand_id', $brandIds);
    }

    $subIds = $q->whereNotNull('subcategory_id')
        ->distinct()
        ->pluck('subcategory_id');
}

$subcategories = class_exists(Subcategory::class)
    ? Subcategory::query()
        ->select('id', 'title')
        ->when($subIds->count(), fn($qq) => $qq->whereIn('id', $subIds))
        ->orderBy('title')
        ->get()
    : collect([]);

// --- Brands that have offers (respect current subcategory filter) ---
$brandIds = collect([]);
if (Schema::hasColumn('products', 'brand_id')) {
    $q = (clone $offersBase);

    // if user selected subcategory, limit brands to that subcategory
    $sub = $filters['sub'];
    if ($sub && Schema::hasColumn('products', 'subcategory_id')) {
        $q->where('subcategory_id', (int) $sub);
    }

    $brandIds = $q->whereNotNull('brand_id')
        ->distinct()
        ->pluck('brand_id');
}

$brandsList = class_exists(Brand::class)
    ? Brand::query()
        ->select('id', 'title')
        ->when($brandIds->count(), fn($qq) => $qq->whereIn('id', $brandIds))
        ->orderBy('title')
        ->get()
    : collect([]);


        // ✅ paginate
        $products = $query->paginate($perPage)->withQueryString();

        // ✅ map products to EXACT format used by FeaturedProductList.vue
        $products->through(function ($p) {
            $thumb = null;

            // spatie media
            if (method_exists($p, 'getFirstMedia')) {
                $thumb = optional($p->getFirstMedia('product_images'))->getUrl();
            }

            $basePrice = (float) ($p->price ?? 0);
            $currentPrice = $basePrice;
            $discountPercent = 0;

            $discountStatus = (int) ($p->discount_status ?? 0);
            $discountType   = (string) ($p->discount_type ?? '');
            $discountAmount = (float) ($p->discounted_amount ?? 0);

            if ($discountStatus === 1 && $discountType && $discountAmount > 0) {
                if ($discountType === 'percent') {
                    $discountPercent = (int) round($discountAmount);
                    $currentPrice = max(0, $basePrice * (1 - ($discountPercent / 100)));
                } elseif ($discountType === 'amount') {
                    $currentPrice = max(0, $basePrice - $discountAmount);
                    $discountPercent = $basePrice > 0 ? (int) round(($discountAmount / $basePrice) * 100) : 0;
                }
            }

            return [
                'id'               => $p->id,
                'name'             => $p->name,
                'slug'             => $p->slug,
                'image'            => $thumb,
                'regular_price'    => round($basePrice, 2),
                'price'            => round($currentPrice, 2),
                'discount_percent' => $discountPercent,
                'discount_status'  => $discountStatus,
                'discount_type'    => $discountType,
                'discounted_amount'=> $discountAmount,
                'is_new'           => $p->created_at ? $p->created_at->gt(now()->subDays(30)) : false,
            ];
        });

        return Inertia::render('Specialoffers/Index', [
            'page' => ['title' => 'Special Offers'],
            'products' => $products,
            'subcategories' => $subcategories,
            'brands' => $brandsList,
            'filters' => [
                'sub' => $filters['sub'] ? (int) $filters['sub'] : null,
                'brands' => is_array($filters['brands']) ? $filters['brands'] : [],
                'sort' => $filters['sort'],
                'per_page' => $perPage,
            ],
        ]);
    }
}
