<?php

namespace App\Http\Controllers;

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
        $rows = Type::with('media');

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
        $request->validate([
            'title' => ['required'],
            'status' => ['required'],
            'featured' => ['required'],
            // Keep same request field name and validation key:
            'vehicle_type_image' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:10000'],
        ]);

        try {
            DB::beginTransaction();

            $type = new Type();
            $type->title = $request->title;
            $type->status = $request->status;
            $type->featured = $request->featured;
            $type->save();

            if ($request->hasFile('vehicle_type_image')) {
                // Keep SAME media collection/path
                $type->addMedia($request->file('vehicle_type_image'))->toMediaCollection('vehicle_type_image');
                $type->save();
            }

            DB::commit();
            return redirect()->route('category.index');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return abort(500);
        }
    }

    public function edit($id)
    {
        $category = Type::with('media')->findOrFail($id);
        return Inertia::render('VehicleCMS/Category/CreateUpdate', ['category' => $category]);
    }

    public function updateStatus(Request $request)
    {
        try {
            $row = Type::findOrFail($request->id);
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
            'title' => ['required'],
            'status' => ['required'],
            'featured' => ['required'],
            // Keep SAME request field name:
            'vehicle_type_image' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:10000'],
        ]);

        try {
            DB::beginTransaction();

            $type = Type::findOrFail($request->id);
            $type->title = $request->title;
            $type->status = $request->status;
            $type->featured = $request->featured;
            $type->save();

            DB::commit();

            if ($request->hasFile('vehicle_type_image')) {
                // Clear existing media in this collection then add the new one
                $type->clearMediaCollection('vehicle_type_image');
                $type->addMedia($request->file('vehicle_type_image'))->toMediaCollection('vehicle_type_image');
                $type->save();
            }

            return redirect()->route('category.index');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return abort(500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            // FIX: accept array properly
            Type::destroy($request->ids);
            return redirect()->route('category.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }
}
