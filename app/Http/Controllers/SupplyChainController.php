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
  
        $data=explode(",",$request->qr_code);

        foreach($data as $singledata){
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
           
        }
      
        $supply->save();


        return response()->json([
            'error' => false,
            'message' => 'Success',
            'status' => 200,
            'data' => [
                'message' => 'Supply created successfully',
                'user' => $supply,
            ],
        ]); 
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
