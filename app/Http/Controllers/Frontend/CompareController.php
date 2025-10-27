<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;
use App\Models\Vehicle;
use App\Models\Manufacture;
use App\Models\VehicleModel;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompareController extends Controller
{
    // 1) show the main page
    public function index()
    {
        return Inertia::render('Compare/index');
    }

    // 2) filter dropdown data
    public function filters()
    {
        return [
          'makes'     => Manufacture::select('id','title')->get(),
          'countries' => Country::select('id','name')->get(),
        ];
    }

    // 3) models for a make
    public function models(Request $req)
    {
        return VehicleModel::where('manufacture_id', $req->make_id)
                           ->select('id','title')
                           ->get();
    }

    // 4) list vehicles by filters
    public function listVehicles(Request $req)
    {
        $q = Vehicle::query()->with('manufacture','vehicleModel');
        if($req->make_id)    $q->where('manufacture_id',$req->make_id);
        if($req->model_id)   $q->where('vehicle_model_id',$req->model_id);
        if($req->country_id) $q->whereJsonContains('countries',(string)$req->country_id);
        return $q->paginate(10);
    }

    // 5) single vehicle details
    public function vehicleDetails(Vehicle $vehicle)
    {
        return $vehicle->only('id','brand','model','fuel_type','fuel_efficiency');
    }

    // 6) fuel prices
    public function fuelPrices()
    {
        return FuelPrice::select('id','fuel_type','price')->get();
    }
}
