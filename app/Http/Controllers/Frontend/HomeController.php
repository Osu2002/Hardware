<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\LiveAuctionManufacturer;
use App\Models\Manufacture;
use App\Models\Settings;
use App\Models\Type;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Services\ApiClient\ApiClient;
use Arr;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function maintain()
    {
        return Inertia::render('Home/maintain');
    }
    public function index(Request $request)
    {
        // dd($request->all());
          $category_manufactures = LiveAuctionManufacturer::where(['status' => 1])->get();
    $vehicle_types = Type::where(['status' => 1, 'featured' => 1])->get();
    $models = VehicleModel::where(['status' => 1])
        ->where(['manufacture_id' => $request->manufacture_id])
        ->get();
    $vehicles = Vehicle::where(['status' => 1]);
    $in_stock = Vehicle::where([
            'availability' => 'Available',
            'status' => 1,
            'featured' => 1
        ])
        ->with('media', 'manufacture', 'vehicleModel')
        ->get();

    $countries = Country::all();

    // ---- settings with safe fallback ----
    $manufacturesSetting = Settings::where('key', 'manufactures')->first();
    $modelsSetting       = Settings::where('key', 'models')->first();
    $yearFromSetting     = Settings::where('key', 'year_from')->first();
    $yearToSetting       = Settings::where('key', 'year_to')->first();

    $manufactures = $manufacturesSetting
        ? json_decode($manufacturesSetting->value, true)
        : []; // default empty array

    $modelsList = $modelsSetting
        ? json_decode($modelsSetting->value, true)
        : []; // default empty array

    $year_from = $yearFromSetting
        ? $yearFromSetting->value
        : null; // or a sensible default like 2000

    $year_to = $yearToSetting
        ? $yearToSetting->value
        : null; // or now() year


        $query = "SELECT * FROM main WHERE 1=1 ";

        if (count($manufactures) > 0) {
            foreach ($manufactures as $key => $value) {
                if ($key == 0) {
                    $query .= "AND marka_name = '" . $value . "' ";
                } else {
                    $query .= "OR marka_name = '" . $value . "' ";
                }
            }
        }

        if (count($models) > 0) {
            if ($key == 0) {
                $query .= "AND model_name = '" . $value . "' ";
            } else {
                $query .= "OR model_name = '" . $value . "' ";
            }
        }

        if ($year_from && $year_to) {
            $query .= "AND year >= '" . $year_from . "' AND year <= '" . $year_to . "' ";
        }


        $query .= ' ORDER BY year DESC LIMIT 4';

        $live_auction_vehicles_list = ApiClient::callAuctionApi($query);

        foreach ($live_auction_vehicles_list as $key => $value) {
            // dd($value['IMAGES']);
            $images = explode('#', $value['IMAGES']);
            $live_auction_vehicles_list[$key]['media'] = $images;

            foreach ($live_auction_vehicles_list[$key]['media'] as $k => $url) {
                $url = str_replace('&h=50', '', $url);
                $live_auction_vehicles_list[$key]['media'][$k] = $url;
            }
        }

        // /////////////////////////////////

        $currentYear = date('Y');
        $baseYear = $currentYear - 5;

        $latest_cars = Vehicle::where(['availability' => 'Available', 'status' => 1, 'featured' => 1])->whereBetween('year', [$baseYear, $currentYear])->with('media', 'manufacture', 'vehicleModel')->limit(4)->latest()->get();

        // dd($latest_cars);

        if ($request->manufacture_id) {
            $vehicles = $vehicles->where('manufacture_id', $request->manufacture_id);
        }

        if ($request->vehicle_type_id) {
            $vehicles = $vehicles->where('vehicle_type_id', $request->vehicle_type_id);
        }

        if ($request->model_id) {
            $vehicles = $vehicles->where('vehicle_model_id', $request->model_id);
        }


        $minYear = clone $vehicles;
        $minYear = $minYear->min('year');
        $maxYear = clone $vehicles;
        $maxYear = $maxYear->max('year');

        $minMileage = clone $vehicles;
        $minMileage = $minMileage->min('mileage');
        $maxMileage = clone $vehicles;
        $maxMileage = $maxMileage->max('mileage');

        $years = range($minYear, $maxYear);
        $years = array_filter($years);

        $featuredVehicles = Vehicle::where(['status' => 1, 'featured' => 1])->with('media', 'manufacture', 'vehicleModel', 'vehicleType')->get();
        // dd($featuredVehicles);

        $reviews = [
            [
                'name' => 'Fahim M.S.M.',
                'title' => 'Greate Work',
                'rating' => 5,
                'comment' => 'From the moment I engaged with this team, I was thoroughly impressed by their professionalism and design capabilities. The UI/UX quality is top-notch, and the system’s customizability allows us to tailor everything exactly to our business needs. Performance-wise, everything runs smoothly and efficiently—clearly optimized for a cloud environment. This has elevated our customer experience significantly. Kudos to the developers!',
                'duration' => '5 years ago',
                'image' => 'https://plus.unsplash.com/premium_photo-1689551670902-19b441a6afde?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cmFuZG9tJTIwcGVvcGxlfGVufDB8fDB8fHww'
            ],
            [
                'name' => 'Chatura Fernando',
                'title' => 'Awesome Design',
                'rating' => 5,
                'comment' => 'The entire process, from inquiry to post-sale support, has been absolutely seamless. The website’s design is both modern and intuitive, making it easy for our team and clients to navigate. Vehicle listings are well-organized and aesthetically pleasing. More than just visual appeal, the service we received was quick, helpful, and professional. Highly recommended!',
                'duration' => '6 years ago',
                'image' => 'https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/35af6a41332353.57a1ce913e889.jpg'
            ],
            [
                'name' => 'Chaminda Thilakarathna',
                'title' => 'Good Job',

                'rating' => 5,
                'comment' => 'One of the best decisions I made was working with this company to import vehicles. The system is incredibly efficient, making the entire purchasing process smooth and hassle-free. The user interface is well-designed, fast-loading, and responsive across all devices. Truly a company that values user experience and customer satisfaction.',
                'duration' => '6 years ago',
                'image' => 'https://img.freepik.com/free-photo/close-up-portrait-curly-handsome-european-male_176532-8133.jpg?semt=ais_hybrid&w=740'
            ],
            [
                'name' => 'Dr. Sanjaya Wimalarathna',
                'title' => 'Awesome',

                'rating' => 5,
                'comment' => 'I was genuinely impressed by the pricing and overall value offered by the platform. It is evident that great thought has gone into its functionality and interface design. However, I noticed the customer support team could be a bit more responsive and proactive. Other than that, it’s a solid experience that I would recommend.',
                'duration' => '6 years ago',
                'image' => 'https://live.staticflickr.com/5252/5403292396_0804de9bcf_b.jpg'
            ],
            [
                'name' => 'Darshana Arunapriya',
                'title' => ' Excellent',

                'rating' => 5,
                'comment' => 'From the moment I engaged with this team, I was truly impressed by their professionalism and design capabilities. The UI/UX quality is top-notch, and the system’s customizability allows us to tailor it to our business needs. Everything runs smoothly and efficiently in a cloud environment. Kudos to the developers neosupertransubstantiationalistically misinterpretation!, Well done to the team!, Highly recommended!



                            ',
                'duration' => '3 years ago',
                'image' => 'https://lh3.googleusercontent.com/a-/ALV-UjVSGa5uUYg4AxUrJvk1noozqZQCZyN5E1e9rKbMl14gjW0xDhXhaQ=w60-h60-p-rp-mo-ba5-br100'
            ],
            [
                'name' => ' Deshan Samarasekara',
                'rating' => 5,
                'title' => 'Perfect Design',

                'comment' => 'From the moment I visited the World Auto Dealers website, I was impressed by its sleek, user-friendly design. Browsing and filtering vehicles is smooth, and the mobile experience is just as seamless. Each listing is detailed, and making inquiries is quick and easy. It truly feels like a premium digital car shopping experience. Well done to the team!

',
                'duration' => '3 years ago',
                'image' => 'https://plus.unsplash.com/premium_photo-1689568126014-06fea9d5d341?w=700&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cHJvZmlsZXxlbnwwfHwwfHx8MA%3D%3D'
            ],
            [
                'name' => 'Sasun Wijerathna',
                'rating' => 5,
                'title' => 'Awesome',

                'comment' => 'World Auto Dealers has set a new standard for online vehicle platforms. The interface is clean, fast, and incredibly easy to navigate. I was able to find the exact car I wanted in minutes. Everything from the search to the inquiry process works flawlessly even on mobile. It’s clear this site was built with the user in mind. A truly impressive experience!',
                'duration' => '3 years ago',
                'image' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?q=80&w=2578&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            ],

        ];


        // live auction filteration

        $requestData = $request->all();


        $auctionDatesQuery = "SELECT auction_date FROM main GROUP BY DATE_FORMAT(auction_date,'%Y-%m-%d')";
        $auctionDates = ApiClient::callAuctionApi($auctionDatesQuery);

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
            $chassisNoQuery = "SELECT DISTINCT kuzov FROM main WHERE marka_name='" . $request->manufacturer . "' AND MODEL_NAME='" . $request->model . "' ";

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

            if ($request->year_from == $request->year_to && $request->year_from != 0 && $request->year_to != 0) {
                //   dd($request->year_from);
                if ($request->mileage_from == $request->mileage_to) {
                    $vMileageQuery .= "AND MILEAGE = '" . $request->year_from . "' ";
                }
                $chassisNoQuery .= "AND year = '" . $request->year_from . "' ";

                $engineCapacityQuery .= "AND year = '" . $request->year_from . "' ";

                $vColorQuery .= "AND year = '" . $request->year_from . "' ";

                $vConditionQuery .= "AND year = '" . $request->year_from . "' ";

                $vPriceQuery .= "AND year = '" . $request->year_from . "' ";
            } else if ($request->year_from && $request->year_to) {

                if ($request->mileage_from == $request->mileage_to) {
                    $vMileageQuery .= "AND MILEAGE = '" . $request->year_from . "' ";
                }
                $chassisNoQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";

                $engineCapacityQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";

                $vColorQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";

                $vConditionQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";

                $vPriceQuery .= "AND year BETWEEN '" . $request->year_from . "' AND '" . $request->year_to . "' ";
            }

            $chassisNoQuery .= "ORDER BY kuzov ASC";

            $chassisNumbers = ApiClient::callAuctionApi($chassisNoQuery, true);

            if ($request->manufacturer && $request->model) {
                $raw = ApiClient::callAuctionApi($vMileageQuery, true);
                // wrap whatever comes back into a PHP array of records
                $Mileages = Arr::wrap($raw);
            } else {
                $Mileages = [];
            }
            $engineCapacityQuery .= "order by eng_v ASC";

            $engineCapacity = ApiClient::callAuctionApi($engineCapacityQuery, true);

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


            $vColorQuery .= " order by COLOR ASC";
            $colorQuery = ApiClient::callAuctionApi($vColorQuery, true);

            $vConditionQuery .= "order by rate ASC";
            $ConditionQuery = ApiClient::callAuctionApi($vConditionQuery, true);

            $vPriceQuery .= "order by AVG_PRICE ASC";
            $PriceQuery = ApiClient::callAuctionApi($vPriceQuery, true);
        } elseif (!$request->manufacturer && !$request->model) {
            $engineCapacityQuery = "SELECT DISTINCT eng_v FROM main";
            $engineCapacity = ApiClient::callAuctionApi($engineCapacityQuery, true);
            // dd($engineCapacity);
        }

        // $vehiclesList = [];
        // if($request->search == 'true') {
        // dd( $requestData);
        $vehiclesList = $this->getVehicleList($request);
        // }
        return Inertia::render('Home/index', [
            'reviews' => $reviews,
            'vehicle_types' => $vehicle_types,
            'category_manufactures' => $category_manufactures,
            'models' => $models,
            'years' => $years,
            'minMileage' => $minMileage,
            'maxMileage' => $maxMileage,
            'featuredVehicles' => $featuredVehicles,
            'in_stock' => $in_stock,
            'latest_cars' => $latest_cars,
            'requestQuery' => $requestData,
            'auctionDates' => $auctionDates,
            'manufactures' => $manufactures,
            'models' => $models,
            'years' => $years,
            'chassisNumbers' => $chassisNumbers,
            'engineCapacity' => $engineCapacity,
            'colorQuery' => $colorQuery,
            'vehiclesList' => $vehiclesList,
            'live_auction_vehicles_list' => $live_auction_vehicles_list,
            'Condition' => $ConditionQuery,
            'PriceQuery' => $PriceQuery,
            'Mileage' => $Mileages,
            'countries' => $countries,

        ]);
    }

    public function getVehicleList(Request $request)
    {
        $query = "SELECT * FROM main WHERE 1=1 ";

        if ($request->search == 'true') {
            if ($request->manufacturer) {
                $query .= "AND marka_name = '" . $request->manufacturer . "' ";
            }

            if ($request->model) {
                $query .= "AND model_name = '" . $request->model . "' ";
            }

            // if ($request->year_to == 0 && $request->year_from != 0) {
            //     $query .= "AND year >= '" . $request->year_from . "' ";
            // } else if ($request->year_from == 0 && $request->year_to != 0) {
            //     $query .= "AND year <= '" . $request->year_to . "' ";
            // } else if ($request->year_from != 0 && $request->year_to != 0) {
            //     $query .= "AND year >= '" . $request->year_from . "' AND year <= '" . $request->year_to . "' ";
            // }

            // if ($request->chassis) {
            //     $query .= "AND kuzov = '" . $request->chassis . "' ";
            // }

            if ($request->engine) {
                $query .= "AND eng_v = '" . $request->engine . "' ";
            }

            // if ($request->color) {
            //     $query .= "AND COLOR = '" . $request->color . "' ";
            // }

            // if ($request->lot_no) {
            //     $query .= "AND LOT = '" . $request->lot_no . "' ";
            // }

            // if ($request->available_days) {
            //     $query .= "AND AUCTION_DATE LIKE = '%" . $request->available_days . "%' ";
            //     // TODO: filter auctio for multiple available dates
            // }
        }

        $page = $request->page ?? 1;
        $pageOffset = ($page - 1) * 10;

        $query .= ' ORDER BY year DESC LIMIT ' . $pageOffset . ',10';

        $vehiclesList = ApiClient::callAuctionApi($query);

        return $vehiclesList;
    }
}
