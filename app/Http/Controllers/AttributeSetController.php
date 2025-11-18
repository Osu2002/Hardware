<?php
// app/Http/Controllers/AttributeSetController.php
namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeSet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class AttributeSetController extends Controller
{
    public function index() {
        return Inertia::render('AttributeSet/Index');
    }

    public function getData() {
        $rows = AttributeSet::query();
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

    public function create() {
        return Inertia::render('AttributeSet/CreateUpdate', [
            'attributes' => Attribute::where('status',1)->orderBy('name')->get(['id','code','name'])
        ]);
    }

    public function store(Request $r) {
        // dd($r->all());
        $r->validate([
            'name'=>'required|max:120',
            'status'=>'required|in:0,1',
            'sort_order'=>'nullable|integer|min:0',
            'map'=>'array' // [{attribute_id,is_required,sort_order}]
        ]);
        try {
            DB::beginTransaction();
            $set = AttributeSet::create($r->only('name','status','sort_order'));
            $payload = [];
            foreach ($r->map ?? [] as $i=>$m) {
                $payload[$m['attribute_id']] = [
                    'is_required' => (bool)($m['is_required'] ?? false),
                    'sort_order'  => (int)($m['sort_order'] ?? $i),
                ];
            }
            $set->attributes()->sync($payload);
            DB::commit();
            return redirect()->route('attribute-set.index');
        } catch (Exception $ex) {
            DB::rollBack(); Log::error($ex); abort(500);
        }
    }

    public function edit($id) {
        $set = AttributeSet::with(['attributes' => function($q){ $q->orderBy('attribute_set_attributes.sort_order'); }])->findOrFail($id);
        return Inertia::render('AttributeSet/CreateUpdate', [
            'set' => $set,
            'attributes' => Attribute::where('status',1)->orderBy('name')->get(['id','code','name'])
        ]);
    }

    public function update(Request $r) {
        $r->validate([
            'id'=>'required|exists:attribute_sets,id',
            'name'=>'required|max:120',
            'status'=>'required|in:0,1',
            'sort_order'=>'nullable|integer|min:0',
            'map'=>'array'
        ]);
        try {
            DB::beginTransaction();
            $set = AttributeSet::findOrFail($r->id);
            $set->update($r->only('name','status','sort_order'));
            $payload = [];
            foreach ($r->map ?? [] as $i=>$m) {
                $payload[$m['attribute_id']] = [
                    'is_required' => (bool)($m['is_required'] ?? false),
                    'sort_order'  => (int)($m['sort_order'] ?? $i),
                ];
            }
            $set->attributes()->sync($payload);
            DB::commit();
            return redirect()->route('attribute-set.index');
        } catch (Exception $ex) {
            DB::rollBack(); Log::error($ex); abort(500);
        }
    }

    public function updateStatus(Request $r) {
        $r->validate(['id'=>'required|exists:attribute_sets,id','status'=>'required|in:0,1']);
        $set = AttributeSet::findOrFail($r->id);
        $set->status = $r->status==1 ? 0 : 1;
        $set->save();
        return redirect()->route('attribute-set.index');
    }

    public function destroy(Request $r) {
        $r->validate(['ids'=>'required|array']);
        AttributeSet::destroy($r->ids);
        return redirect()->route('attribute-set.index');
    }
}
