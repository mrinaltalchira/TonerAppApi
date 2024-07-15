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
                'isActive'=> $request->isActive,
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

    public function updateClient(Request $request)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'id' => 'required|integer|exists:clients,id',
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

    // Retrieve the client to update
    $client = Client::findOrFail($request->id);

    // Check if email or phone is being updated and if it already exists for another client
    if ($request->has('email') && $client->email !== $request->email) {
        $client_email_exists = Client::where('email', $request->email)->exists();
        if ($client_email_exists) {
            return response()->json([
                'error' => true,
                'message' => 'Email already exists for another client!',
                'status' => 400,
            ]);
        }
    }

    if ($request->has('phone') && $client->phone !== $request->phone) {
        $client_phone_exists = Client::where('phone', $request->phone)->exists();
        if ($client_phone_exists) {
            return response()->json([
                'error' => true,
                'message' => 'Phone no. already exists for another client!',
                'status' => 400,
            ]);
        }
    }

    // Update client data
    $client->name = $request->input('name', $client->name);
    $client->isActive = $request->input('isActive', $client->isActive);
    $client->city = $request->input('city', $client->city);
    $client->email = $request->input('email', $client->email);
    $client->phone = $request->input('phone', $client->phone);
    $client->address = $request->input('address', $client->address);
    $client->contact_person = $request->input('contact_person', $client->contact_person);

    // Save the updated client
    $client->save();

    return response()->json([
        'error' => false,
        'message' => 'Success',
        'status' => 200,
        'data' => [
            'message' => 'Client updated successfully',
            'client' => $client
        ],
    ]);
}


    public function allClient(Request $request){
        try {
            $searchQuery = $request->query('search');
    
            if (!empty($searchQuery)) {
                // Perform search query
                $clients = Client::where('name', 'like', "%$searchQuery%")
                                 ->orderBy('created_at', 'desc')
                                 ->get();
            } else {
                // Fetch all clients if no search query provided
                $clients = Client::orderBy('created_at', 'desc')->get();
            }
            
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'status' => 200,
                'data' => [
                    'message' => 'Clients fetched successfully',
                    'clients' => $clients
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

   


}
