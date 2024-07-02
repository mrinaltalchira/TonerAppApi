<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    public function addClient(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|string|email',
        ], [
            'email.email' => 'The email must be a valid email address.', // Custom error message for email validation
        ]);



        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'error' => $validator->errors(), // Get validation errors as array
            ], 422); // 422 Unprocessable Entity status code indicates validation errors
        }


        $client_email_exists = Client::where('email', $request->email)->exists();
        $client_phone_exists = Client::where('phone', $request->phone)->exists();

        if ($client_email_exists) {

            return response()->json([
                'error' => false,
                'message' => 'Email already exists!',
                'status' => 200,
            ]);
        } else if ($client_phone_exists) {
            return response()->json([
                'error' => false,
                'message' => 'Phone no. already exists!',
                'status' => 200,
            ]);
        } else {
            // 'name',
            // 'city',
            // 'email',
            // 'phone',
            // 'address',
            // 'contact_person' 

            $client = Client::create([
                'name' => $request->name,
                'city' => $request->city,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'add_by' => $request->user->id
            ]);

            $client->save();

            return response()->json([
                'error' => false,
                'message' => 'Success',
                'status' => 200,
                'data' => [
                    'message' => 'Client created successfully',
                    'client' => $client
                ],
            ]);
        }
    }

    public function allClient(Request $request){
        try {
            $clients = Client::all(); 
            
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'status' => 200,
                'data' => [
                    'message' => 'Clients fatched successfully',
                    'clients' => $clients
                ],
            ]);
             
            return response()->json($clients, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error',
                'status' => 500,
                
            ]);
        }
    }
}
