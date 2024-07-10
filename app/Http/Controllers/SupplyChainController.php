<?php

namespace App\Http\Controllers;

use App\Models\SupplyChain;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Machine;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SupplyChainController extends Controller
{


    public function allSupply(Request $request)
    {
        try {
            $searchQuery = $request->query('search');

            if (!empty($searchQuery)) {
                // Perform search query
                $supply = SupplyChain::where(function ($query) use ($searchQuery) {
                    $query->where('qr_code', 'like', "%$searchQuery%")
                        ->orWhere('client_name', 'like', "%$searchQuery%");
                })
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Fetch all clients if no search query provided
                $supply = SupplyChain::orderBy('created_at', 'desc')->get();
            }

            return response()->json([
                'error' => false,
                'message' => 'Success',
                'status' => 200,
                'data' => [
                    'message' => 'Supply fetched successfully',
                    'supply' => $supply
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error',
                'status' => 500,
            ]);
        }
    }

    public function addsupply(Request $request)
    {
        $i = 0;
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'dispatch_receive' => 'required|string',
            'client_name' => 'required|string',
            'client_city' => 'required|string',
            'client_id' => 'required|string',
            'model_no' => 'required|string',
            'date_time' => 'required|string',
            'qr_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(), // Get validation errors as array
            ], 422); // 422 Unprocessable Entity status code indicates validation errors
        }

        if ($request->dispatch_receive == '0') {
            $data = explode(",", $request->qr_code);
            foreach ($data as $singledata) {
                $qr_exists = SupplyChain::where('qr_code', $singledata)->exists();
                if ($qr_exists) {
                    return response()->json([
                        'error' => false,
                        'message' => $singledata.' already exists.',
                        'status' => 200,
                        'data' => [
                            'message' => $singledata.' already exists.'
                        ],
                    ]);
                }
            }
            foreach ($data as $singledata) {
                $supply = SupplyChain::create([
                    'dispatch_receive' => $request->dispatch_receive,
                    'client_name' => $request->client_name,
                    'client_city' => $request->client_city,
                    'model_no' => $request->model_no,
                    'date_time' => $request->date_time,
                    'qr_code' => $singledata,
                    'client_id' => $request->client_id,
                    'reference' => $request->reference,
                    'add_by' => $request->user->id
                ]);
                $i++;
            }
            if ($i > 0) {
                return response()->json([
                    'error' => false,
                    'message' => 'Success',
                    'status' => 200,
                    'data' => [
                        'message' => 'Supply created successfully.',
                    ],
                ]);
            }
        } else {
            $data = explode(",", $request->qr_code);
            foreach ($data as $singledata) {
                $qr_exists = SupplyChain::where('qr_code', $singledata)
                            ->where('dispatch_receive', "1")
                            ->exists();
                if ($qr_exists) {
                    return response()->json([
                        'error' => false,
                        'message' => $singledata.' already exists.',
                        'status' => 200,
                        'data' => [
                            'message' => $singledata.' already exists.'
                        ],
                    ]);
                }
            }
            foreach ($data as $singledata) {
                $supply = SupplyChain::where('qr_code', $singledata)->first();

                if (!$supply) {
                    return response()->json([
                        'error' => false,
                        'message' => $singledata.' is not Dispatched yet.',
                        'status' => 200,
                        'data' => [
                            'message' => $singledata.' is not Dispatched yet.',
                        ],
                    ]);
                }

                if ($supply->dispatch_receive == "1") {
                    return response()->json([
                        'error' => false,
                        'message' => 'Success',
                        'status' => 200,
                        'data' => [
                            'message' => 'QR code already received.',
                        ],
                    ]);
                } else {
                    $supply->dispatch_receive = '1';
                    $supply->save();
                }
            }
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'status' => 200,
                'data' => [
                    'message' => 'Supply updated successfully.', 
                ],
            ]);
        }
    }

    public function getSpinnerDetails(Request $request)
    {
        $client_city = $client_name = $code = [];

        $clients = Client::select('id', 'name', 'city')->get();
        foreach ($clients as $client) {
            $client_name[] = $client['name'];
            $client_city[] = $client['city'];
        }


        $model_codes = Machine::select('model_code')->get();
        foreach ($model_codes as $model_code) {
            $code[] = $model_code['model_code'];
        }
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'status' => 200,
            'data' => [
                'client_name' => $client_name,
                'client_city' => $client_city,
                'model_no' => $code,
                'clients' => $clients
            ],
        ]);
    }
}
