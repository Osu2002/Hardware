<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Type; // keep using existing Type model/table/columns
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('VehicleCMS/Category/Index');
    }

    public function getData()
    {
        $rows = Category::with('media');

        return DataTables::of($rows)
            ->addColumn('check', function ($row) {
                return '<div class="custom-control custom-checkbox item-check">
                    <input type="checkbox" class="form-check-input" id="'.$row->id.'" value="'.$row->id.'">
                    <label class="form-check-label" for="'.$row->id.'"></label>
                </div>';
            })
            ->addColumn('action', function ($row) {
                $action_html = '';
                // Adjust permission slugs to category.*
                if (auth()->user()->can('category.view') || auth()->user()->can('category.edit')) {
                    $action_html .= '<a class="dropdown-item action_edit" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-edit mr-2"></i> View / Edit</a>';
                }
                if (auth()->user()->can('category.edit')) {
                    $action_html .= '<a class="dropdown-item '.($row->status == 1 ? 'text-warning' : 'text-success').' action_status_change" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" data-status="'.$row->status.'" href="javascript:void(0)"><i class="fas fa-power-off mr-2"></i>'.($row->status == 1 ? ' Deactivate' : ' Activate').'</a> ';
                }
                $action_html .= '<div class="dropdown-divider"></div>';
                if (auth()->user()->can('category.delete')) {
                    $action_html .= '<a class="dropdown-item text-danger action_delete" data-bs-toggle="modal" data-bs-target="#deleteConfirm" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-trash mr-2"></i> Delete</a> ';
                }

                return '<div class="btn-group">
                    <button type="button" class="btn btn-main btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                    <div class="dropdown-menu" style="min-width:10rem;">'.$action_html.'</div>
                </div>';
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 1 && !$row->deleted_at) {
                    return '<span class="badge bg-success">Active</span>';
                } elseif ($row->status == 0 && !$row->deleted_at) {
                    return '<span class="badge bg-warning">Inactive</span>';
                } elseif ($row->deleted_at) {
                    return '<span class="badge bg-danger">Suspended</span>';
                }
                return '';
            })
            ->addColumn('image', function ($row) {
                if (count($row->media) > 0) {
                    return '<img src="'.$row->media[0]->original_url.'" height="25"/>';
                }
                return 'No Image';
            })
            ->rawColumns(['check', 'action', 'status', 'image'])
            ->make(true);
    }

    public function create()
    {
        
        return Inertia::render('VehicleCMS/Category/CreateUpdate');
    }

   public function store(Request $request)
{
    $category = new Category();
    $category->title    = $request->title;
    $category->status   = $request->status;
    $category->featured = $request->featured;
    $category->save();

    if ($request->hasFile('vehicle_type_image')) {
        $category->addMedia($request->file('vehicle_type_image'))
                 ->toMediaCollection('category_image');
    }

     if ($request->hasFile('category_banner_image')) {
        $category->addMedia($request->file('category_banner_image'))
            ->toMediaCollection('category_banner');
    }
    return redirect()->route('category.index');
}

   public function edit($id)
{
    $category = Category::with('media')->findOrFail($id);
    return Inertia::render('VehicleCMS/Category/CreateUpdate', ['category' => $category]);
}

    public function updateStatus(Request $request)
    {
        try {
            $row = Category::findOrFail($request->id);
            $row->status = $request->status == 0 ? 1 : 0;
            $row->save();

            return redirect()->route('category.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }

   public function update(Request $request)
{
    $request->validate([
        'id' => ['required'],
        'title' => ['required'],
        'status' => ['required'],
        'featured' => ['required'],
        'vehicle_type_image' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:10000'],
        // ✅ NEW:
        'category_banner_image' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:12000'],
    ]);

    DB::beginTransaction();

    $category = Category::findOrFail($request->id);
    $category->title = $request->title;
    $category->status = $request->status;
    $category->featured = $request->featured;
    $category->save();

    DB::commit();

    // Category Image
    if ($request->hasFile('vehicle_type_image')) {
        $category->clearMediaCollection('category_image');
        $category->addMedia($request->file('vehicle_type_image'))
            ->toMediaCollection('category_image');
    }

    // ✅ Banner Image
    if ($request->hasFile('category_banner_image')) {
        $category->clearMediaCollection('category_banner');
        $category->addMedia($request->file('category_banner_image'))
            ->toMediaCollection('category_banner');
    }

    return redirect()->route('category.index');
}



    public function destroy(Request $request)
    {
        try {
            // FIX: accept array properly
            Category::destroy($request->ids);
            return redirect()->route('category.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }
}
