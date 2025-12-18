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
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('VehicleCMS/SubCategory/Index');
    }

    /**
     * Index table shows ONLY "subcategory sets"
     * = categories that have at least 1 subcategory.
     *
     * Columns: Category | Count | Status | Action
     */
    public function getData(Request $request)
    {
        $query = Category::query()
            ->select(['categories.id', 'categories.title'])
            ->whereHas('subcategories') // ✅ show only created sets
            ->withCount([
                'subcategories',
                'subcategories as active_subcategories_count' => fn($q) => $q->where('status', 1),
            ]);

        return DataTables::eloquent($query)
            ->addColumn('check', fn($row) => '
                <div class="custom-control custom-checkbox item-check">
                    <input type="checkbox" class="form-check-input" id="cat_' . $row->id . '" value="' . $row->id . '">
                    <label class="form-check-label" for="cat_' . $row->id . '"></label>
                </div>
            ')
            ->editColumn('title', fn($row) => e($row->title))

            ->addColumn('count', fn($row) => (int)($row->subcategories_count ?? 0))

            ->addColumn('status', function ($row) {
                $total  = (int)$row->subcategories_count;
                $active = (int)$row->active_subcategories_count;

                if ($total === 0) return '<span class="badge bg-secondary">No Subcategories</span>';
                if ($active > 0)  return '<span class="badge bg-success">Active</span>';
                return '<span class="badge bg-warning">Inactive</span>';
            })

            ->addColumn('action', function ($row) {
                $total  = (int)$row->subcategories_count;
                $active = (int)$row->active_subcategories_count;
                $currentStatus = ($active > 0) ? 1 : 0;

                $html = '';

                if (auth()->user()->can('subcategory.view') || auth()->user()->can('subcategory.edit')) {
                    $html .= '<a class="dropdown-item action_edit" data-item-id="' . $row->id . '" href="javascript:void(0)">
                        <i class="fas fa-edit mr-2"></i> View / Edit
                    </a>';
                }

                // Disable activate/deactivate if set has 0 subcategories (avoid confusion)
                if (auth()->user()->can('subcategory.edit') && $total > 0) {
                    $html .= '<a class="dropdown-item ' . ($currentStatus ? 'text-warning' : 'text-success') . ' action_status_change"
                        data-item-id="' . $row->id . '" data-status="' . $currentStatus . '" href="javascript:void(0)">
                        <i class="fas fa-power-off mr-2"></i> ' . ($currentStatus ? 'Deactivate' : 'Activate') . '
                    </a>';
                }

                $html .= '<div class="dropdown-divider"></div>';

                if (auth()->user()->can('subcategory.delete')) {
                    $html .= '<a class="dropdown-item text-danger action_delete"
                        data-bs-toggle="modal" data-bs-target="#deleteConfirm"
                        data-item-id="' . $row->id . '" href="javascript:void(0)">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </a>';
                }

                return '<div class="btn-group">
                    <button type="button" class="btn btn-main btn-sm dropdown-toggle" data-bs-toggle="dropdown">Action</button>
                    <div class="dropdown-menu" style="min-width:10rem;">' . $html . '</div>
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
            ->orderBy('sort_order') // still ok inside set
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
            $existingIds = SubCategory::where('category_id', $categoryId)->pluck('id')->all();

            $incomingIds = collect($items)
                ->pluck('id')
                ->filter()
                ->map(fn($v) => (int)$v)
                ->all();

            // remove deleted rows (soft delete)
            $toDelete = array_diff($existingIds, $incomingIds);
            if (!empty($toDelete)) {
                SubCategory::where('category_id', $categoryId)->whereIn('id', $toDelete)->delete();
            }

            foreach ($items as $i => $row) {
                $title = (string)($row['title'] ?? '');
                $id = !empty($row['id']) ? (int)$row['id'] : null;

                $slugBase = Str::slug($title);
                $slug = $slugBase ?: Str::random(8);

                $dupQuery = SubCategory::where('category_id', $categoryId)->where('slug', $slug);
                if ($id) $dupQuery->where('id', '!=', $id);
                if ($dupQuery->exists()) $slug .= '-' . Str::random(5);

                $payload = [
                    'category_id' => $categoryId,
                    'title'       => $title,
                    'slug'        => $slug,
                    'status'      => $status,
                    'sort_order'  => $i + 1,
                ];

                $sub = $id
                    ? SubCategory::where('category_id', $categoryId)->findOrFail($id)
                    : new SubCategory();

                $sub->fill($payload);
                $sub->save();

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

    public function updateStatus(Request $r)
    {
        $r->validate([
            'id'     => ['required', 'exists:categories,id'],
            'status' => ['required', 'in:0,1'],
        ]);

        $categoryId = (int)$r->id;
        $new = ((int)$r->status === 0) ? 1 : 0;

        SubCategory::where('category_id', $categoryId)->update(['status' => $new]);

        return redirect()->route('subcategory.index');
    }

    public function destroy(Request $r)
    {
        $r->validate(['ids' => ['required', 'array']]);

        SubCategory::whereIn('category_id', $r->ids)->delete();

        return redirect()->route('subcategory.index');
    }
}
