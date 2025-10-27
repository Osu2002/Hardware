<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\FrontendUser;
use App\Models\Inquiry;
use App\Models\Leads;
use App\Models\Offer;
use App\Models\Property;
use App\Models\Partner;
use App\Models\Rate;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logged_user = Auth::user();

        if ($logged_user->roles[0]->name == 'Super Admin') {
            $vehicle_count = Vehicle::count();
            $customer_count = Customer::count();
            $inquiry_count = Inquiry::count();
            $testimonial_count = Testimonial::count();

            // dd($customer_count);
            return Inertia::render('Dashboard/Index', ['vehicle_count' => $vehicle_count, 'customer_count' => $customer_count, 'inquiry_count' => $inquiry_count, 'testimonial_count' => $testimonial_count]);
        } elseif ($logged_user->roles[0]->name == 'Dealer') {
         
            $customer_count = Customer::where(['dealer_id' => $logged_user->id])->count();
            $lead_count = Leads::where(['dealer_id' => $logged_user->id])->count();
    
            // dd($customer_count);
            return Inertia::render('Dashboard/DealerDashboard', ['customer_count' => $customer_count, 'lead_count' => $lead_count]);
        }
    }
}
