<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeSet;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private function safeCount(callable $fn): int
    {
        try {
            $v = $fn();
            return (int)($v ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function safeArray(callable $fn): array
    {
        try {
            $v = $fn();
            return is_array($v) ? $v : (array)$v;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function index()
    {
        $user = Auth::user();

        // --- Optional UOM model detection ---
        $uomClass = null;
        if (class_exists(\App\Models\Uom::class)) {
            $uomClass = \App\Models\Uom::class;
        } elseif (class_exists(\App\Models\UOM::class)) {
            $uomClass = \App\Models\UOM::class;
        }

        // --- KPI totals (all safe) ---
        $counts = [
            'categories_total'    => $this->safeCount(fn() => Category::count()),
            'categories_active'   => $this->safeCount(fn() => Category::where('status', 1)->count()),
            'categories_inactive' => $this->safeCount(fn() => Category::where('status', 0)->count()),
            'categories_featured' => $this->safeCount(fn() => Category::where('featured', 1)->count()),

            'brands_total'        => $this->safeCount(fn() => Brand::count()),
            'brands_active'       => $this->safeCount(fn() => Brand::where('status', 1)->count()),
            'brands_inactive'     => $this->safeCount(fn() => Brand::where('status', 0)->count()),
            'brands_featured'     => $this->safeCount(fn() => Brand::where('featured', 1)->count()),

            // UOM counts: only if model exists, else 0
            'uoms_total'          => $uomClass ? $this->safeCount(fn() => $uomClass::count()) : 0,
            'uoms_active'         => $uomClass ? $this->safeCount(fn() => $uomClass::where('status', 1)->count()) : 0,
            'uoms_inactive'       => $uomClass ? $this->safeCount(fn() => $uomClass::where('status', 0)->count()) : 0,

            'attributes_total'    => $this->safeCount(fn() => Attribute::count()),
            'attributes_active'   => $this->safeCount(fn() => Attribute::where('status', 1)->count()),
            'attributes_inactive' => $this->safeCount(fn() => Attribute::where('status', 0)->count()),
            'attributes_filterable' => $this->safeCount(fn() => Attribute::where('is_filterable', 1)->count()),
            'attributes_variant'  => $this->safeCount(fn() => Attribute::where('is_variant_option', 1)->count()),

            'attribute_sets_total'    => $this->safeCount(fn() => AttributeSet::count()),
            'attribute_sets_active'   => $this->safeCount(fn() => AttributeSet::where('status', 1)->count()),
            'attribute_sets_inactive' => $this->safeCount(fn() => AttributeSet::where('status', 0)->count()),

            'products_total'      => $this->safeCount(fn() => Product::count()),
            'products_active'     => $this->safeCount(fn() => Product::where('status', 1)->count()),
            'products_inactive'   => $this->safeCount(fn() => Product::where('status', 0)->count()),
            'products_discounted' => $this->safeCount(fn() => Product::where('discount_status', 1)->count()),
        ];

        // --- Products trend: last 12 months (safe) ---
        $end = Carbon::now()->startOfDay();
        $start = (clone $end)->subMonths(11)->startOfMonth();

        $rawMonthly = $this->safeArray(function () use ($start) {
            return Product::query()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
                ->where('created_at', '>=', $start)
                ->groupBy('ym')
                ->orderBy('ym')
                ->pluck('c', 'ym')
                ->toArray();
        });

        $months = [];
        $monthCounts = [];
        for ($i = 0; $i < 12; $i++) {
            $m = (clone $start)->addMonths($i);
            $key = $m->format('Y-m');
            $months[] = $m->format('M Y');
            $monthCounts[] = (int)($rawMonthly[$key] ?? 0);
        }

        // --- Attribute types distribution (safe) ---
        $attrTypes = $this->safeArray(function () {
            return Attribute::query()
                ->select('type', DB::raw('COUNT(*) as c'))
                ->groupBy('type')
                ->orderBy('type')
                ->pluck('c', 'type')
                ->toArray();
        });

        $attrTypeLabels = array_keys($attrTypes);
        $attrTypeCounts = array_map('intval', array_values($attrTypes));

        // --- Top brands by product count (safe) ---
        $topBrands = $this->safeArray(function () {
            return Product::query()
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->selectRaw("COALESCE(brands.title, 'No Brand') as brand_title, COUNT(products.id) as c")
                ->groupBy('brand_title')
                ->orderByDesc('c')
                ->limit(8)
                ->get()
                ->map(fn($r) => ['brand_title' => $r->brand_title, 'c' => (int)$r->c])
                ->toArray();
        });

        // --- Recent products (safe) ---
        $recentProducts = [];
        try {
            $recentProducts = Product::query()
                ->with(['brand:id,title', 'uom:id,name'])
                ->orderByDesc('created_at')
                ->limit(8)
                ->get(['id', 'name', 'sku', 'status', 'price', 'sale_price', 'brand_id', 'uom_id', 'discount_status', 'created_at']);
        } catch (\Throwable $e) {
            // if relations missing, fallback without relations
            $recentProducts = Product::query()
                ->orderByDesc('created_at')
                ->limit(8)
                ->get(['id', 'name', 'sku', 'status', 'price', 'sale_price', 'discount_status', 'created_at']);
        }

        // --- Charts payload (safe defaults) ---
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
            'statusBreakdown' => [
                'labels' => ['Active', 'Inactive'],
                'datasets' => [[
                    'label' => 'Status',
                    'data' => [(int)$counts['products_active'], (int)$counts['products_inactive']],
                ]],
            ],
            'attributeTypes' => [
                'labels' => $attrTypeLabels,
                'datasets' => [[
                    'label' => 'Attributes',
                    'data' => $attrTypeCounts,
                ]],
            ],
            'topBrands' => [
                'labels' => array_map(fn($x) => $x['brand_title'], $topBrands),
                'datasets' => [[
                    'label' => 'Products',
                    'data' => array_map(fn($x) => (int)$x['c'], $topBrands),
                ]],
            ],
        ];

        return Inertia::render('Dashboard/Index', [
            'counts' => $counts,
            'charts' => $charts,
            'recentProducts' => $recentProducts,
        ]);
    }
}
