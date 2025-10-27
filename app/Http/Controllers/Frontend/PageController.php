<?php

namespace App\Http\Controllers\Frontend;

use App\Exports\VehicleStatsExport;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Feature;
use App\Models\Inquiry;
use App\Models\LiveAuctionManufacturer;
use App\Models\Manufacture;
use App\Models\Settings;
use App\Models\Testimonial;
use App\Models\Type;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Services\ApiClient\ApiClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use App\Models\FuelPrice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class PageController extends Controller
{
    public function about()
    {
        // dd('test');
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $countries = Country::where(['status' => 1])->get();
        $image_url = 'images/about.jpg';

        return Inertia::render('About/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries,
            'image_url' => $image_url
        ]);
        // return Inertia::render('About/index');
    }

    public function auction($id, Request $request)
    {
        // $id = $request->id;
        $requestData = $request->all();

        // if (!$id) {
        //     return redirect()->back();
        // }

        $query = "SELECT * FROM main WHERE id = '" . $id . "' order by marka_name ASC";
        $details = ApiClient::callAuctionApi($query, true);
        // dd($id);
        $details = count($details) > 0 ? $details[0] : null;

        $images = $details ? explode('#', $details['IMAGES']) : [];

        // stats filter queries
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $statsYearQuery = "SELECT count(year), year FROM stats ";
        $statsChassiesQuery = "SELECT count(kuzov), kuzov FROM stats ";
        $statsConditionQuery = "SELECT count(rate), rate FROM stats ";
        $countries = Country::where(['status' => 1])->get();

        if ($request->manufacture) {
            $statsYearQuery .= "WHERE marka_name = '" . $request->manufacture . "' ";
            $statsChassiesQuery .= "WHERE marka_name = '" . $request->manufacture . "' ";
            $statsConditionQuery .= "WHERE marka_name = '" . $request->manufacture . "' ";
        }

        if ($request->model) {
            $statsYearQuery .= "AND model_name = '" . $request->model . "' ";
            $statsChassiesQuery .= "AND model_name = '" . $request->model . "' ";
            $statsConditionQuery .= "AND model_name = '" . $request->model . "' ";
        }

        if ($request->chassis_no) {
            $statsConditionQuery .= "AND kuzov = '" . $request->chassis_no . "' ";
        }

        if ($request->year) {
            $statsChassiesQuery .= "AND year = '" . $request->year . "' ";
            $statsConditionQuery .= "AND year = '" . $request->year . "' ";
            $statsConditionQuery .= "AND year = '" . $request->year . "' ";
        }

        $statsYearQuery .= "GROUP BY year ORDER BY year DESC";
        $statsChassiesQuery .= 'GROUP BY kuzov ORDER BY kuzov ASC';
        $statsConditionQuery .= 'GROUP BY rate ORDER BY rate ASC';

        $statsYears = ApiClient::callAuctionApi($statsYearQuery);
        $statsChassis = ApiClient::callAuctionApi($statsChassiesQuery, true);
        $statsConditions = ApiClient::callAuctionApi($statsConditionQuery, true);

        $vehicleStatsList = $this->getVehicleStatsList($request);

        return Inertia::render('Auction_view/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries,
            'details' => $details,
            'vImages' => $images,
            'statsYears' => $statsYears,
            'statsChassis' => $statsChassis,
            'statsConditions' => $statsConditions,
            'requestQuery' => $requestData,
            'vehicleStatsList' => $vehicleStatsList
        ]);
    }




    public function Live_auction(Request $request)
    {
        $requestData = $request->all();
        // dd($requestData);
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $auctionDatesQuery = "SELECT auction_date FROM main GROUP BY DATE_FORMAT(auction_date,'%Y-%m-%d')";
        $auctionDates = ApiClient::callAuctionApi($auctionDatesQuery);
        $countries = Country::where(['status' => 1])->get();

        $manufcturesQuery = "SELECT DISTINCT marka_name FROM main ORDER BY marka_name ASC";
        $manufactures = ApiClient::callAuctionApi($manufcturesQuery, true);

        $models = [];
        if ($request->manufacturer) {
            $modelQuery = "SELECT DISTINCT MODEL_NAME FROM main WHERE marka_name='" . $request->manufacturer . "' ORDER BY MODEL_NAME ASC";
            $models = ApiClient::callAuctionApi($modelQuery, true);
        }

        $years = [];
        $chassisNumbers = [];
        $engineCapacity = [];
        $colorQuery = [];
        $ConditionQuery = [];
        $PriceQuery = [];
        $Mileages = [];

        if ($request->manufacturer && $request->model) {
            // get years
            $yearsQuery = "SELECT DISTINCT year FROM main WHERE marka_name='" . $request->manufacturer . "' AND MODEL_NAME='" . $request->model . "' ORDER BY year DESC";
            $years = ApiClient::callAuctionApi($yearsQuery, true);

            // get chassis number
            $chassisNoQuery = "SELECT DISTINCT kuzov FROM main WHERE marka_name='" . $request->manufacturer . "' AND model_name='" . $request->model . "' ";

            // get engine capacity
            $engineCapacityQuery = "SELECT DISTINCT eng_v FROM main WHERE marka_name='" . $request->manufacturer . "' AND model_name='" . $request->model . "' ";

            // get colors
            $vColorQuery = "SELECT DISTINCT COLOR FROM main WHERE marka_name='" . $request->manufacturer . "' AND model_name='" . $request->model . "' ";

            // get condition
            $vConditionQuery = "SELECT DISTINCT rate FROM main WHERE marka_name='" . $request->manufacturer . "' AND model_name='" . $request->model . "' ";

            // get price
            $vPriceQuery = "SELECT DISTINCT AVG_PRICE FROM main WHERE marka_name='" . $request->manufacturer . "' AND model_name='" . $request->model . "' ";

            // get Mileage
            $vMileageQuery = "SELECT DISTINCT MILEAGE FROM main WHERE marka_name='" . $request->manufacturer . "' AND MODEL_NAME='" . $request->model . "' ORDER BY MILEAGE ASC";


            // dd($vMileageQuery);

            if ($request->year_from == $request->year_to && $request->year_from != 0 && $request->year_to != 0) {
                if ($request->year_from != 0) {
                    $chassisNoQuery .= "AND year = '" . $request->year_from . "' ";
                }
                if ($request->mileage_from == $request->mileage_to) {
                    $vMileageQuery .= "AND MILEAGE = '" . $request->year_from . "' ";
                }

                $engineCapacityQuery .= "AND year = '" . $request->year_from . "' ";

                $vColorQuery .= "AND year = '" . $request->year_from . "' ";

                $vConditionQuery .= "AND year = '" . $request->year_from . "' ";

                $vPriceQuery .= "AND year = '" . $request->year_from . "' ";
            } else if ($request->year_from && $request->year_to) {
                if ($request->year_from != 0 && $request->year_to != 0) {

                    $chassisNoQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";
                }
                if ($request->mileage_from != 0 && $request->mileage_to != 0) {
                    $vMileageQuery .= "AND MILEAGE BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";
                }

                $engineCapacityQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";

                $vColorQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";

                $vConditionQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";

                $vPriceQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";
            }

            $chassisNoQuery .= "ORDER BY kuzov ASC";

            $chassisNumbers = ApiClient::callAuctionApi($chassisNoQuery, true);

            $Mileages = ApiClient::callAuctionApi($vMileageQuery, true);


            // dd($Mileages);

            if ($request->chassis) {
                $engineCapacityQuery .= "AND kuzov = '" . $request->chassis . "' ";

                $vColorQuery .= "AND kuzov = '" . $request->chassis . "' ";

                $vConditionQuery .= "AND kuzov = '" . $request->chassis . "' ";

                $vPriceQuery .= "AND kuzov = '" . $request->chassis . "' ";
            }

            if ($request->engine) {
                $vColorQuery .= "AND eng_v = '" . $request->engine . "' ";

                $vConditionQuery .= "AND eng_v = '" . $request->engine . "' ";

                $vPriceQuery .= "AND eng_v = '" . $request->engine . "' ";
            }
            if ($request->grade) {

                $vPriceQuery .= "AND RATE = '" . $request->grade . "' ";
            }

            $engineCapacityQuery .= "order by eng_v ASC";
            $engineCapacity = ApiClient::callAuctionApi($engineCapacityQuery, true);

            $vConditionQuery .= "order by RATE ASC";
            $ConditionQuery = ApiClient::callAuctionApi($vConditionQuery, true);

            $vPriceQuery .= "order by AVG_PRICE ASC";
            $PriceQuery = ApiClient::callAuctionApi($vPriceQuery, true);

            $vColorQuery .= " order by COLOR ASC";
            $colorQuery = ApiClient::callAuctionApi($vColorQuery, true);
        } elseif (!$request->manufacturer && !$request->model) {
            $engineCapacityQuery = "SELECT DISTINCT eng_v FROM main";
            $engineCapacity = ApiClient::callAuctionApi($engineCapacityQuery, true);
            // dd($engineCapacity);
        }

        // $vehiclesList = [];
        // if($request->search == 'true') {
        // dd( $ConditionQuery);
        $vehiclesList = $this->getVehicleList($request);
        $vehiclesListCount = $this->getVehicleListCount($request);
        $vehiclesListCount = count($vehiclesListCount) > 0 ? $vehiclesListCount[0]['TAG0'] : 0;
        // }


        return Inertia::render('Live_auction/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries,
            'requestQuery' => $requestData,
            'auctionDates' => $auctionDates,
            'manufactures' => $manufactures,
            'models' => $models,
            'years' => $years,
            'chassisNumbers' => $chassisNumbers,
            'engineCapacity' => $engineCapacity,
            'colorQuery' => $colorQuery,
            'vehiclesList' => $vehiclesList,
            'vehiclesListCount' => $vehiclesListCount,
            'Condition' => $ConditionQuery,
            'PriceQuery' => $PriceQuery,
            'Mileage' => $Mileages,
        ]);
    }
    public function service()
    {
        $countries = Country::where(['status' => 1])->get();

        // dd('test');
        return Inertia::render('Services/index', ['countries' => $countries]);
    }
    public function available(Request $request, $countrySlug = null)
    {

        // dd($request->all());
        // Get all filter data
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $requestData = $request->all();
        $vehicleTypes = Type::where(['status' => 1])->with('media')->get();
        $manufacturers = Manufacture::where(['status' => 1])->with('media')->get();
        $models = VehicleModel::where(['status' => 1])->get();
        $countries = Country::where(['status' => 1])->get();

        // Base query
        $vehicles = Vehicle::where(['status' => 1]);

        // Handle country slug from URL
        $selectedCountry = null;
        if ($countrySlug) {
            $selectedCountry = Country::whereRaw("LOWER(REPLACE(name, ' ', '-')) = ?", [strtolower($countrySlug)])->first();
            if ($selectedCountry) {
                $vehicles->whereRaw('JSON_CONTAINS(countries, ?)', [json_encode((string) $selectedCountry->id)]);
            }
        }

        // Additional filters from request (if no slug is provided)
        if (!$countrySlug && $request->country) {
            if (is_array($request->country)) {
                $vehicles->where(function ($query) use ($request) {
                    foreach ($request->country as $countryId) {
                        $query->orWhereRaw('JSON_CONTAINS(countries, ?)', [json_encode((string) $countryId)]);
                    }
                });
            } else {
                $vehicles->whereRaw('JSON_CONTAINS(countries, ?)', [json_encode((string) $request->country)]);
            }
        }

        // Other filters (unchanged)
        if ($request->brand) {
            $vehicles->whereIn('manufacture_id', is_array($request->brand) ? $request->brand : [$request->brand]);
        }

        if ($request->model) {
            $vehicles->whereIn('vehicle_model_id', is_array($request->model) ? $request->model : [$request->model]);
        }

        if ($request->type) {
            $types = is_array($request->type) ? $request->type : [$request->type];

            $vehicles->where(function ($q) use ($types) {
                foreach ($types as $typeId) {
                    $q->orWhereRaw(
                        "JSON_CONTAINS(vehicle_type_id, ?)",
                        [json_encode((string) $typeId)]
                    );
                }
            });
        }


        if ($request->drive_type) {
            $vehicles->whereIn('drive_type', is_array($request->drive_type) ? $request->drive_type : [$request->drive_type]);
        }

        if ($request->engineCC) {
            $vehicles->where('engine_capacity', '>=', $request->engineCC);
        }

        if ($request->sortBy) {
            switch ($request->sortBy) {
                case 'priceLowHigh':
                    $vehicles->orderBy('initial_payment', 'asc');
                    break;
                case 'priceHighLow':
                    $vehicles->orderBy('initial_payment', 'desc');
                    break;
                case 'newest':
                    $vehicles->orderBy('year', 'desc');
                    break;
            }
        }

        if ($request->keyword) {
            $vehicles->where(function ($query) use ($request) {
                $query->where('drive_type', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhereHas('manufacture', function ($q) use ($request) {
                        $q->where('title', 'LIKE', '%' . $request->keyword . '%');
                    })
                    ->orWhereHas('vehicleModel', function ($q) use ($request) {
                        $q->where('title', 'LIKE', '%' . $request->keyword . '%');
                    })
                    ->orWhereHas('vehicleType', function ($q) use ($request) {
                        $q->where('title', 'LIKE', '%' . $request->keyword . '%');
                    });
            });
        }

        //add pagination
        $vehicles = $vehicles
            ->with('media', 'vehicleType', 'manufacture', 'vehicleModel')
            ->orderBy('year', 'desc')
            ->paginate(
                12
            )
            ->appends($request->all());

        return Inertia::render('Available_stock/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'vehicleTypes' => $vehicleTypes,
            'manufacturers' => $manufacturers,
            'models' => $models,
            'vehicles' => $vehicles,
            'requestQuery' => $requestData,
            'countries' => $countries,
            'selectedCountry' => $selectedCountry ? ['id' => $selectedCountry->id, 'name' => $selectedCountry->name] : null,
        ]);
    }
    public function product(Request $request)
    {
        if (!$request->id) {
            return redirect()->back();
        }
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $countries = Country::where(['status' => 1])->get();
        $featuredVehicles = Vehicle::where(['status' => 1, 'featured' => 1])->with('media', 'manufacture', 'vehicleModel')->get();
        //  dd($featuredVehicles);

        $vehicle = Vehicle::with('manufacture', 'vehicleModel', 'vehicleType', 'exColor', 'inColor', 'media')->find($request->id);

        $randomVehicles = Vehicle::with('media', 'manufacture', 'vehicleModel')
            ->where('id', '!=', $vehicle->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $features = json_decode($vehicle->features, true);
        //    $features  = json_decode($features, true);
        $features = json_decode($vehicle->features, true);
        if (!is_array($features)) {
            $features = [];
        }
        $features = Feature::whereIn('id', $features)->get();
        return Inertia::render('Product_view/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'vehicle' => $vehicle,
            'features' => $features,
            'randomVehicles' => $randomVehicles,
            'countries' => $countries,
            'featuredVehicles' => $featuredVehicles,
        ]);
    }
    public function faq()
    {
        $countries = Country::where(['status' => 1])->get();
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        // dd('test');
        return Inertia::render('FAQ/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries
        ]);
    }
    public function knowledge()
    {
        $countries = Country::where(['status' => 1])->get();
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        // dd('test');
        return Inertia::render('Knowledge/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries
        ]);
    }
    public function testimonials()
    {
        // dd('test');
        $countries = Country::where(['status' => 1])->get();
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $testimonials = Testimonial::where('status', 1)->with('media')->get();
        // dd($testimonials->all());

        $makes = Manufacture::where('status', 1)->orderBy('title')->get();
        $models = VehicleModel::where('status', 1)->orderBy('title')->get();
        $fuelPrices = FuelPrice::all();

        $vehicles = Vehicle::where('status', 1)
            ->with(['manufacture', 'vehicleModel'])
            ->get();


        return Inertia::render('Testimonials/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries,
            'testimonials' => $testimonials,


            'makes' => $makes,
            'models' => $models,
            'fuelPrices' => $fuelPrices,
            'vehicles' => $vehicles,
        ]);
    }
    public function contact()
    {
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $countries = Country::where(['status' => 1])->get();
        // dd('test');
        return Inertia::render('Contact_us/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries
        ]);
    }
    public function HowtoOrder()
    {
        // dd('test');
        $countries = Country::where(['status' => 1])->get();

        $site_url = 'https://nikoba.com';

        return Inertia::render('How_to_order/index', ['countries' => $countries, 'site_url' => $site_url]);
    }
    public function login(Request $request)
    {

        if ($request->filled('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        }
        // dd('test');
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $countries = Country::where(['status' => 1])->get();

        return Inertia::render('Login/index', [
            'redirect' => $request->query('redirect'),
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries
        ]);
    }
    public function register(Request $request)
    {
        if ($request->filled('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        }
        // dd('test');
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $countries = Country::where(['status' => 1])->get();

        return Inertia::render('Register/index', [
            'redirect' => $request->query('redirect'),
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries
        ]);
    }
    public function dealerregister()
    {
        // dd('test');
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $countries = Country::where(['status' => 1])->get();

        return Inertia::render('Register/dealerindex', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries
        ]);
    }
    public function dealerstore(Request $request)
    {
        // dd($request->all());
        // dd($request);
        $request->validate([
            'name' => ['required'],
            'email' => ['required', 'unique:backend_users,email'],
            'mobile_no' => ['nullable', 'unique:backend_users,mobile_no'],
            'password' => ['required', 'confirmed'],
            'profile_image' => ['nullable', 'mimes:jpeg,jpg,png,webp', 'max:10000'],
            'bank_name' => ['required'],
            'branch' => ['required'],
            'acc_number' => ['required'],
            'payee_name' => ['required'],
            'branch_code' => ['required'],
            'passbook_image' => ['required', 'mimes:jpeg,jpg,png,webp', 'max:10000'],
        ]);
        // dd($request->all());
        try {
            DB::beginTransaction();

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile_no = $request->mobile_no;

            $user->bank_name = $request->bank_name;
            $user->branch = $request->branch;
            $user->acc_number = $request->acc_number;
            $user->payee_name = $request->payee_name;
            $user->branch_code = $request->branch_code;

            $user->email_verified_at = now();
            $user->password = Hash::make($request->password);
            $user->status = 1;
            $user->save();

            DB::commit();

            $role = Role::find(7);
            $user->assignRole($role->name);


            if ($request->hasFile('passbook_image')) {
                $file = $user->addMedia($request->file('passbook_image'))->toMediaCollection('passbook');
                $user->passbook_image = str_replace(config('app.url'), '', $file->getUrl());
                $user->save();
            }

            return redirect()->route('index');
        } catch (Exception $ex) {
            dd($ex);
            DB::rollBack();
            return abort(500);
        }
    }


    public function signup()
    {
        // dd('test');
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        return Inertia::render('Login/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures
        ]);
    }

    public function forgotpassword()
    {
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        // dd('test'); $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        return Inertia::render('Forgot_password/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures
        ]);
    }
    public function profile()
    {
        $countries = Country::where(['status' => 1])->get();
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $inquires = Inquiry::where('email', auth('customer')->user()?->email)->get();
        $affilates = DB::table('affiliates')->where('affiliate_id', auth('customer')->user()?->enrolled_affiliate_id)->get();
        $affilatesUserIDs = $affilates->pluck('customer_id');
        $affilateUsers = Customer::with('media')->whereIn('id', $affilatesUserIDs)->get();

        $affilateUsersCount = count($affilateUsers);
        $settingsAffiliate = Settings::where(['key' => 'affilate_point_value'])->first()->value ?? 0;
        $affiliatePoints = (int) $affilateUsersCount * (float) $settingsAffiliate;


        return Inertia::render('User_profile/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'inquires' => $inquires,
            'affilateUsers' => $affilateUsers,
            'affiliatePoints' => $affiliatePoints,
            'countries' => $countries
        ]);
    }
    public function PrivacyPolicy()
    {
        // dd('test');
        return Inertia::render('PrivacyPolicy/index');
    }
    public function NewPassword(Request $request)
    {
        // dd('test');
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        return Inertia::render('NewPassword/index', [
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'token' => $request->token
        ]);
    }

    public function getVehicleList(Request $request)
    {
        // dd($request);

        $vehiclesList = [];
        $query = "SELECT * FROM main WHERE 1=1 ";



        if ($request->search == 'true') {
            if ($request->manufacturer) {
                $query .= "AND marka_name = '" . $request->manufacturer . "' ";
            }

            if ($request->model) {
                $query .= "AND model_name = '" . $request->model . "' ";
            }

            if ($request->year_to == 0 && $request->year_from != 0) {
                $query .= "AND year >= '" . $request->year_from . "' ";
            } else if ($request->year_from == 0 && $request->year_to != 0) {
                $query .= "AND year <= '" . $request->year_to . "' ";
            } else if ($request->year_from != 0 && $request->year_to != 0) {
                $query .= "AND year >= '" . $request->year_from . "' AND year <= '" . $request->year_to . "' ";
            }

            if ($request->mileage_to == 0 && $request->mileage_from != 0) {
                $query .= "AND MILEAGE >= '" . $request->year_from . "' ";
            } else if ($request->mileage_from == 0 && $request->mileage_to != 0) {
                $query .= "AND MILEAGE <= '" . $request->mileage_to . "' ";
            } else if ($request->mileage_from != 0 && $request->mileage_to != 0) {
                $query .= "AND MILEAGE >= '" . $request->mileage_from . "' AND MILEAGE <= '" . $request->mileage_to . "' ";
            }

            if ($request->chassis) {
                $query .= "AND kuzov = '" . $request->chassis . "' ";
            }


            if ($request->engine) {
                $query .= "AND eng_v = '" . $request->engine . "' ";
            }


            if ($request->color) {
                $query .= "AND COLOR = '" . $request->color . "' ";
            }
            if ($request->grade) {
                $query .= "AND rate = '" . $request->grade . "' ";
            }

            if ($request->start_price) {
                $query .= "AND AVG_PRICE >= '" . $request->start_price . "' ";
            }

            if ($request->lot_no) {
                $query .= "AND LOT = '" . $request->lot_no . "' ";
            }
            // dd(explode(' ', $request->available_days)[0]);
            // if ($request->available_days) {
            //     $query .= "AND AUCTION_DATE LIKE '%" . explode(' ', $request->available_days)[0] . "%' ";
            //     // TODO: filter auctio for multiple available dates
            // }
            $page = $request->page ?? 1;
            $pageOffset = ($page - 1) * 12;

            $query .= ' ORDER BY year DESC LIMIT ' . $pageOffset . ',12';

            $vehiclesList = ApiClient::callAuctionApi($query);
        }


        return $vehiclesList;
    }

    public function getVehicleListCount(Request $request)
    {

        $vehiclesList = [];
        $query = "SELECT COUNT(*) FROM main WHERE 1=1 ";

        if ($request->search == 'true') {
            if ($request->manufacturer) {
                $query .= "AND marka_name = '" . $request->manufacturer . "' ";
            }

            if ($request->model) {
                $query .= "AND model_name = '" . $request->model . "' ";
            }

            if ($request->year_to == 0 && $request->year_from != 0) {
                $query .= "AND year >= '" . $request->year_from . "' ";
            } else if ($request->year_from == 0 && $request->year_to != 0) {
                $query .= "AND year <= '" . $request->year_to . "' ";
            } else if ($request->year_from != 0 && $request->year_to != 0) {
                $query .= "AND year >= '" . $request->year_from . "' AND year <= '" . $request->year_to . "' ";
            }

            if ($request->chassis) {
                $query .= "AND kuzov = '" . $request->chassis . "' ";
            }

            if ($request->engine) {
                $query .= "AND eng_v = '" . $request->engine . "' ";
            }

            // if ($request->color) {
            //     $query .= "AND COLOR = '" . $request->color . "' ";
            // }

            if ($request->lot_no) {
                $query .= "AND LOT = '" . $request->lot_no . "' ";
            }
            // dd(explode(' ', $request->available_days)[0]);
            // if ($request->available_days) {
            //     $query .= "AND AUCTION_DATE LIKE '%" . explode(' ', $request->available_days)[0] . "%' ";
            //     // TODO: filter auctio for multiple available dates
            // }

            $query .= ' ORDER BY year DESC';

            $vehiclesList = ApiClient::callAuctionApi($query);
        }


        return $vehiclesList;
    }

    public function getVehicleStatsList(Request $request)
    {

        $query = "SELECT * FROM stats WHERE 1=1 ";
        if ($request->manufacture) {
            $query .= "AND marka_name = '" . $request->manufacture . "' ";
        }

        if ($request->model) {
            $query .= "AND model_name = '" . $request->model . "' ";
        }

        if ($request->chassis_no) {
            $query .= "AND kuzov = '" . $request->chassis_no . "' ";
        }

        if ($request->engine) {
            $query .= "AND eng_v = '" . $request->engine . "' ";
        }

        if ($request->year) {
            $query .= "AND year = '" . $request->year . "' ";
        }

        if ($request->rate) {
            $query .= "AND rate = '" . $request->rate . "' ";
        }

        $page = $request->page > 0 ? $request->page : 1;
        $pageOffset = ($page - 1) * 10;

        $query .= "ORDER BY auction_date DESC LIMIT " . $pageOffset . ",10";

        // dd($query);
        $vehiclesStatList = ApiClient::callAuctionApi($query);

        return $vehiclesStatList;
    }
    public function AppDownload()
    {
        // dd('test');
        return Inertia::render('Download/AppDownload');
    }

    public function auctionStat(Request $request)
    {
        // dd($request->all());
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
        $id = $request->id;
        $requestData = $request->all();

        if (!$id) {
            return redirect()->back();
        }

        $query = "SELECT * FROM stats WHERE id = '" . $id . "' order by marka_name ASC";
        $details = ApiClient::callAuctionApi($query, true);

        // dd($id);

        $details = count($details) > 0 ? $details[0] : null;

        $images = $details ? explode('#', $details['IMAGES']) : [];

        // stats filter queries
        $statsYearQuery = "SELECT count(year), year FROM stats ";
        $statsChassiesQuery = "SELECT count(kuzov), kuzov FROM stats ";
        $statsConditionQuery = "SELECT count(rate), rate FROM stats ";

        if ($request->manufacture) {
            $statsYearQuery .= "WHERE marka_name = '" . $request->manufacture . "' ";
            $statsChassiesQuery .= "WHERE marka_name = '" . $request->manufacture . "' ";
            $statsConditionQuery .= "WHERE marka_name = '" . $request->manufacture . "' ";
        }

        if ($request->model) {
            $statsYearQuery .= "AND model_name = '" . $request->model . "' ";
            $statsChassiesQuery .= "AND model_name = '" . $request->model . "' ";
            $statsConditionQuery .= "AND model_name = '" . $request->model . "' ";
        }

        if ($request->chassis_no) {
            $statsConditionQuery .= "AND kuzov = '" . $request->chassis_no . "' ";
        }

        if ($request->year) {
            $statsChassiesQuery .= "AND year = '" . $request->year . "' ";
            $statsConditionQuery .= "AND year = '" . $request->year . "' ";
            $statsConditionQuery .= "AND year = '" . $request->year . "' ";
        }

        $statsYearQuery .= "GROUP BY year ORDER BY year DESC";
        $statsChassiesQuery .= 'GROUP BY kuzov ORDER BY kuzov ASC';
        $statsConditionQuery .= 'GROUP BY rate ORDER BY rate ASC';

        $statsYears = ApiClient::callAuctionApi($statsYearQuery);
        $statsChassis = ApiClient::callAuctionApi($statsChassiesQuery, true);
        $statsConditions = ApiClient::callAuctionApi($statsConditionQuery, true);

        $vehicleStatsList = $this->getVehicleStatsList($request);

        $countries = Country::where(['status' => 1])->get();
        // dd($countries);
        return Inertia::render('Auction_view/index', ['details' => $details, 'vImages' => $images, 'statsYears' => $statsYears, 'statsChassis' => $statsChassis, 'statsConditions' => $statsConditions, 'requestQuery' => $requestData, 'vehicleStatsList' => $vehicleStatsList, 'countries' => $countries, 'vehicle_types' => $vehicle_types, 'category_manufactures' => $category_manufactures]);
    }

    public function appAuction()
    {
        return Inertia::render('Auction/index');
    }

    public function appDealer()
    {
        return Inertia::render('Dealer/index');
    }
    public function exportVehicleStatsList()
    {
        set_time_limit(600); // optional: increase execution time

        $limit = 250;
        $offset = 0;
        $batchNumber = 1;
        $tempFiles = [];

        // Clean export folder
        Storage::deleteDirectory('exports');
        Storage::makeDirectory('exports');

        do {
            $query = "SELECT * FROM stats WHERE 1=1 ORDER BY auction_date DESC LIMIT $offset, $limit";
            $batch = ApiClient::callAuctionApi($query);

            if (is_array($batch) && count($batch)) {
                $fileName = "vehicle_stats_batch_{$batchNumber}.xlsx";
                $filePath = "exports/{$fileName}";

                Excel::store(new VehicleStatsExport($batch), $filePath, null, \Maatwebsite\Excel\Excel::XLSX);
                $tempFiles[] = storage_path("app/" . $filePath);

                $offset += $limit;
                $batchNumber++;
            } else {
                break;
            }
        } while (true);

        if (empty($tempFiles)) {
            return back()->with('error', 'No data found.');
        }

        // Zip the files
        $zipPath = storage_path("app/exports/vehicle_stats_batches.zip");
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($tempFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }


    public function testexcel()
    {
        $query = "SELECT * FROM stats WHERE 1=1 ORDER BY auction_date DESC";

        $vehicles = ApiClient::callAuctionApi($query);

        if (empty($vehicles)) {
            return redirect()->back()->with('error', 'No data found.');
        }

        return Excel::download(new VehicleStatsExport($vehicles), 'vehicle_stats.xlsx');
    }



    public function exportView(Request $request)
    {
        $countries = Country::where(['status' => 1])->get();
        $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
        $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();

        $manufcturesQuery = "SELECT DISTINCT marka_name FROM main ORDER BY marka_name ASC";
        $manufactures = ApiClient::callAuctionApi($manufcturesQuery, true);

        $models = [];
        if ($request->manufacturer) {
            $modelQuery = "SELECT DISTINCT MODEL_NAME FROM main WHERE marka_name='" . $request->manufacturer . "' ORDER BY MODEL_NAME ASC";
            $models = ApiClient::callAuctionApi($modelQuery, true);
        }
        $engineCapacity = [];
        $chassisNumbers = [];
        $grades = [];
        $auctionGrades = [];

        if ($request->manufacturer && $request->model) {

            $engineCapacityQuery = "SELECT DISTINCT eng_v FROM main WHERE marka_name='" . $request->manufacturer . "' AND model_name='" . $request->model . "' ";
            $engineCapacityQuery .= "order by eng_v ASC";
            $engineCapacity = ApiClient::callAuctionApi($engineCapacityQuery, true);

            $chassisNoQuery = "SELECT DISTINCT kuzov FROM main WHERE marka_name='" . $request->manufacturer . "' AND MODEL_NAME='" . $request->model . "' ";
            $chassisNoQuery .= "ORDER BY kuzov ASC";
            $chassisNumbers = ApiClient::callAuctionApi($chassisNoQuery, true);

            $gradeQuery = "SELECT DISTINCT GRADE FROM main WHERE marka_name='" . $request->manufacturer . "' AND MODEL_NAME='" . $request->model . "' ";
            $gradeQuery .= "ORDER BY GRADE ASC";
            $gradesRaw = ApiClient::callAuctionApi($gradeQuery, true);

            $vConditionQuery = "SELECT DISTINCT rate FROM main WHERE marka_name='" . $request->manufacturer . "' AND model_name='" . $request->model . "' ";
            $vConditionQuery .= "order by rate ASC";
            $auctionGrades = ApiClient::callAuctionApi($vConditionQuery, true);

            // Decode HTML entities
            $grades = array_map(function ($gradeItem) {
                if (isset($gradeItem['GRADE'])) {
                    $gradeItem['GRADE'] = html_entity_decode($gradeItem['GRADE'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
                return $gradeItem;
            }, $gradesRaw);
        }

        // dd($manufactures);
        return Inertia::render('ExportStats/Index', [
            'manufactures' => $manufactures,
            'models' => $models,
            'engineCapacity' => $engineCapacity,
            'chassisNumbers' => $chassisNumbers,
            'grades' => $grades,
            'auctionGrades' => $auctionGrades,
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'countries' => $countries
        ]);
    }

    // public function exportMultipleModelStats(Request $request)
    // {

    //     // dd($request->all());
    //     set_time_limit(600); // Increase time for big jobs

    //     $marka = $request->manufacturer;
    //     $model = $request->model;
    //     $eng_v = $request->engine;

    //     if (!$marka || !$model || !$eng_v) {
    //         return back()->with('error', 'Missing required vehicle parameters.');
    //     }

    //     $combinations = [
    //         ['marka' => $marka, 'model' => $model, 'eng_v' => $eng_v]
    //     ];

    //     $tempFiles = [];
    //     Storage::deleteDirectory('exports');
    //     Storage::makeDirectory('exports');

    //     foreach ($combinations as $index => $combo) {
    //         $marka = addslashes($combo['marka']);
    //         $model = addslashes($combo['model']);
    //         $eng_v = addslashes($combo['eng_v']);

    //         $vehicles = [];
    //         $offset = 0;
    //         $limit = 250;

    //         while ($offset < 1000) {
    //             $query = "SELECT * FROM stats
    //             WHERE marka_name = '{$marka}'
    //               AND model_name = '{$model}'
    //               AND eng_v = '{$eng_v}'
    //               AND year BETWEEN 2023 AND 2025
    //               AND finish > 0
    //               AND status IN ('SOLD', 'Sold', 'Sold By Nego')
    //             ORDER BY auction_date DESC
    //             LIMIT {$offset}, {$limit}";

    //             $batch = ApiClient::callAuctionApi($query);

    //             if (empty($batch)) {
    //                 break; // No more records
    //             }

    //             $vehicles = array_merge($vehicles, $batch);

    //             if (count($batch) < $limit) {
    //                 break; // Last batch
    //             }

    //             $offset += $limit;
    //         }

    //         if (!empty($vehicles)) {
    //             $filename = "stats_{$marka}_{$model}_{$eng_v}.xlsx";
    //             $filepath = "exports/" . $filename;

    //             Excel::store(new VehicleStatsExport($vehicles), $filepath, null, \Maatwebsite\Excel\Excel::XLSX);
    //             $tempFiles[] = storage_path("app/" . $filepath);
    //         }
    //     }

    //     if (empty($tempFiles)) {
    //         return back()->with('error', 'No data found for any combination.');
    //     }

    //     $zipPath = storage_path("app/exports/vehicle_stats_models.zip");
    //     $zip = new ZipArchive;
    //     if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    //         foreach ($tempFiles as $file) {
    //             $zip->addFile($file, basename($file));
    //         }
    //         $zip->close();
    //     }

    //     return response()->download($zipPath)->deleteFileAfterSend(true);
    // }


    public function exportMultipleModelStats(Request $request)
    {
        // Log::info($request->all());
        set_time_limit(600);

        $marka = $request->manufacturer;
        $model = $request->model;
        $eng_v = $request->engine;
        $chassis = $request->chassis;
        $grade = $request->grade;
        $auctiongrade = $request->auctiongrade;

        if (!$marka || !$model || !$eng_v) {
            return back()->with('error', 'Missing required vehicle parameters.');
        }

        $vehicles = [];
        $offset = 0;
        $limit = 250;

        $marka_escaped = addslashes($marka);
        $model_escaped = addslashes($model);
        $eng_v_escaped = addslashes($eng_v);
        $chassis_condition = '';
        $grade_condition = '';
        $auctiongrade_condition = '';

       if ($chassis && $chassis != 'All') {
            $chassis_escaped = addslashes($chassis);
            $chassis_condition = " AND kuzov = '{$chassis_escaped}'";
        }
        if ($grade && $grade != 'All') {
            $grade_escaped = addslashes($grade);
            $grade_condition = " AND GRADE = '{$grade_escaped}'";
        }
        if ($auctiongrade && $auctiongrade != 'All') {
            $auctiongrade_escaped = addslashes($auctiongrade);
            $auctiongrade_condition = " AND rate = '{$auctiongrade_escaped}'";
        }

        while ($offset < 1000) {
            $query = "SELECT * FROM stats
            WHERE marka_name = '{$marka_escaped}'
              AND model_name = '{$model_escaped}'
              AND eng_v = '{$eng_v_escaped}'
                 {$chassis_condition}
                 {$grade_condition}
                 {$auctiongrade_condition}
              AND year BETWEEN 2023 AND 2025
              AND finish > 0
              AND status IN ('SOLD', 'Sold', 'Sold By Nego')
            ORDER BY auction_date DESC
            LIMIT {$offset}, {$limit}";

            $batch = ApiClient::callAuctionApi($query);

            if (empty($batch)) {
                break;
            }

            $vehicles = array_merge($vehicles, $batch);
            Log::info($vehicles);
            if (count($batch) < $limit) {
                break;
            }

            $offset += $limit;
        }

        if (empty($vehicles)) {
            return back()->with('error', 'No data found for the selected combination.');
        }

        $filename = "stats_{$marka}_{$model}_{$eng_v}" . ($chassis ? "_{$chassis}" : "") . ".xlsx";
        return Excel::download(new VehicleStatsExport($vehicles), $filename);
    }

    public function viewStats(Request $request)
    {
        Log::info($request->all());
        set_time_limit(600);

        $marka = $request->manufacturer;
        $model = $request->model;
        $eng_v = $request->engine;
        $chassis = $request->chassis;
        $grade = $request->grade;
        $auctiongrade = $request->auctiongrade;

        if (!$marka || !$model || !$eng_v) {
            return back()->with('error', 'Missing required vehicle parameters.');
        }

        $marka_escaped = addslashes($marka);
        $model_escaped = addslashes($model);
        $eng_v_escaped = addslashes($eng_v);
        $chassis_condition = '';
        $grade_condition = '';
        $auctiongrade_condition = '';

        if ($chassis && $chassis != 'All') {
            $chassis_escaped = addslashes($chassis);
            $chassis_condition = " AND kuzov = '{$chassis_escaped}'";
        }
        if ($grade && $grade != 'All') {
            $grade_escaped = addslashes($grade);
            $grade_condition = " AND GRADE = '{$grade_escaped}'";
        }
        if ($auctiongrade && $auctiongrade != 'All') {
            $auctiongrade_escaped = addslashes($auctiongrade);
            $auctiongrade_condition = " AND rate = '{$auctiongrade_escaped}'";
        }

        $yearStats = [];


        foreach ([2023, 2024, 2025] as $year) {
            $vehicles = [];
            $offset = 0;
            $limit = 250;

            while ($offset < 1000) {
                $query = "SELECT * FROM stats
                WHERE marka_name = '{$marka_escaped}'
                  AND model_name = '{$model_escaped}'
                  AND eng_v = '{$eng_v_escaped}'
                      {$chassis_condition}
                 {$grade_condition}
                  {$auctiongrade_condition}
                  AND year = {$year}
                  AND finish > 0
                  AND status IN ('SOLD', 'Sold', 'Sold By Nego')
                ORDER BY auction_date DESC
                LIMIT {$offset}, {$limit}";

                $batch = ApiClient::callAuctionApi($query);

                if (empty($batch)) {
                    break;
                }

                $vehicles = array_merge($vehicles, $batch);
                // Log::info($vehicles);
                if (count($batch) < $limit) {
                    break;
                }

                $offset += $limit;
            }

            $totalFinish = array_sum(array_column($vehicles, 'FINISH'));
            $vehicleCount = count($vehicles);
            $average = $vehicleCount > 0 ? round($totalFinish / $vehicleCount, 2) : 0;

            $finishPrices = array_column($vehicles, 'FINISH');
            $minPrice = !empty($finishPrices) ? min($finishPrices) : 0;
            $maxPrice = !empty($finishPrices) ? max($finishPrices) : 0;

            $yearStats[$year] = [
                'year' => $year,
                'vehicles' => $vehicleCount,
                'total_finish' => $totalFinish,
                'average_finish_price' => $average,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
            ];
        }

        return response()->json([
            'marka' => $marka,
            'model' => $model,
            'engine' => $eng_v,
            'statistics' => array_values($yearStats)
        ]);
    }
}
