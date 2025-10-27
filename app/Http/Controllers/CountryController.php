<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Hash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Exception;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::select('id', 'name', 'phone1', 'location')
            ->where('status', 1) // Only active countries, if needed
            ->get();
        return Inertia::render('Country/Index', ['countries' => $countries]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Country/CreateUpdate');
    }
    public function getData()
    {
        $users = Country::with('media')->get();
        // dd($users);
        return DataTables::of($users)
            ->addColumn('check', function ($row) {
                return '<div class="custom-control custom-checkbox item-check">
            <input type="checkbox" class="form-check-input" id="' . $row->id . '" value="' . $row->id . '">
            <label class="form-check-label form-check-label" for="' . $row->id . '"></label>
          </div>';
            })

            ->addColumn('action',  function ($row) {



                $action_html = '';
                if (auth()->user()->can('country.view country.edit')) {
                    $action_html .= '<a class="dropdown-item action_edit" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" href="javascript:void(0)"><i class="fas fa-edit mr-2"></i> View / Edit</a>';
                }
                if (auth()->user()->can('country.edit')) {
                    $action_html .= '<a class="dropdown-item ' . ($row->status == 1 ? 'text-warning' : 'text-success') . ' action_status_change" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" data-status="' . $row->status . '" href="javascript:void(0)"><i class="fas fa-power-off mr-2"></i>' . ($row->status == 1 ? ' Deactivate' : ' Activate') . '</a> ';
                }

                $action_html .= '<div class="dropdown-divider"></div>';
                if (auth()->user()->can('country.delete')) {
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
            ->rawColumns(['check', 'action', 'status', 'image'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // Insert a new country record
    public function store(Request $request)
    {
        // Validate the request data
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'status'     => 'required|in:0,1',
            'slug' => ['required', 'unique:countries'],
            'flag_Image' => 'nullable|image|max:2048',
            'phone1'     => 'required|string|max:20',
            'phone2'     => 'nullable|string|max:20',
            'location'   => 'required|string|max:100',
            'email'      => 'nullable|email|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Create a new Customer instance and save data
            $country = new Country();
            $country->name = $request->name;
            $country->slug = $request->slug;
            $country->status = $request->status;
            $country->phone1 = $request->phone1;
            $country->phone2 = $request->phone2;
            $country->location = $request->location;
            $country->email = $request->email;

            $country->save();

            // Save profile image if uploaded
            if ($request->hasFile('flag_Image')) {
                $country->addMedia($request->file('flag_Image'))->toMediaCollection('Flag_Image');
            }

            DB::commit();

            return redirect()->route('country.index')->with('success', 'Country created successfully.');
        } catch (Exception $ex) {
            dd($ex);
            DB::rollBack();
            return abort(500, 'An error occurred while creating the country.');
        }
    }



    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            // 'name' => ['required'],
            // 'status' => ['required'],
            // 'email' => ['required', 'email'],
            // 'phone' => ['required'],
            // 'address' => ['required'],
            // 'profile_image' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:10000']
        ]);

        try {
            DB::beginTransaction();

            $country = Country::find($request->id);
            $country->name = $request->name;
            $country->slug = $request->slug;
            $country->status = $request->status;
            $country->phone1 = $request->phone1;
            $country->phone2 = $request->phone2;
            $country->location = $request->location;
            $country->email = $request->email;


            $country->save();


            if ($request->hasFile('flag_Image')) {
                if ($country->media) {
                    Storage::disk('public')->delete($country->media);
                    $country->clearMediaCollection('Flag_Image');
                }
                $country->addMedia($request->file('flag_Image'))->toMediaCollection('Flag_Image');
            }

            DB::commit();

            return redirect()->route('country.index')->with('success', 'Country updated successfully.');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return abort(500, 'An error occurred while updating the country.');
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $country = Country::with('media')->find($id);
        // dd($vehicle_types);
        return Inertia::render('Country/CreateUpdate', ['country' => $country]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // dd($request->all());
        try {
            Country::destroy([$request->ids]);
            return redirect()->route('country.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }

    public function updateStatus(Request $request)
    {
        // dd($request->all());
        try {
            $user = Country::find($request->id);
            if ($request->status == 0) {
                $user->status = 1;
            } else {
                $user->status = 0;
            }
            $user->save();

            return redirect()->route('country.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }
}
