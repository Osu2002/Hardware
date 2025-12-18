<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()
    {
        return Inertia::render('VehicleCMS/Brand/Index');
    }

    public function getData()
    {
        $rows = Brand::with('media');

        return DataTables::of($rows)
            ->addColumn('check', fn($row) =>
                '<div class="custom-control custom-checkbox item-check">
                   <input type="checkbox" class="form-check-input" id="'.$row->id.'" value="'.$row->id.'">
                   <label class="form-check-label" for="'.$row->id.'"></label>
                 </div>'
            )
            ->addColumn('status', function ($row) {
                if ($row->status == 1 && !$row->deleted_at) return '<span class="badge bg-success">Active</span>';
                if ($row->status == 0 && !$row->deleted_at) return '<span class="badge bg-warning">Inactive</span>';
                if ($row->deleted_at) return '<span class="badge bg-danger">Suspended</span>';
                return '';
            })
            ->addColumn('image', function ($row) {
                $media = $row->getFirstMedia('brand_logo');
                if ($media) return '<img src="'.$media->getUrl().'" height="25"/>';
                return 'No Image';
            })
            ->addColumn('action', function ($row) {
                $html = '';
                if (auth()->user()->can('brand.view') || auth()->user()->can('brand.edit')) {
                    $html .= '<a class="dropdown-item action_edit" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-edit mr-2"></i> View / Edit</a>';
                }
                if (auth()->user()->can('brand.edit')) {
                    $html .= '<a class="dropdown-item '.($row->status == 1 ? 'text-warning' : 'text-success').' action_status_change" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" data-status="'.$row->status.'" href="javascript:void(0)"><i class="fas fa-power-off mr-2"></i>'.($row->status == 1 ? ' Deactivate' : ' Activate').'</a>';
                }
                $html .= '<div class="dropdown-divider"></div>';
                if (auth()->user()->can('brand.delete')) {
                    $html .= '<a class="dropdown-item text-danger action_delete" data-bs-toggle="modal" data-bs-target="#deleteConfirm" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-trash mr-2"></i> Delete</a>';
                }

                return '<div class="btn-group">
                          <button type="button" class="btn btn-main btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                          <div class="dropdown-menu" style="min-width:10rem;">'.$html.'</div>
                        </div>';
            })
            ->rawColumns(['check','status','image','action'])
            ->make(true);
    }

    public function create()
    {
        return Inertia::render('VehicleCMS/Brand/CreateUpdate');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required','max:160'],
            'slug'  => ['required','max:140', Rule::unique('brands','slug')],
            'status' => ['required','in:0,1'],
            'featured' => ['required','in:0,1'],
            'website_url' => ['nullable','url'],
            'support_email' => ['nullable','email'],
            'hotline_phone' => ['nullable','max:40'],
            'country' => ['nullable','max:80'],
            'founded_year' => ['nullable','integer','min:1800','max:2100'],
            'short_description' => ['nullable','max:500'],
            'seo_title' => ['nullable','max:180'],
            'seo_description' => ['nullable','max:240'],
            'sort_order' => ['nullable','integer','min:0'],
            'brand_logo' => ['nullable','mimes:jpeg,jpg,png,webp','max:10000'],
            'brand_banner' => ['nullable','mimes:jpeg,jpg,png,webp','max:15000'],
        ]);

        try {
            DB::beginTransaction();

            $brand = Brand::create($request->only([
                'title','slug','status','featured','website_url','hotline_phone','support_email',
                'country','founded_year','short_description','long_description',
                'seo_title','seo_description','sort_order'
            ]));

            if ($request->hasFile('brand_logo')) {
                $brand->clearMediaCollection('brand_logo');
                $brand->addMedia($request->file('brand_logo'))->toMediaCollection('brand_logo');
            }
            if ($request->hasFile('brand_banner')) {
                $brand->clearMediaCollection('brand_banner');
                $brand->addMedia($request->file('brand_banner'))->toMediaCollection('brand_banner');
            }

            DB::commit();
            return redirect()->route('brand.index');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return abort(500);
        }
    }

    public function edit($id)
    {
        $brand = Brand::with('media')->findOrFail($id);
        return Inertia::render('VehicleCMS/Brand/CreateUpdate', ['brand' => $brand]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required','exists:brands,id'],
            'title' => ['required','max:160'],
            'slug'  => ['required','max:140', Rule::unique('brands','slug')->ignore($request->id)],
            'status' => ['required','in:0,1'],
            'featured' => ['required','in:0,1'],
            'website_url' => ['nullable','url'],
            'support_email' => ['nullable','email'],
            'hotline_phone' => ['nullable','max:40'],
            'country' => ['nullable','max:80'],
            'founded_year' => ['nullable','integer','min:1800','max:2100'],
            'short_description' => ['nullable','max:500'],
            'seo_title' => ['nullable','max:180'],
            'seo_description' => ['nullable','max:240'],
            'sort_order' => ['nullable','integer','min:0'],
            'brand_logo' => ['nullable','mimes:jpeg,jpg,png,webp','max:10000'],
            'brand_banner' => ['nullable','mimes:jpeg,jpg,png,webp','max:15000'],
        ]);

        try {
            DB::beginTransaction();

            $brand = Brand::findOrFail($request->id);
            $brand->update($request->only([
                'title','slug','status','featured','website_url','hotline_phone','support_email',
                'country','founded_year','short_description','long_description',
                'seo_title','seo_description','sort_order'
            ]));

            DB::commit();

            if ($request->hasFile('brand_logo')) {
                $brand->clearMediaCollection('brand_logo');
                $brand->addMedia($request->file('brand_logo'))->toMediaCollection('brand_logo');
            }
            if ($request->hasFile('brand_banner')) {
                $brand->clearMediaCollection('brand_banner');
                $brand->addMedia($request->file('brand_banner'))->toMediaCollection('brand_banner');
            }

            return redirect()->route('brand.index');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return abort(500);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => ['required','exists:brands,id'],
            'status' => ['required','in:0,1'],
        ]);

        try {
            $brand = Brand::findOrFail($request->id);
            $brand->status = $request->status == 1 ? 0 : 1;
            $brand->save();

            return redirect()->route('brand.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['ids' => ['required','array']]);
        try {
            Brand::destroy($request->ids);
            return redirect()->route('brand.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }
}
