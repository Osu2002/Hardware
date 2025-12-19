<?php

namespace App\Http\Controllers;

use App\Models\AttributeSet;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;
use App\Models\SubCategory;


class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Product/Index');
    }

    public function getData()
    {
        $rows = Product::with(['brand:id,title', 'uom:id,name'])
            ->select(['id', 'name', 'sku', 'status', 'price', 'sale_price', 'brand_id', 'uom_id', 'sort_order', 'created_at']);

        return DataTables::of($rows)
            ->addColumn(
                'check',
                fn($row) =>
                '<div class="custom-control custom-checkbox item-check">
                   <input type="checkbox" class="form-check-input" id="' . $row->id . '" value="' . $row->id . '">
                   <label class="form-check-label" for="' . $row->id . '"></label>
                 </div>'
            )
            ->addColumn('brand', fn($r) => $r->brand?->title ?? '-')
            ->addColumn('uom', fn($r) => $r->uom?->name ?? '-')
            ->addColumn('status', function ($row) {
                if ($row->status == 1 && !$row->deleted_at) return '<span class="badge bg-success">Active</span>';
                if ($row->status == 0 && !$row->deleted_at) return '<span class="badge bg-warning">Inactive</span>';
                if ($row->deleted_at) return '<span class="badge bg-danger">Suspended</span>';
                return '';
            })
            ->addColumn('action', function ($row) {
                $html = '';
                $html .= '<a class="dropdown-item action_edit" style="font-size:14px;padding:5px 13px;" data-item-id="' . $row->id . '" href="javascript:void(0)"><i class="fas fa-edit mr-2"></i> View / Edit</a>';
                $html .= '<a class="dropdown-item ' . ($row->status == 1 ? 'text-warning' : 'text-success') . ' action_status_change" style="font-size:14px;padding:5px 13px;" data-item-id="' . $row->id . '" data-status="' . $row->status . '" href="javascript:void(0)"><i class="fas fa-power-off mr-2"></i>' . ($row->status == 1 ? ' Deactivate' : ' Activate') . '</a>';
                $html .= '<div class="dropdown-divider"></div>';
                $html .= '<a class="dropdown-item text-danger action_delete" data-bs-toggle="modal" data-bs-target="#deleteConfirm" style="font-size:14px;padding:5px 13px;" data-item-id="' . $row->id . '" href="javascript:void(0)"><i class="fas fa-trash mr-2"></i> Delete</a>';

                return '<div class="btn-group">
                          <button type="button" class="btn btn-main btn-sm dropdown-toggle" data-bs-toggle="dropdown">Action</button>
                          <div class="dropdown-menu" style="min-width:10rem;">' . $html . '</div>
                        </div>';
            })
            ->rawColumns(['check', 'status', 'action'])
            ->make(true);
    }

    /** Generate unique random SKU (server side safety) */
    private function generateUniqueSku(int $len = 8): string
    {
        do {
            // example: HW-8F3K2ZQ1
            $sku = 'HW-' . strtoupper(Str::random($len));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    /** Attributes for a set incl pivot + options */
    private function attributesForSet(?int $setId): array
    {
        if (!$setId) return [];

        $set = AttributeSet::with([
            'attributes' => function ($q) {
                $q->select('attributes.id', 'attributes.code', 'attributes.name', 'attributes.type', 'attributes.unit')
                    ->with(['options' => fn($oq) => $oq->orderBy('sort_order')]);
            }
        ])->find($setId);

        if (!$set) return [];

        return $set->attributes
            ->sortBy(fn($a) => (int)($a->pivot->sort_order ?? 0))
            ->values()
            ->map(function ($a) {
                return [
                    'id'          => $a->id,
                    'code'        => $a->code,   // ✅ JSON key will be this
                    'name'        => $a->name,
                    'type'        => $a->type,
                    'unit'        => $a->unit,
                    'is_required' => (bool)($a->pivot->is_required ?? false),
                    'sort_order'  => (int)($a->pivot->sort_order ?? 0),
                    'options'     => $a->type === 'select'
                        ? $a->options->map(fn($o) => ['id' => $o->id, 'value' => $o->value, 'hex' => $o->hex])->values()->toArray()
                        : [],
                ];
            })
            ->toArray();
    }

    /** Build + validate JSON {code:value} */
    private function buildAttributesJsonOrFail(?int $setId, array $attributesMap): array
    {
        if (!$setId) return [];

        $set = AttributeSet::with(['attributes.options'])->findOrFail($setId);

        $incomingById = collect($attributesMap)
            ->filter(fn($r) => !empty($r['attribute_id']))
            ->mapWithKeys(fn($r) => [(int)$r['attribute_id'] => ($r['value'] ?? null)]);

        $errors = [];
        $json = [];

        foreach ($set->attributes as $attr) {
            $code = (string)$attr->code;   // ✅ keys become attribute.code
            $name = (string)$attr->name;
            $type = (string)$attr->type;

            $value = $incomingById->get((int)$attr->id, null);

            if ($attr->pivot?->is_required && ($value === null || $value === '')) {
                $errors['attr_' . $code] = $name . ' is required.';
                continue;
            }

            if ($value === null || $value === '') continue;

            if ($type === 'number') {
                if (!is_numeric($value)) {
                    $errors['attr_' . $code] = $name . ' must be a number.';
                    continue;
                }
                $value = (float)$value;
            }

            if ($type === 'boolean') {
                $value = ($value === 1 || $value === '1' || $value === true) ? 1 : 0;
            }

            if ($type === 'select') {
                $allowed = $attr->options->pluck('value')->all();
                if (!in_array($value, $allowed, true)) {
                    $errors['attr_' . $code] = $name . ' has an invalid option.';
                    continue;
                }
            }

            if ($type === 'color') {
                $v = (string)$value;
                if ($v && $v[0] !== '#') $v = '#' . $v;
                $value = $v;
            }

            $json[$code] = $value;
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $json;
    }

    public function create(Request $r)
    {
        $setId = $r->integer('attribute_set_id');
        

        return Inertia::render('Product/CreateUpdate', [
            'brands'        => Brand::where('status', 1)->orderBy('title')->get(['id', 'title']),
            'uoms'          => Uom::where('status', 1)->orderBy('name')->get(['id', 'name']),
            'categories'    => Category::orderBy('title')->get(['id', 'title']),
            'attributeSets' => AttributeSet::where('status', 1)->orderBy('name')->get(['id', 'name']),
            'attributes'    => $this->attributesForSet($setId),
            'images'        => [],
            'subcategories' => SubCategory::where('status', 1)
    ->orderBy('title')
    ->get(['id','title','category_id']),

        ]);
    }

    public function store(Request $r)
    {
        $r->validate([
            'name' => ['required', 'max:180'],
            'sku'  => ['nullable', 'max:120', Rule::unique('products', 'sku')], // ✅ allow auto
            'status' => ['required', 'in:0,1'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'uom_id' => ['nullable', 'exists:uoms,id'],
            'attribute_set_id' => ['nullable', 'exists:attribute_sets,id'],
           
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'short_description' => ['nullable', 'max:500'],
            'description' => ['nullable'],
            'attributes_map' => ['nullable', 'array'],
            'images.*' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
            'discount_status' => ['required', 'in:0,1'],
            'discount_type' => ['required_if:discount_status,1', 'nullable', 'in:percent,amount'],
            'discounted_amount' => ['required_if:discount_status,1', 'nullable', 'numeric', 'min:0'],
            'in_stock'        => ['nullable', 'in:0,1'],
'stock_count'     => ['nullable', 'integer', 'min:0'],
'warranty_period' => ['nullable', 'integer', 'min:0'],
'warranty_type'   => ['nullable', 'max:120'],


            'category_id' => ['nullable', 'exists:categories,id'],
'subcategory_id' => [
    'nullable',
    Rule::exists('subcategories', 'id')
        ->where(fn($q) => $q->where('category_id', $r->category_id)),
],

        ]);

        DB::beginTransaction();
        try {
            $slug = Str::slug($r->name);
            if (Product::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::random(5);
            }

            $sku = $r->sku ?: $this->generateUniqueSku(); // ✅ server side auto SKU

            $attributesJson = $this->buildAttributesJsonOrFail(
                $r->attribute_set_id ? (int)$r->attribute_set_id : null,
                $r->attributes_map ?? []
            );

            $inStock = $r->input('in_stock');
$inStock = ($inStock === '' || $inStock === null) ? null : (int)$inStock;

$stockCount = $r->input('stock_count');
$stockCount = ($stockCount === '' || $stockCount === null) ? null : (int)$stockCount;

$warrantyPeriod = $r->input('warranty_period');
$warrantyPeriod = ($warrantyPeriod === '' || $warrantyPeriod === null) ? null : (int)$warrantyPeriod;

$warrantyType = $r->input('warranty_type');
$warrantyType = ($warrantyType === '' || $warrantyType === null) ? null : $warrantyType;


            $product = Product::create([
                'name' => $r->name,
                'slug' => $slug,
                'sku'  => $sku,
                'status' => $r->status,
                'sort_order' => (int)($r->sort_order ?? 0),
                'brand_id' => $r->brand_id,
                'uom_id' => $r->uom_id,
                'attribute_set_id' => $r->attribute_set_id,
                'attributes_json'  => $attributesJson, // ✅ {code:value}
                // 'primary_category_id' => $r->primary_category_id,
                'price' => $r->price,
                'sale_price' => $r->sale_price,
                'short_description' => $r->short_description,
                'description' => $r->description,
                'discount_status'   => (int)($r->discount_status ?? 0),
                'discount_type'     => $r->discount_status ? $r->discount_type : null,
                'discounted_amount' => $r->discount_status ? $r->discounted_amount : null,
                'subcategory_id' => $r->subcategory_id,
                'in_stock'        => $inStock,
'stock_count'     => $stockCount,
'warranty_period' => $warrantyPeriod,
'warranty_type'   => $warrantyType,


            ]);

           $product->categories()->sync($r->category_id ? [(int)$r->category_id] : []);


            if ($r->hasFile('images')) {
                foreach ($r->file('images', []) as $img) {
                    $product->addMedia($img)->toMediaCollection('product_images');
                }
            }

            DB::commit();
            return redirect()->route('product.index');
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
public function edit(Request $r, $id)
{
    $product = Product::with(['categories:id,title'])->findOrFail($id);

    $setId = $r->integer('attribute_set_id') ?: (int)$product->attribute_set_id;

    return Inertia::render('Product/CreateUpdate', [
        'product'       => $product,
        'brands'        => Brand::where('status', 1)->orderBy('title')->get(['id', 'title']),
        'uoms'          => Uom::where('status', 1)->orderBy('name')->get(['id', 'name']),
        'categories'    => Category::orderBy('title')->get(['id', 'title']),
        'subcategories' => SubCategory::where('status', 1)
            ->orderBy('title')
            ->get(['id','title','category_id']),
        'attributeSets' => AttributeSet::where('status', 1)->orderBy('name')->get(['id', 'name']),
        'attributes'    => $this->attributesForSet($setId),

        'images' => $product->getMedia('product_images')->map(fn($m) => [
            'id'  => $m->id,
            'url' => $m->getUrl(),
        ])->values(),
    ]);
}

public function destroyImage(Product $product, $media)
{
    $mediaItem = $product->media()
        ->where('id', $media)
        ->where('collection_name', 'product_images')
        ->firstOrFail();

    $mediaItem->delete(); // ✅ deletes db row + file

    return back(); // Inertia will refresh the edit page
}
    public function update(Request $r)
    {
        $r->validate([
            'id' => ['required', 'exists:products,id'],
            'name' => ['required', 'max:180'],
            'sku'  => ['nullable', 'max:120', Rule::unique('products', 'sku')->ignore($r->id)], // ✅ allow auto
            'status' => ['required', 'in:0,1'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'uom_id' => ['nullable', 'exists:uoms,id'],
            'attribute_set_id' => ['nullable', 'exists:attribute_sets,id'],
           
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'short_description' => ['nullable', 'max:500'],
            'description' => ['nullable'],
            'attributes_map' => ['nullable', 'array'],
            'images.*' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
            'discount_status' => ['required', 'in:0,1'],
            'discount_type' => ['required_if:discount_status,1', 'nullable', 'in:percent,amount'],
            'discounted_amount' => ['required_if:discount_status,1', 'nullable', 'numeric', 'min:0'],
            'in_stock'        => ['nullable', 'in:0,1'],
'stock_count'     => ['nullable', 'integer', 'min:0'],
'warranty_period' => ['nullable', 'integer', 'min:0'],
'warranty_type'   => ['nullable', 'max:120'],

            'category_id' => ['nullable', 'exists:categories,id'],
'subcategory_id' => [
    'nullable',
    Rule::exists('subcategories', 'id')
        ->where(fn($q) => $q->where('category_id', $r->category_id)),
],

        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($r->id);

            $sku = $r->sku ?: ($product->sku ?: $this->generateUniqueSku());

            $attributesJson = $this->buildAttributesJsonOrFail(
                $r->attribute_set_id ? (int)$r->attribute_set_id : null,
                $r->attributes_map ?? []
            );
            $inStock = $r->input('in_stock');
$inStock = ($inStock === '' || $inStock === null) ? null : (int)$inStock;

$stockCount = $r->input('stock_count');
$stockCount = ($stockCount === '' || $stockCount === null) ? null : (int)$stockCount;

$warrantyPeriod = $r->input('warranty_period');
$warrantyPeriod = ($warrantyPeriod === '' || $warrantyPeriod === null) ? null : (int)$warrantyPeriod;

$warrantyType = $r->input('warranty_type');
$warrantyType = ($warrantyType === '' || $warrantyType === null) ? null : $warrantyType;


            $product->update([
                'name' => $r->name,
                'sku'  => $sku,
                'status' => $r->status,
                'sort_order' => (int)($r->sort_order ?? 0),
                'brand_id' => $r->brand_id,
                'uom_id' => $r->uom_id,
                'attribute_set_id' => $r->attribute_set_id,
                'attributes_json'  => $attributesJson, // ✅ {code:value}
                // 'primary_category_id' => $r->primary_category_id,
                'price' => $r->price,
                'sale_price' => $r->sale_price,
                'short_description' => $r->short_description,
                'description' => $r->description,
                'discount_status'   => (int)($r->discount_status ?? 0),
                'discount_type'     => $r->discount_status ? $r->discount_type : null,
                'discounted_amount' => $r->discount_status ? $r->discounted_amount : null,
                'subcategory_id' => $r->subcategory_id,
                'in_stock'        => $inStock,
'stock_count'     => $stockCount,
'warranty_period' => $warrantyPeriod,
'warranty_type'   => $warrantyType,


            ]);

          $product->categories()->sync($r->category_id ? [(int)$r->category_id] : []);


            if ($r->hasFile('images')) {
                foreach ($r->file('images', []) as $img) {
                    $product->addMedia($img)->toMediaCollection('product_images');
                }
            }

            DB::commit();
            return redirect()->route('product.index');
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function destroy(Request $r)
    {
        $r->validate(['ids' => 'required|array']);
        Product::destroy($r->ids);
        return redirect()->route('product.index');
    }
}
