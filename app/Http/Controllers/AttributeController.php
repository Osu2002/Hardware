<?php

// app/Http/Controllers/AttributeController.php
namespace App\Http\Controllers;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AttributeController extends Controller {
  public function index(){ return Inertia::render('Attribute/Index'); }
  public function getData(){
    return datatables()->of(Attribute::query())->make(true);
  }
  public function create(){ return Inertia::render('Attribute/CreateUpdate'); }
  public function store(Request $r){
    $r->validate([
      'code'=>['required','max:64','regex:/^[a-z0-9_]+$/','unique:attributes,code'],
      'name'=>['required','max:120'],
      'type'=>['required','in:text,number,select,boolean,color'],
      'status'=>['required','in:0,1'],
      'is_filterable'=>'boolean','is_variant_option'=>'boolean',
      'options'=>'array'
    ]);
    $attr = Attribute::create($r->only('code','name','type','unit','is_filterable','is_variant_option','status','sort_order'));
    if ($r->type==='select') {
      foreach ($r->options ?? [] as $i=>$o) {
        AttributeOption::create([
          'attribute_id'=>$attr->id,'value'=>$o['value'],'hex'=>$o['hex']??null,'sort_order'=>$i
        ]);
      }
    }
    return redirect()->route('attribute.index');
  }
  public function edit(Attribute $attribute){
    $attribute->load('options');
    return Inertia::render('Attribute/CreateUpdate',['attribute'=>$attribute]);
  }
  public function update(Request $r){
    $r->validate([
      'id'=>['required','exists:attributes,id'],
      'code'=>['required','max:64','regex:/^[a-z0-9_]+$/', Rule::unique('attributes','code')->ignore($r->id)],
      'name'=>['required','max:120'],
      'type'=>['required','in:text,number,select,boolean,color'],
      'status'=>['required','in:0,1'],
      'is_filterable'=>'boolean','is_variant_option'=>'boolean',
      'options'=>'array'
    ]);
    $attr = Attribute::findOrFail($r->id);
    $attr->update($r->only('code','name','type','unit','is_filterable','is_variant_option','status','sort_order'));
    // replace options if select
    $attr->options()->delete();
    if ($r->type==='select') {
      foreach ($r->options ?? [] as $i=>$o) {
        AttributeOption::create([
          'attribute_id'=>$attr->id,'value'=>$o['value'],'hex'=>$o['hex']??null,'sort_order'=>$i
        ]);
      }
    }
    return redirect()->route('attribute.index');
  }
  public function destroy(Request $r){
    Attribute::destroy($r->ids ?? []);
    return redirect()->route('attribute.index');
  }
}
