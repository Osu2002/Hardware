<?php
// app/Http/Controllers/UomController.php
namespace App\Http\Controllers;

use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class UomController extends Controller
{
    public function index()
    {
        return Inertia::render('UOM/Index');
    }

    public function getData()
    {
        $rows = Uom::query();

        return DataTables::of($rows)
            ->addColumn('check', fn($row) =>
                '<div class="custom-control custom-checkbox item-check">
                   <input type="checkbox" class="form-check-input" id="'.$row->id.'" value="'.$row->id.'">
                   <label class="form-check-label" for="'.$row->id.'"></label>
                 </div>'
            )
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
            ->addColumn('action', function ($row) {
                $html = '';
                if (auth()->user()->can('uom.view') || auth()->user()->can('uom.edit')) {
                    $html .= '<a class="dropdown-item action_edit" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-edit mr-2"></i> View / Edit</a>';
                }
                if (auth()->user()->can('uom.edit')) {
                    $html .= '<a class="dropdown-item '.($row->status == 1 ? 'text-warning' : 'text-success').' action_status_change" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" data-status="'.$row->status.'" href="javascript:void(0)"><i class="fas fa-power-off mr-2"></i>'.($row->status == 1 ? ' Deactivate' : ' Activate').'</a> ';
                }
                $html .= '<div class="dropdown-divider"></div>';
                if (auth()->user()->can('uom.delete')) {
                    $html .= '<a class="dropdown-item text-danger action_delete" data-bs-toggle="modal" data-bs-target="#deleteConfirm" style="font-size:14px;padding:5px 13px;" data-item-id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-trash mr-2"></i> Delete</a>';
                }
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
        return Inertia::render('UOM/CreateUpdate');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required','max:32', 'regex:/^[A-Za-z0-9\-_]+$/', 'unique:uoms,code'],
            'name' => ['required','max:80'],
            'status' => ['required','in:0,1'],
            'sort_order' => ['nullable','integer','min:0']
        ]);

        try {
            DB::beginTransaction();
            Uom::create($request->only('code','name','status','sort_order'));
            DB::commit();
            return redirect()->route('uom.index');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return abort(500);
        }
    }

    public function edit($id)
    {
        $uom = Uom::findOrFail($id);
        return Inertia::render('UOM/CreateUpdate', ['uom' => $uom]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required','exists:uoms,id'],
            'code' => ['required','max:32', 'regex:/^[A-Za-z0-9\-_]+$/', Rule::unique('uoms','code')->ignore($request->id)],
            'name' => ['required','max:80'],
            'status' => ['required','in:0,1'],
            'sort_order' => ['nullable','integer','min:0']
        ]);

        try {
            DB::beginTransaction();
            $uom = Uom::findOrFail($request->id);
            $uom->update($request->only('code','name','status','sort_order'));
            DB::commit();
            return redirect()->route('uom.index');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return abort(500);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => ['required','exists:uoms,id'],
            'status' => ['required','in:0,1'],
        ]);

        try {
            $uom = Uom::findOrFail($request->id);
            $uom->status = $request->status == 1 ? 0 : 1;
            $uom->save();
            return redirect()->route('uom.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['ids' => ['required','array']]);
        try {
            Uom::destroy($request->ids);
            return redirect()->route('uom.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }
}
