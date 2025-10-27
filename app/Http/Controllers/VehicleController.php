<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Country;
use App\Models\Feature;
use App\Models\Manufacture;
use App\Models\Type;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Yajra\DataTables\Facades\DataTables;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('VehicleCMS/Vehicle/Index');
    }

    public function getData()
    {
        $users = Vehicle::with('media');
        // dd($users);
        return DataTables::of($users)
            ->addColumn('check', function ($row) {
                return '<div class="custom-control custom-checkbox item-check">
            <input type="checkbox" class="form-check-input" id="' . $row->id . '" value="' . $row->id . '">
            <label class="form-check-label form-check-label" for="' . $row->id . '"></label>
          </div>';
            })

            ->addColumn('action', function ($row) {



                $action_html = '';
                if (auth()->user()->can('backend-user.view backend-user.edit')) {
                    $action_html .= '<a class="dropdown-item action_edit" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" href="javascript:void(0)"><i class="fas fa-edit mr-2"></i> View / Edit</a>';
                }
                if (auth()->user()->can('backend-user.edit')) {
                    $action_html .= '<a class="dropdown-item ' . ($row->status == 1 ? 'text-warning' : 'text-success') . ' action_status_change" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" data-status="' . $row->status . '" href="javascript:void(0)"><i class="fas fa-power-off mr-2"></i>' . ($row->status == 1 ? ' Deactivate' : ' Activate') . '</a> ';
                }
                $action_html .= '<div class="dropdown-divider"></div>';
                if (auth()->user()->can('backend-user.delete')) {
                    $action_html .= '<a class="dropdown-item text-danger action_delete" data-bs-toggle="modal" data-bs-target="#deleteConfirm" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" href="javascript:void(0)"><i class="fas fa-trash mr-2"></i> Delete</a> ';
                }
                return '<div class="btn-group">
                                <button type="button" class="btn btn-main btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Action
                                </button>
                                <div class="dropdown-menu" style="min-width: 10rem;">
                                ' . $action_html . '
                                </div>
                            </div>';
            })->addColumn('status', function ($row) {
                if ($row->status == 1 && !$row->deleted_at) {
                    return '<span class="badge bg-success">Active</span>';
                } else if ($row->status == 0 && !$row->deleted_at) {
                    return '<span class="badge bg-warning">Inactive</span>';
                } else if ($row->deleted_at) {
                    return '<span class="badge bg-danger">Suspended</span>';
                }
            })
            ->addColumn('image', function ($row) {
                if (count($row->media) > 0) {
                    $image = '<img src="' . $row->media[0]->original_url . '" height="50"/>';
                } else {
                    $image = "No Image";
                }

                return $image;
            })
            ->addColumn('vehicle_model_id', function ($row) {
                $vehicle_model_id = $row->vehicle_model_id;
                $vehicle_model = VehicleModel::find($vehicle_model_id);
                if ($vehicle_model) {
                    return $vehicle_model->title;
                } else {
                    return "";
                }
            })
            ->rawColumns(['check', 'action', 'status', 'image', 'vehicle_model_id'])
            ->make(true);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $features = Feature::all();
        $type = Type::where('status', 1)->get();
        $manufacture = Manufacture::all();
        $color = Color::all();
        $countries = Country::all();

        if ($request->manufacture_id) {
            // dd($request->all());
            $model = VehicleModel::where(['manufacture_id' => $request->manufacture_id, 'status' => 1])->get();
        } else {
            // if no manufacture selected
            $model = collect([]);
        }

        // dd($type);
        return Inertia::render('VehicleCMS/Vehicle/CreateUpdate', ['countries' => $countries, 'features' => $features, 'type' => $type, 'manufacture' => $manufacture, 'color' => $color, 'model' => $model]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        \Log::debug('gradeSections payload:', $request->input('gradeSections', []));

        $request->validate([


        ]);
        // dd($request->all());
        try {
            DB::beginTransaction();

            $vehicle = new Vehicle();
            $vehicle->vehicle_type_id = json_encode($request->vehicle_type);
            $vehicle->manufacture_id = $request->manufacture_name;
            $vehicle->vehicle_model_id = $request->model;
            $vehicle->seo_url = "123";
            $vehicle->transmission = json_encode($request->transmission);
            $vehicle->year = $request->year;
            $vehicle->drive_type = json_encode($request->drive_type);
            $vehicle->availability = $request->availability;
            $vehicle->editorContent = $request->editorContent;
            $vehicle->featured = $request->featured;
            $vehicle->latest = $request->latest;
            $vehicle->status = $request->status;
            $vehicle->countries = json_encode($request->countries);
            $vehicle->monthly_price = $request->monthly_price;
            $vehicle->monthly_price_currency = $request->monthly_price_currency;
            $vehicle->vehicle_status = $request->vehicle_status;
            $vehicle->engine_capacities = json_encode($request->engine_capacities);
            // $vehicle->fuel_types = json_encode($request->fuel_types);
            $vehicle->grades = json_encode($request->grades);
            $vehicle->chassis_nos = json_encode($request->chassis_nos);
            // $vehicle->description = json_encode($request->description);
            $vehicle->fuel_types = json_encode($request->fuel_types ?? []); 
            $vehicle->grades = json_encode($request->gradeSections);

            // dd($vehicle->grades);
            $vehicle->save();


            if ($request->hasFile('vehicle_image')) {
                $vehicle->addMedia($request->file('vehicle_image'))->withCustomProperties(['type' => 'vehicle_main'])->toMediaCollection('Vehicle_Images');
            }

            if ($request->has('uploaded_Images')) {
                foreach ($request->uploaded_Images as $file) {
                    if (isset($file['file']) && $file['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $vehicle->addMedia($file['file'])
                            ->withCustomProperties(['type' => 'vehicle_gallery'])
                            ->toMediaCollection('Vehicle_Images');
                    }
                }
            }

            $vehicle->save();

            DB::commit();


            return redirect()->route('vehicle.index');
        } catch (Exception $ex) {
            dd($ex);
            DB::rollBack();
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        $features = Feature::all();
        $vehicle = Vehicle::with('media')->find($id);
        $features = Feature::all();
        $type = Type::all();
        $manufacture = Manufacture::all();
        $color = Color::all();
        $countries = Country::all();

        if ($request->manufacture_id) {
            // dd($request->all());
            $model = VehicleModel::where('manufacture_id', $request->manufacture_id)->get();
        } else {
            $model = VehicleModel::all();
        }

        // dd($vehicle);
        return Inertia::render('VehicleCMS/Vehicle/CreateUpdate', ['countries' => $countries, 'vehicle' => $vehicle, 'features' => $features, 'type' => $type, 'manufacture' => $manufacture, 'color' => $color, 'model' => $model]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            // 'vehicle_type' => ['required'],
            // 'manufacture_name' => ['required'],
            // 'model' => ['required'],
            // 'seo_url' => ['nullable'],
            // 'ex_color' => ['required'],
            // 'in_color' => ['required'],
            // 'transmission' => ['required'],
            // 'year' => ['nullable'],
            // 'chassis_no' => ['required'],
            // // 'condition' => ['nullable'],
            // 'seats' => ['required'],
            // 'doors' => ['required'],
            // 'passengers' => ['required'],
            // 'engine_capacity' => ['required'],
            // 'mileage' => ['required'],
            // 'fuel_type' => ['required'],
            // 'drive_type' => ['required'],
            // 'auction_grade' => ['nullable'],
            // 'interior_condition' => ['nullable'],
            // 'availability' => ['required'],

            // 'vehicle_status' => ['nullable', 'in:new,used'],

            // 'editorContent' => ['nullable'],
            // 'featured' => ['required'],
            // 'features' => ['nullable'],
            // 'latest' => ['nullable'],
            // 'status' => ['required'],
            // 'countries' => ['nullable', 'array'],

            // 'monthly_price' => ['nullable', 'numeric'],
            // 'monthly_price_currency' => ['nullable', 'in:USD,LKR'],
            // 'initial_payment' => ['nullable', 'numeric'],
            // 'initial_payment_currency' => ['nullable', 'in:USD,LKR'],


            // 'fuel_type' => 'required|in:Diesel,Petrol,Hybrid,Electric',
            // 'fuel_efficiency' => 'required|numeric|min:0',

            // 'Vehicle_Image' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:10000']
        ]);


        //  dd($request->all());
        try {
            DB::beginTransaction();
            $vehicle = Vehicle::find($request->id);
            $vehicle->vehicle_type_id = json_encode($request->vehicle_type);
            $vehicle->manufacture_id = $request->manufacture_name;
            $vehicle->vehicle_model_id = $request->model;
            $vehicle->seo_url = "123";
            // $vehicle->ex_color = $request->ex_color;
            // $vehicle->in_color = $request->in_color;
            // $vehicle->features = json_encode($request->features ?? []); // Convert array to JSON
            $vehicle->transmission = json_encode($request->transmission);
            $vehicle->year = $request->year;
            // $vehicle->chassis_no = $request->chassis_no;
            // $vehicle->condition = $request->condition;
            // $vehicle->seats = $request->seats;
            // $vehicle->doors = $request->doors;
            // $vehicle->passengers = $request->passengers;
            // $vehicle->engine_capacity = $request->engine_capacity;
            // $vehicle->mileage = $request->mileage;
            // $vehicle->fuel_type = $request->fuel_type;
            $vehicle->drive_type = json_encode($request->drive_type);
            // $vehicle->auction_grade = $request->auction_grade;
            // $vehicle->interior_condition = $request->interior_condition;
            $vehicle->availability = $request->availability;
            $vehicle->editorContent = $request->editorContent;
            $vehicle->featured = $request->featured;
            $vehicle->latest = $request->latest;
            $vehicle->status = $request->status;
            $vehicle->countries = json_encode($request->countries);
            $vehicle->monthly_price = $request->monthly_price;
            $vehicle->monthly_price_currency = $request->monthly_price_currency;
            $vehicle->vehicle_status = $request->vehicle_status;
            $vehicle->engine_capacities = json_encode($request->engine_capacities);
            $vehicle->fuel_types = json_encode($request->fuel_types);
            $vehicle->grades = json_encode($request->grades);
            $vehicle->chassis_nos = json_encode($request->chassis_nos);
            $vehicle->grades = json_encode($request->gradeSections);

            // dd($vehicle->countries);
            $vehicle->save();

            if ($request->hasFile('vehicle_image')) {
                $mainImage = $vehicle->media()
                    ->where('collection_name', 'Vehicle_Images')
                    ->whereJsonContains('custom_properties->type', 'vehicle_main')
                    ->first();

                if ($mainImage) {
                    Storage::disk('public')->delete($mainImage->getPath());
                    $mainImage->delete();
                }
                $vehicle->addMedia($request->file('vehicle_image'))->withCustomProperties(['type' => 'vehicle_main'])->toMediaCollection('Vehicle_Images');
            }

            if ($request->has('uploaded_Images')) {
                foreach ($request->uploaded_Images as $file) {
                    if (isset($file['file']) && $file['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $vehicle->addMedia($file['file'])
                            ->withCustomProperties(['type' => 'vehicle_gallery'])
                            ->toMediaCollection('Vehicle_Images');
                    }
                }
            }
            $vehicle->save();
            DB::commit();

            return redirect()->route('vehicle.index');
        } catch (Exception $ex) {
            dd($ex);
            DB::rollBack();
            Log::error($ex);
            abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // dd($request->all());
        try {
            Vehicle::destroy([$request->ids]);
            return redirect()->route('vehicle.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }

    public function updateStatus(Request $request)
    {
        // dd($request->all());
        try {
            $user = Vehicle::find($request->id);
            if ($request->status == 0) {
                $user->status = 1;
            } else {
                $user->status = 0;
            }
            $user->save();

            return redirect()->route('vehicle.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }
    public function removeImage(Request $request)
    {

        $urlParts = explode('/', $request->imageUrl);
        $uuid = $urlParts[count($urlParts) - 2]; // Assuming the UUID is in the second last part
        $fileName = $urlParts[count($urlParts) - 1]; // The last part is the file name

        // Find the media record by UUID or file name
        $media = Media::where('uuid', $uuid)
            ->where('file_name', $fileName)
            ->first();
        // dd($media);
        if ($media) {
            // Delete the media from the product
            $media->delete(); // This removes the file from storage and the database

            return redirect()->back();
        }
    }
}
