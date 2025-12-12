<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeSet;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Uom;
use App\Models\Homebanner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // --- KPI totals ---
        $counts = [
            'categories_total' => Category::count(),
            'categories_active' => Category::where('status', 1)->count(),
            'categories_inactive' => Category::where('status', 0)->count(),
            'categories_featured' => Category::where('featured', 1)->count(),

            'brands_total' => Brand::count(),
            'brands_active' => Brand::where('status', 1)->count(),
            'brands_inactive' => Brand::where('status', 0)->count(),
            'brands_featured' => Brand::where('featured', 1)->count(),

            'uoms_total' => Uom::count(),
            'uoms_active' => Uom::where('status', 1)->count(),
            'uoms_inactive' => Uom::where('status', 0)->count(),

            'attributes_total' => Attribute::count(),
            'attributes_active' => Attribute::where('status', 1)->count(),
            'attributes_inactive' => Attribute::where('status', 0)->count(),
            'attributes_filterable' => Attribute::where('is_filterable', 1)->count(),
            'attributes_variant' => Attribute::where('is_variant_option', 1)->count(),

            'attribute_sets_total' => AttributeSet::count(),
            'attribute_sets_active' => AttributeSet::where('status', 1)->count(),
            'attribute_sets_inactive' => AttributeSet::where('status', 0)->count(),

            'products_total' => Product::count(),
            'products_active' => Product::where('status', 1)->count(),
            'products_inactive' => Product::where('status', 0)->count(),
            'products_discounted' => Product::where('discount_status', 1)->count(),

            // banners KPI (optional)
            'homebanners_total' => Homebanner::count(),
            'homebanners_active' => Homebanner::where('status', 1)->count(),
        ];

        // --- Products trend: last 12 months ---
        $end = Carbon::now()->startOfDay();
        $start = (clone $end)->subMonths(11)->startOfMonth();

        $rawMonthly = Product::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
            ->where('created_at', '>=', $start)
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('c', 'ym')
            ->toArray();

        $months = [];
        $monthCounts = [];
        for ($i = 0; $i < 12; $i++) {
            $m = (clone $start)->addMonths($i);
            $key = $m->format('Y-m');
            $months[] = $m->format('M Y');
            $monthCounts[] = (int)($rawMonthly[$key] ?? 0);
        }

        // --- Attribute type distribution ---
        $attrTypes = Attribute::query()
            ->select('type', DB::raw('COUNT(*) as c'))
            ->groupBy('type')
            ->orderBy('type')
            ->pluck('c', 'type')
            ->toArray();

        $attrTypeLabels = array_keys($attrTypes);
        $attrTypeCounts = array_map('intval', array_values($attrTypes));

        // --- Top brands by product count ---
        $topBrands = Product::query()
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->selectRaw("COALESCE(brands.title, 'No Brand') as brand_title, COUNT(products.id) as c")
            ->groupBy('brand_title')
            ->orderByDesc('c')
            ->limit(8)
            ->get();

        // --- Recent products ---
        $recentProducts = Product::query()
            ->with(['brand:id,title', 'uom:id,name'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(['id', 'name', 'sku', 'status', 'price', 'sale_price', 'brand_id', 'uom_id', 'discount_status', 'created_at']);

        // --- Status breakdown ---
        $statusBreakdown = [
            'labels' => ['Active', 'Inactive'],
            'datasets' => [[
                'label' => 'Status',
                'data' => [(int)$counts['products_active'], (int)$counts['products_inactive']],
            ]],
        ];

        $charts = [
            'productsTrend' => [
                'labels' => $months,
                'datasets' => [[
                    'label' => 'Products Created',
                    'data' => $monthCounts,
                    'fill' => false,
                    'tension' => 0.35,
                ]],
            ],
            'statusBreakdown' => $statusBreakdown,
            'attributeTypes' => [
                'labels' => $attrTypeLabels,
                'datasets' => [[
                    'label' => 'Attributes',
                    'data' => $attrTypeCounts,
                ]],
            ],
            'topBrands' => [
                'labels' => $topBrands->pluck('brand_title')->values(),
                'datasets' => [[
                    'label' => 'Products',
                    'data' => $topBrands->pluck('c')->map(fn($x) => (int)$x)->values(),
                ]],
            ],
        ];

        // --- Home Banners (load all, newest first) ---
        $homebanners = Homebanner::with('media')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'name' => $b->name,
                    'status' => (int)$b->status,
                    'image_url' => $b->getFirstMediaUrl('Home_Banner_Image') ?: null,
                    'created_at' => $b->created_at,
                ];
            });

        return Inertia::render('Dashboard/Index', [
            'counts' => $counts,
            'charts' => $charts,
            'recentProducts' => $recentProducts,
            'homebanners' => $homebanners,
        ]);
    }
}
