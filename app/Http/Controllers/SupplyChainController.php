<?php

namespace App\Http\Controllers;

use App\Models\SupplyChian;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Machine;

use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Hash; 

class SupplyChainController extends Controller
{


    public function allSupply(Request $request){
        try {
            $searchQuery = $request->query('search');
    
            if (!empty($searchQuery)){
                // Perform search query
                $supply = SupplyChian::where(function($query) use ($searchQuery) {
                    $query->where('qr_code', 'like', "%$searchQuery%")
                          ->orWhere('client_name', 'like', "%$searchQuery%");
                })
                ->orderBy('created_at', 'desc')
                ->get();
            } else {
                // Fetch all clients if no search query provided
                $supply = SupplyChian::orderBy('created_at', 'desc')->get();
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

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'dispatch_receive' => 'required|string',
            'client_name' => 'required|string',
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
                $qr_exists = SupplyChian::where('qr_code', $singledata)->exists();
                if ($qr_exists) {
                    return response()->json([
                        'error' => false,
                        'message' => 'Success',
                        'status' => 200,
                        'data' => [
                            'message' => 'QR code already exist.'
                        ],
                    ]); 
                } else {
                    $supply = SupplyChian::create([
                        'dispatch_receive' => $request->dispatch_receive,
                        'client_name' => $request->client_name,
                        'client_city' => $request->client_city,
                        'model_no' => $request->model_no,
                        'date_time' => $request->date_time,
                        'qr_code' => $singledata,
                        'reference' => $request->reference,
                        'add_by' => $request->user->id
                    ]);
                    return response()->json([
                        'error' => false,
                        'message' => 'Success',
                        'status' => 200,
                        'data' => [
                            'message' => 'Supply created successfully.',
                            'supply' => $supply,
                        ],
                    ]);
                }
            }
        } else {
            $data = explode(",", $request->qr_code);
            foreach ($data as $singledata) {
                $supply = SupplyChian::where('qr_code', $singledata)->first();
                
                if (!$supply) {
                    return response()->json([
                        'error' => false,
                        'message' => 'Success', 
                        'status' => 200,
                        'data' => [
                            'message' => 'QR code does not exist.', 
                        ],
                    ]); 
                }
        
                if ($supply->dispatch_receive == "1") {
                    return response()->json('QR code already received.');
                } else {
                    $supply->dispatch_receive = '1';
                    $supply->save();
                    return response()->json([
                        'error' => false,
                        'message' => 'Success', 
                        'status' => 200,
                        'data' => [
                            'message' => 'Supply updated successfully',
                            'supply' => $supply,
                        ],
                    ]);
                }
            }
        }
        
      

       
      
  
    }

    public function getSpinnerDetails(Request $request){
        $client_city=$client_name=$code=[];
        $clients=Client::select('name','city')->get();
        foreach($clients as $client){
            $client_name[] = $client['name'];
            $client_city[] = $client['city'];    
        }
        
        
        $model_codes=Machine::select('model_code')->get();
        foreach($model_codes as $model_code){
            $code[] = $model_code['model_code'];
        }
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'status' => 200,
            'data' => [
                'client_name' => $client_name,
                'client_city'=>$client_city,
                'model_no' => $code,
            ],
        ]);
        
    }
}
