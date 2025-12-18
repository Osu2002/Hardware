<?php

// app/Http/Controllers/AttributeController.php
namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AttributeController extends Controller
{
  public function index()
  {
    return Inertia::render('Attribute/Index');
  }
  public function getData()
  {
    $rows = Attribute::query()->withCount('options'); // optional

    return datatables()->of($rows)
      ->addColumn('check', function ($row) {
        return '<div class="custom-control custom-checkbox item-check">
                <input type="checkbox" class="form-check-input" id="att_' . $row->id . '" value="' . $row->id . '">
                <label class="form-check-label" for="att_' . $row->id . '"></label>
            </div>';
      })
      
      ->addColumn('type_label', function ($row) {
        $map = [
          'text' => 'Text',
          'number' => 'Number',
          'select' => 'Select',
          'boolean' => 'Boolean',
          'color' => 'Color',
        ];
        return $map[$row->type] ?? $row->type;
      })
      ->addColumn('filterable', function ($row) {
        return $row->is_filterable ? '<span class="badge bg-info">Yes</span>' : '<span class="badge bg-secondary">No</span>';
      })
      ->addColumn('variant', function ($row) {
        return $row->is_variant_option ? '<span class="badge bg-primary">Yes</span>' : '<span class="badge bg-secondary">No</span>';
      })
      ->addColumn('status_badge', function ($row) {
        return $row->status == 1
          ? '<span class="badge bg-success">Active</span>'
          : '<span class="badge bg-warning">Inactive</span>';
      })
      ->addColumn('action', function ($row) {
        $action_html = '';

        if (auth()->user()->can('attribute.edit') || auth()->user()->can('attribute.view')) {
          $action_html .= '<a class="dropdown-item" href="' . route('attribute.edit', $row->id) . '">
                    <i class="fas fa-edit mr-2"></i> View / Edit</a>';
        }

        if (auth()->user()->can('attribute.delete')) {
          $action_html .= '<div class="dropdown-divider"></div>';
          $action_html .= '<a class="dropdown-item text-danger action_delete" data-bs-toggle="modal" data-bs-target="#deleteConfirm"
                    data-item-id="' . $row->id . '" href="javascript:void(0)">
                    <i class="fas fa-trash mr-2"></i> Delete</a>';
        }

        return '<div class="btn-group">
                <button type="button" class="btn btn-main btn-sm dropdown-toggle" data-bs-toggle="dropdown">Action</button>
                <div class="dropdown-menu" style="min-width:10rem;">' . $action_html . '</div>
            </div>';
      })
      ->rawColumns(['check', 'filterable', 'variant', 'status_badge', 'action'])
      ->make(true);
  }

  public function create()
  {
    return Inertia::render('Attribute/CreateUpdate');
  }
  public function store(Request $r)
  {
    $r->validate([
      'code' => ['required', 'max:64', 'regex:/^[a-z0-9_]+$/', 'unique:attributes,code'],
      'name' => ['required', 'max:120'],
      'type' => ['required', 'in:text,number,select,boolean,color'],
      'status' => ['required', 'in:0,1'],
      'is_filterable' => 'boolean',
      'is_variant_option' => 'boolean',
      'options' => 'array'
    ]);
    $attr = Attribute::create($r->only('code', 'name', 'type', 'unit', 'is_filterable', 'is_variant_option', 'status', 'sort_order'));
    if ($r->type === 'select') {
      foreach ($r->options ?? [] as $i => $o) {
        AttributeOption::create([
          'attribute_id' => $attr->id,
          'value' => $o['value'],
          'hex' => $o['hex'] ?? null,
          'sort_order' => $i
        ]);
      }
    }
    
    return redirect()->route('attribute.index');
  }
  public function edit(Attribute $attribute)
  {
    $attribute->load('options');
    return Inertia::render('Attribute/CreateUpdate', ['attribute' => $attribute]);
  }
  public function update(Request $r)
  {
    $r->validate([
      'id' => ['required', 'exists:attributes,id'],
      'code' => ['required', 'max:64', 'regex:/^[a-z0-9_]+$/', Rule::unique('attributes', 'code')->ignore($r->id)],
      'name' => ['required', 'max:120'],
      'type' => ['required', 'in:text,number,select,boolean,color'],
      'status' => ['required', 'in:0,1'],
      'is_filterable' => 'boolean',
      'is_variant_option' => 'boolean',
      'options' => 'array'
    ]);
    $attr = Attribute::findOrFail($r->id);
    $attr->update($r->only('code', 'name', 'type', 'unit', 'is_filterable', 'is_variant_option', 'status', 'sort_order'));
    // replace options if select
    $attr->options()->delete();
    if ($r->type === 'select') {
      foreach ($r->options ?? [] as $i => $o) {
        AttributeOption::create([
          'attribute_id' => $attr->id,
          'value' => $o['value'],
          'hex' => $o['hex'] ?? null,
          'sort_order' => $i
        ]);
      }
    }
    return redirect()->route('attribute.index');
  }
  public function destroy(Request $r)
  {
    Attribute::destroy($r->ids ?? []);
    return redirect()->route('attribute.index');
  }
}
