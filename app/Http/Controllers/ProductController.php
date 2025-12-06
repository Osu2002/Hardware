<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeSet;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Product/Index');
    }

    public function getData()
    {
        $rows = Product::with(['brand:id,title','uom:id,name'])
            ->select(['id','name','sku','status','price','sale_price','brand_id','uom_id','sort_order','created_at']);

        return DataTables::of($rows)
            ->addColumn('check', fn($row) =>
                '<div class="custom-control custom-checkbox item-check">
                   <input type="checkbox" class="form-check-input" id="'.$row->id.'" value="'.$row->id.'">
                   <label class="form-check-label" for="'.$row->id.'"></label>
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
                $html .= '<a class="dropdown-item action_edit" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-edit mr-2"></i> View / Edit</a>';
                $html .= '<a class="dropdown-item '.($row->status==1?'text-warning':'text-success').' action_status_change" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" data-status="'.$row->status.'" href="javascript:void(0)"><i class="fas fa-power-off mr-2"></i>'.($row->status==1?' Deactivate':' Activate').'</a>';
                $html .= '<div class="dropdown-divider"></div>';
                $html .= '<a class="dropdown-item text-danger action_delete" data-bs-toggle="modal" data-bs-target="#deleteConfirm" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-trash mr-2"></i> Delete</a>';
                return '<div class="btn-group">
                          <button type="button" class="btn btn-main btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                          <div class="dropdown-menu" style="min-width:10rem;">'.$html.'</div>
                        </div>';
            })
            ->rawColumns(['check','status','action'])
            ->make(true);
    }

    public function create()
    {
        return Inertia::render('Product/CreateUpdate', [
            'brands'      => Brand::where('status',1)->orderBy('title')->get(['id','title']),
            'uoms'        => Uom::where('status',1)->orderBy('name')->get(['id','name']),
            'categories'  => Category::orderBy('title')->get(['id','title']),
            'attributeSets' => AttributeSet::where('status',1)->orderBy('name')->get(['id','name']),
            'attributes'  => [], // filled on edit or via client switch of attribute set
        ]);
    }

    public function store(Request $r)
    {
        $r->validate([
            'name' => ['required','max:180'],
            'sku'  => ['required','max:120','unique:products,sku'],
            'status' => ['required','in:0,1'],
            'brand_id' => ['nullable','exists:brands,id'],
            'uom_id' => ['nullable','exists:uoms,id'],
            'attribute_set_id' => ['nullable','exists:attribute_sets,id'],
            'primary_category_id' => ['nullable','exists:categories,id'],
            'categories' => ['array'],
            'price' => ['nullable','numeric','min:0'],
            'sale_price' => ['nullable','numeric','min:0'],
            'short_description' => ['nullable','max:500'],
            'description' => ['nullable'],
            'attributes_map' => ['array'], // [{attribute_id,value}]
            'images.*' => ['nullable','mimes:jpeg,jpg,png,webp','max:20480'],
             'discount_status' => ['required','in:0,1'],
    'discount_type' => ['required_if:discount_status,1','nullable','in:percent,amount'],
    'discounted_amount' => ['required_if:discount_status,1','nullable','numeric','min:0'],
        ]);

        try {
            DB::beginTransaction();

            $slug = Str::slug($r->name);
            if (Product::where('slug',$slug)->exists()) {
                $slug .= '-' . Str::random(5);
            }

            $product = Product::create([
                'name' => $r->name,
                'slug' => $slug,
                'sku'  => $r->sku,
                'status' => $r->status,
                'sort_order' => (int)($r->sort_order ?? 0),
                'brand_id' => $r->brand_id,
                'uom_id' => $r->uom_id,
                'attribute_set_id' => $r->attribute_set_id,
                'primary_category_id' => $r->primary_category_id,
                'price' => $r->price,
                'sale_price' => $r->sale_price,
                'short_description' => $r->short_description,
                'description' => $r->description,
                'discount_status'   => (int)($r->discount_status ?? 0),
    'discount_type'     => $r->discount_status ? $r->discount_type : null,
    'discounted_amount' => $r->discount_status ? $r->discounted_amount : null,
            ]);

            // categories (pivot without FK)
            $product->categories()->sync($r->categories ?? []);

            // attribute values
            foreach ($r->attributes_map ?? [] as $row) {
                if (!empty($row['attribute_id'])) {
                    ProductAttributeValue::create([
                        'product_id'   => $product->id,
                        'attribute_id' => $row['attribute_id'],
                        'value'        => $row['value'] ?? null,
                    ]);
                }
            }

            // images (optional)
            if ($r->hasFile('images')) {
                foreach ($r->file('images', []) as $img) {
                    $product->addMedia($img)->toMediaCollection('product_images');
                }
            }

            DB::commit();
            return redirect()->route('product.index');
        } catch (Exception $ex) {
            DB::rollBack(); report($ex); abort(500);
        }
    }

    public function edit($id)
    {
        $product = Product::with([
            'categories:id,title',
            'attributeValues.attribute:id,code,name,type,unit'
        ])->findOrFail($id);

        $attrs = [];
        if ($product->attribute_set_id) {
            $attrs = AttributeSet::with(['attributes:id,code,name,type,unit'])
                ->find($product->attribute_set_id)?->attributes ?? collect();
        }

        return Inertia::render('Product/CreateUpdate', [
    'product'       => $product,
    'brands'        => Brand::where('status',1)->orderBy('title')->get(['id','title']), // << was name
    'uoms'          => Uom::where('status',1)->orderBy('name')->get(['id','name']),
    'categories'    => Category::orderBy('title')->get(['id','title']),
    'attributeSets' => AttributeSet::where('status',1)->orderBy('name')->get(['id','name']),
    'attributes'    => $attrs->map(fn($a)=>$a->only(['id','code','name','type','unit']))->values(),
    'images'        => $product->getMedia('product_images')->map(fn($m)=>$m->getUrl()),
]);

    }

    public function update(Request $r)
    {
        $r->validate([
            'id' => ['required','exists:products,id'],
            'name' => ['required','max:180'],
            'sku'  => ['required','max:120','unique:products,sku,'.$r->id],
            'status' => ['required','in:0,1'],
            'brand_id' => ['nullable','exists:brands,id'],
            'uom_id' => ['nullable','exists:uoms,id'],
            'attribute_set_id' => ['nullable','exists:attribute_sets,id'],
            'primary_category_id' => ['nullable','exists:categories,id'],
            'categories' => ['array'],
            'price' => ['nullable','numeric','min:0'],
            'sale_price' => ['nullable','numeric','min:0'],
            'short_description' => ['nullable','max:500'],
            'description' => ['nullable'],
            'attributes_map' => ['array'],
            'images.*' => ['nullable','mimes:jpeg,jpg,png,webp','max:20480'],
              'discount_status' => ['required','in:0,1'],
    'discount_type' => ['required_if:discount_status,1','nullable','in:percent,amount'],
    'discounted_amount' => ['required_if:discount_status,1','nullable','numeric','min:0'],
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($r->id);
            $product->update([
                'name' => $r->name,
                'sku'  => $r->sku,
                'status' => $r->status,
                'sort_order' => (int)($r->sort_order ?? 0),
                'brand_id' => $r->brand_id,
                'uom_id' => $r->uom_id,
                'attribute_set_id' => $r->attribute_set_id,
                'primary_category_id' => $r->primary_category_id,
                'price' => $r->price,
                'sale_price' => $r->sale_price,
                'short_description' => $r->short_description,
                'description' => $r->description,
                'discount_status'   => (int)($r->discount_status ?? 0),
    'discount_type'     => $r->discount_status ? $r->discount_type : null,
    'discounted_amount' => $r->discount_status ? $r->discounted_amount : null,
            ]);

            $product->categories()->sync($r->categories ?? []);

            // reset and insert product attribute values
            $product->attributeValues()->delete();
            foreach ($r->attributes_map ?? [] as $row) {
                if (!empty($row['attribute_id'])) {
                    ProductAttributeValue::create([
                        'product_id'   => $product->id,
                        'attribute_id' => $row['attribute_id'],
                        'value'        => $row['value'] ?? null,
                    ]);
                }
            }

            if ($r->hasFile('images')) {
                foreach ($r->file('images', []) as $img) {
                    $product->addMedia($img)->toMediaCollection('product_images');
                }
            }

            DB::commit();
            return redirect()->route('product.index');
        } catch (Exception $ex) {
            DB::rollBack(); report($ex); abort(500);
        }
    }

    public function updateStatus(Request $r)
    {
        $r->validate(['id'=>'required|exists:products,id','status'=>'required|in:0,1']);
        $p = Product::findOrFail($r->id);
        $p->status = $r->status==1 ? 0 : 1;
        $p->save();
        return redirect()->route('product.index');
    }

    public function destroy(Request $r)
    {
        $r->validate(['ids'=>'required|array']);
        Product::destroy($r->ids);
        return redirect()->route('product.index');
    }
}
