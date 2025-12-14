<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('VehicleCMS/SubCategory/Index');
    }

    /**
     * Index table shows ONLY categories (as you requested):
     * Category | Status (based on subcategories) | Sort | Action
     */
  public function getData()
{
    $rows = Category::query()
        ->select(['id', 'title', 'sort_order'])
        ->withCount([
            'subcategories',
            'subcategories as active_subcategories_count' => fn($q) => $q->where('status', 1),
        ]);

    return DataTables::of($rows)
        ->addColumn('check', function ($row) {
            return '<div class="custom-control custom-checkbox item-check">
                <input type="checkbox" class="form-check-input" id="cat_'.$row->id.'" value="'.$row->id.'">
                <label class="form-check-label" for="cat_'.$row->id.'"></label>
            </div>';
        })

        // IMPORTANT: title is a REAL column -> use editColumn
        ->editColumn('title', fn($row) => e($row->title))

        ->addColumn('status', function ($row) {
            if ((int)$row->subcategories_count === 0) {
                return '<span class="badge bg-secondary">No Subcategories</span>';
            }
            if ((int)$row->active_subcategories_count > 0) {
                return '<span class="badge bg-success">Active</span>';
            }
            return '<span class="badge bg-warning">Inactive</span>';
        })

        // IMPORTANT: return real DB field name sort_order (not "sort")
        ->editColumn('sort_order', fn($row) => (int)($row->sort_order ?? 0))

        ->addColumn('action', function ($row) {
            $currentStatus = ((int)$row->active_subcategories_count > 0) ? 1 : 0;
            $html = '';

            if (auth()->user()->can('subcategory.view') || auth()->user()->can('subcategory.edit')) {
                $html .= '<a class="dropdown-item action_edit" style="font-size:14px;padding:5px 13px;"
                    data-item-id="'.$row->id.'" href="javascript:void(0)">
                    <i class="fas fa-edit mr-2"></i> View / Edit
                </a>';
            }

            if (auth()->user()->can('subcategory.edit')) {
                $html .= '<a class="dropdown-item '.($currentStatus == 1 ? 'text-warning' : 'text-success').' action_status_change"
                    style="font-size:14px;padding:5px 13px;"
                    data-item-id="'.$row->id.'" data-status="'.$currentStatus.'" href="javascript:void(0)">
                    <i class="fas fa-power-off mr-2"></i>'.($currentStatus == 1 ? ' Deactivate' : ' Activate').'
                </a>';
            }

            $html .= '<div class="dropdown-divider"></div>';

            if (auth()->user()->can('subcategory.delete')) {
                $html .= '<a class="dropdown-item text-danger action_delete"
                    data-bs-toggle="modal" data-bs-target="#deleteConfirm"
                    style="font-size:14px;padding:5px 13px;"
                    data-item-id="'.$row->id.'" href="javascript:void(0)">
                    <i class="fas fa-trash mr-2"></i> Delete
                </a>';
            }

            return '<div class="btn-group">
                <button type="button" class="btn btn-main btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Action
                </button>
                <div class="dropdown-menu" style="min-width:10rem;">'.$html.'</div>
            </div>';
        })
        ->rawColumns(['check', 'status', 'action'])
        ->make(true);
}


    public function create()
    {
        return Inertia::render('VehicleCMS/SubCategory/CreateUpdate', [
            'categories'    => Category::orderBy('title')->get(['id', 'title']),
            'category'      => null,
            'subcategories' => [],
            'status'        => 1,
        ]);
    }

    public function edit($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $subs = SubCategory::with('media')
            ->where('category_id', $category->id)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($s) {
                $img = $s->getFirstMediaUrl('subcategory_image');
                return [
                    'id'        => $s->id,
                    'title'     => $s->title,
                    'image_url' => $img ?: '',
                ];
            })
            ->values();

        // derive set status: if any active -> 1 else 0 (if none exist -> 1 default)
        $hasAny = SubCategory::where('category_id', $category->id)->exists();
        $setStatus = $hasAny
            ? (SubCategory::where('category_id', $category->id)->where('status', 1)->exists() ? 1 : 0)
            : 1;

        return Inertia::render('VehicleCMS/SubCategory/CreateUpdate', [
            'categories'    => Category::orderBy('title')->get(['id', 'title']),
            'category'      => $category->only(['id', 'title']),
            'subcategories' => $subs,
            'status'        => $setStatus,
        ]);
    }

    public function store(Request $r)
    {
        return $this->saveSet($r);
    }

    public function update(Request $r)
    {
        return $this->saveSet($r);
    }

    /**
     * Saves multiple subcategories for ONE category.
     * Each subcategory is stored as its own record (unique ID).
     */
    private function saveSet(Request $r)
    {
        $r->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'status'      => ['required', 'in:0,1'],
            'subcategories' => ['required', 'array', 'min:1'],
            'subcategories.*.id'    => ['nullable', 'integer'],
            'subcategories.*.title' => ['required', 'max:180'],
            'subcategories.*.image' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
        ]);

        $categoryId = (int)$r->category_id;
        $status = (int)$r->status;
        $items = $r->input('subcategories', []);

        DB::beginTransaction();
        try {
            $existing = SubCategory::where('category_id', $categoryId)->get();
            $existingIds = $existing->pluck('id')->all();

            $incomingIds = collect($items)
                ->pluck('id')
                ->filter()
                ->map(fn($v) => (int)$v)
                ->all();

            // soft-delete removed items
            $toDelete = array_diff($existingIds, $incomingIds);
            if (!empty($toDelete)) {
                SubCategory::where('category_id', $categoryId)
                    ->whereIn('id', $toDelete)
                    ->delete();
            }

            foreach ($items as $i => $row) {
                $title = (string)$row['title'];
                $id = !empty($row['id']) ? (int)$row['id'] : null;

                $slugBase = Str::slug($title);
                $slug = $slugBase ?: Str::random(8);

                // ensure unique slug per category (exclude current id when updating)
                $dupQuery = SubCategory::where('category_id', $categoryId)->where('slug', $slug);
                if ($id) $dupQuery->where('id', '!=', $id);
                if ($dupQuery->exists()) {
                    $slug .= '-' . Str::random(5);
                }

                $payload = [
                    'category_id' => $categoryId,
                    'title'       => $title,
                    'slug'        => $slug,
                    'status'      => $status,
                    'sort_order'  => $i + 1,
                ];

                if ($id) {
                    $sub = SubCategory::where('category_id', $categoryId)->findOrFail($id);
                    $sub->update($payload);
                } else {
                    $sub = SubCategory::create($payload);
                }

                // handle image for this row
                $file = $r->file("subcategories.$i.image");
                if ($file) {
                    $sub->clearMediaCollection('subcategory_image');
                    $sub->addMedia($file)->toMediaCollection('subcategory_image');
                }
            }

            DB::commit();
            return redirect()->route('subcategory.index');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            throw $ex;
        }
    }

    /**
     * Toggle ALL subcategories under a category (set-level activate/deactivate)
     */
    public function updateStatus(Request $r)
    {
        $r->validate([
            'id'     => ['required', 'exists:categories,id'],
            'status' => ['required', 'in:0,1'],
        ]);

        try {
            $categoryId = (int)$r->id;
            $new = ((int)$r->status === 0) ? 1 : 0;

            SubCategory::where('category_id', $categoryId)->update(['status' => $new]);

            return redirect()->route('subcategory.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }

    /**
     * Delete subcategory set(s) by category ids (soft delete rows)
     */
    public function destroy(Request $r)
    {
        $r->validate(['ids' => ['required', 'array']]);

        try {
            SubCategory::whereIn('category_id', $r->ids)->delete();
            return redirect()->route('subcategory.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }
}
