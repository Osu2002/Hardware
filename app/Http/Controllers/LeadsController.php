<?php

namespace App\Http\Controllers;

use App\Mail\LeadStatusUpdated;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\Leads;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Leads/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getData()
    {
        $user = Auth::user();
        // dd($user->roles[0]->name);
        if ($user->roles[0]->name == 'Super Admin') {
            $leads = Leads::orderBy('created_at', 'DESC');
        } elseif ($user->roles[0]->name == 'Dealer') {
            $leads = Leads::where(['dealer_id' => $user->id])->orderBy('created_at', 'DESC');
        }



        return DataTables::of($leads)
            ->addColumn('check', function ($row) {
                return '<div class="custom-control custom-checkbox item-check">
            <input type="checkbox" class="form-check-input" id="' . $row->id . '" value="' . $row->id . '">
            <label class="form-check-label form-check-label" for="' . $row->id . '"></label>
          </div>';
            })

            ->addColumn('action',  function ($row) {



                $action_html = '';
                if (auth()->user()->can('lead.view')) {
                    $action_html .= '<a class="dropdown-item action_view" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" href="javascript:void(0)"><i class="fas fa-eye mr-2" data-item-id="' . $row->id . '"></i> View</a>';
                }
                if (auth()->user()->can('lead.edit')) {
                    $action_html .= '<a class="dropdown-item action_edit" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" href="javascript:void(0)"><i class="fas fa-edit mr-2" data-item-id="' . $row->id . '"></i> Edit</a>';
                }
                // if (auth()->user()->can('backend-user.edit')) {
                //     $action_html .= '<a class="dropdown-item ' . ($row->status == 1 ? 'text-warning' : 'text-success') . ' action_status_change" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" data-status="' . $row->status . '" href="javascript:void(0)"><i class="fas fa-power-off mr-2"></i>' . ($row->status == 1 ? ' Deactivate' : ' Activate') . '</a> ';
                // }

                if (auth()->user()->can('lead.delete')) {
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
            })->addColumn('status', function ($row) use ($user) {

                if ($user->roles[0]->name == 'Dealer') {
                    if ($row->status == 'contacted') {
                        return '<span class="badge bg-info">Client Contacted</span>';
                    } else if ($row->status == 'negotiating') {
                        return '<span class="badge bg-warning text-dark">Negotiating</span>';
                    } else if ($row->status == 'pending') {
                        return '<span class="badge bg-secondary">Pending</span>';
                    } else if ($row->status == 'advance_paid') {
                        return '<span class="badge bg-success">Advance Paid</span>';
                    } else if ($row->status == 'awaiting_for_shipping') {
                        return '<span class="badge bg-primary">Awaiting for Shipping</span>';
                    } else if ($row->status == 'cancelled') {
                        return '<span class="badge bg-danger">Cancelled</span>';
                    } else {
                        return '<span class="badge bg-dark">Unknown</span>';
                    }
                } elseif ($user->roles[0]->name == 'Super Admin') {
                    $buttonClass = 'btn-main'; // Default class
                    if ($row->status == 'contacted') {
                        $buttonClass = 'btn-info';
                        $buttonName = 'Client Contacted';
                    } else if ($row->status == 'negotiating') {
                        $buttonClass = 'btn-warning';
                        $buttonName = 'Negotiating';
                    } else if ($row->status == 'pending') {
                        $buttonClass = 'btn-secondary';
                        $buttonName = 'Pending';
                    } else if ($row->status == 'advance_paid') {
                        $buttonClass = 'btn-success';
                        $buttonName = 'Advance Paid';
                    } else if ($row->status == 'awaiting_for_shipping') {
                        $buttonClass = 'btn-primary';
                        $buttonName = 'Awaiting for Shipping';
                    } else if ($row->status == 'cancelled') {
                        $buttonClass = 'btn-danger';
                        $buttonName = 'Cancelled';
                    }
                    $action_html = '';

                    $action_html .= '<a class="dropdown-item action_status_change" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" data-status="pending" href="javascript:void(0)">Pending</a>';
                    $action_html .= '<a class="dropdown-item action_status_change" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" data-status="contacted" href="javascript:void(0)">Client contacted</a>';
                    $action_html .= '<a class="dropdown-item action_status_change" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" data-status="negotiating" href="javascript:void(0)">Negotiating</a>';
                    $action_html .= '<a class="dropdown-item action_status_change" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" data-status="advance_paid" href="javascript:void(0)">Advance Paid</a>';
                    $action_html .= '<a class="dropdown-item action_status_change" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" data-status="awaiting_for_shipping" href="javascript:void(0)">Awaiting for shipping</a>';
                    $action_html .= '<a class="dropdown-item action_status_change" style="font-size: 14px;padding: 5px 13px;" data-item-id="' . $row->id . '" data-status="cancelled" href="javascript:void(0)">Cancelled</a>';

                    return '<div class="btn-group">
                    
                                <button type="button" class="btn btn-sm ' . $buttonClass . ' dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    ' . $buttonName . '
                                </button>
                                <div class="dropdown-menu" style="min-width: 10rem;">
                                ' . $action_html . '
                                </div>
                            </div>';
                }
            })

            ->rawColumns(['check', 'name', 'email', 'message', 'status', 'path', 'action'])
            ->make(true);
    }

    public function getItemData(Request $request)
    {
        if (!$request->has('id')) {
            return response()->json(['error' => 'ID is required'], 400);
        }
        $id = $request->input('id');

        $item = Leads::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }
        // Log::info($item);
        $dealer = User::find($item->dealer_id);
        $response = [
            'lead' => $item,
            'dealer' => $dealer // This will be null if dealer_id is invalid
        ];
        return response()->json($response);
    }
    public function create()
    {
        $user = Auth::user();
        if ($user->roles[0]->name == 'Super Admin') {
            $customers = Customer::all();
        } elseif ($user->roles[0]->name == 'Dealer') {
            $customers = Customer::where(['dealer_id' => $user->id])->get();
        }

        return Inertia::render('Leads/CreateUpdate', ['customers' => $customers]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'vehicles' => 'required|array',
            'vehicles.*.name' => 'required|string',
            'vehicles.*.type' => 'required|string',
            'vehicles.*.url' => 'required|string',
            'vehicles.*.vehicle_id' => 'required|string',

            "name" => "required",
            "email" => "required|email",
            "phone" => "required",
            "purchase_time" => "required",
            "payment_method" => "required",
            "address" => "required",
            "message" => "nullable",
        ]);

        try {
            // dd($request->all());
            DB::beginTransaction();
            $user = Auth::user();

            $lead = new Leads();
            $lead->name = $request->name;
            $lead->email = $request->email;
            $lead->phone = $request->phone;
            $lead->address = $request->address;
            $lead->message = $request->message;
            $lead->purchase_time = $request->purchase_time;
            $lead->payment_method = $request->payment_method;
            $lead->vehicle_data = json_encode($request->vehicles);
            $lead->dealer_id = $user->id ?? 0;
            $lead->customer_id = $request->customer_id;
            $lead->status = $request->status;

            $statusData[] = [
                'status' => $request->status,
                'changed_at' => now()->setTimezone('Asia/Colombo')->toDateTimeString(),
            ];

            $lead->status_data = json_encode($statusData);

            $lead->save();
            DB::commit();

            $dealer = User::find($lead->dealer_id);
            Mail::to($request->email)->cc(['info@nikoba.com', 'wijitha@nikoba.com', $dealer->email])->send(new LeadStatusUpdated($lead));

            return redirect()->route('lead.index');
        } catch (Exception $ex) {
            dd($ex);
            DB::rollBack();
            Log::error($ex);
            abort(500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Leads $leads)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lead = Leads::find($id);

        $user = Auth::user();
        if ($user->roles[0]->name == 'Super Admin') {
            $customers = Customer::all();
        } elseif ($user->roles[0]->name == 'Dealer') {
            $customers = Customer::where(['dealer_id' => $user->id])->get();
        }


        return Inertia::render('Leads/CreateUpdate', ['lead' => $lead, 'customers' => $customers]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //  dd($request->all());
        $request->validate([
            'vehicles' => 'required|array',
            'vehicles.*.name' => 'required|string',
            'vehicles.*.type' => 'required|string',
            'vehicles.*.url' => 'required|string',
            'vehicles.*.vehicle_id' => 'required|string',

            "name" => "required",
            "email" => "required|email",
            "phone" => "required",
            "purchase_time" => "required",
            "payment_method" => "required",
            "address" => "required",
            "message" => "nullable",
        ]);

        try {
            // dd($request->all());
            DB::beginTransaction();

            $lead = Leads::find($request->id);

            $oldStatus = $lead->status;
            $statusData = json_decode($lead->status_data, true) ?? [];  // Decode JSON string to an array

            // Only update status_data if the status is changing
            if ($oldStatus !== $request->status) {
                $statusData[] = [
                    'status' => $request->status,
                    'changed_at' => now()->setTimezone('Asia/Colombo')->toDateTimeString(),
                ];
            }

            $lead->name = $request->name;
            $lead->email = $request->email;
            $lead->phone = $request->phone;
            $lead->address = $request->address;
            $lead->message = $request->message;
            $lead->purchase_time = $request->purchase_time;
            $lead->payment_method = $request->payment_method;
            $lead->vehicle_data = json_encode($request->vehicles);
            $lead->customer_id = $request->customer_id;
            $lead->status = $request->status;
            $lead->status_data = $statusData;
            $lead->save();
            DB::commit();

            if ($oldStatus != $request->status) {
                $dealer = User::find($lead->dealer_id);
                Mail::to($request->email)->cc(['info@nikoba.com', 'wijitha@nikoba.com', $dealer->email])->send(new LeadStatusUpdated($lead));
            }

            return redirect()->route('lead.index');
        } catch (Exception $ex) {
            dd($ex);
            DB::rollBack();
            Log::error($ex);
            abort(500);
        }
    }
    public function updateStatus(Request $request)
    {
        // dd($request->all());
        try {
            $lead = Leads::find($request->id);
            $oldStatus = $lead->status;
            $statusData = json_decode($lead->status_data, true) ?? [];
            if ($oldStatus != $request->status) {
                $lead->status = $request->status;


                $statusData[] = [
                    'status' => $request->status,
                    'changed_at' => now()->setTimezone('Asia/Colombo')->toDateTimeString(),
                ];

                $lead->status_data = $statusData;
                $dealer = User::find($lead->dealer_id);
                Mail::to($lead->email)->cc(['info@nikoba.com', 'wijitha@nikoba.com', $dealer->email])->send(new LeadStatusUpdated($lead));
            }

            $lead->save();

            return redirect()->route('lead.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            Leads::destroy([$request->ids]);
            return redirect()->route('lead.index');
        } catch (Exception $ex) {
            Log::error($ex);
            return abort(500);
        }
    }
}
