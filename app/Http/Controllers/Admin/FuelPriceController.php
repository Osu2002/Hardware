<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class FuelPriceController extends Controller
{
  public function index()
  {
    return Inertia::render('FuelList/index');
  }

  public function getData()
  {
    return DataTables::of(FuelPrice::select(['id','fuel_type','price']))
     ->addColumn('action', function ($r) {
    return '
      <a href="'.route('fuels.edit', $r->id).'" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i></a>
      <form method="POST" action="'.route('fuels.destroy', $r->id).'" style="display:inline;">
        '.csrf_field().method_field('DELETE').'
        <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete?\')">
          <i class="bx bx-trash"></i>
        </button>
      </form>';
})

      ->rawColumns(['action'])
      ->make(true);
  }

  public function create()
  {
    return Inertia::render('FuelList/Create');
  }

  public function store(Request $req)
  {
    $data = $req->validate([
      'fuel_type'=>'required|string|unique:fuel_prices,fuel_type',
      'price'=>'required|numeric|min:0',
    ]);
    FuelPrice::create($data);
    return redirect()->route('fuels.index')->with('success','Added.');
  }

  public function edit(FuelPrice $fuelPrice)
  {
    return Inertia::render('FuelList/Edit', [
      'fuelPrice' => $fuelPrice
    ]);
  }

  public function update(Request $req, FuelPrice $fuelPrice)
  {
    $data = $req->validate([
      'fuel_type'=>"required|string|unique:fuel_prices,fuel_type,{$fuelPrice->id}",
      'price'=>'required|numeric|min:0',
    ]);
    $fuelPrice->update($data);
    return redirect()->route('fuels.index')->with('success','Updated.');
  }

  public function destroy(FuelPrice $fuelPrice)
  {
    $fuelPrice->delete();
    return redirect()->route('fuels.index')->with('success','Deleted.');
  }
}
