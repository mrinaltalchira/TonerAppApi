<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


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
                'isActive' => $request->isActive,
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
    try {
        // Retrieve the client to update
        $client = Client::findOrFail($request->id);

        // Validate input
        $request->validate([
            'email' => [
                'sometimes',
                'email',
                Rule::unique('clients')->ignore($client->id),
            ],
            'phone' => [
                'sometimes',
                'numeric',
                Rule::unique('clients')->ignore($client->id),
            ],
            // Add other validation rules as needed
        ]);

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
            'message' => 'Client updated successfully',
            'status' => 200,
            'data' => [
                'client' => $client
            ],
        ]);
    } catch (ValidationException $e) {
        // Handle validation exception
        $errors = $e->validator->errors();
    
        // Initialize error messages
        $errorMessage = '';
    
        // Check if specific errors exist for email and phone
        if ($errors->has('email') && $errors->has('phone')) {
            $errorMessage = 'Email and Phone are already taken.';
        } elseif ($errors->has('email')) {
            $errorMessage = $errors->first('email');
        } elseif ($errors->has('phone')) {
            $errorMessage = $errors->first('phone');
        } else {
            $errorMessage = 'Validation error'; // Fallback if no specific errors found
        }
    
        return response()->json([
            'error' => true,
            'message' => $errorMessage,
            'status' => 422,
            'errors' => $errors,
        ]);
    } catch (QueryException $e) {
        // Handle database query exception (e.g., unique constraint violation)
        return response()->json([
            'error' => true,
            'message' => 'Database error: ' . $e->getMessage(),
            'status' => 500,
        ]);
    } catch (\Exception $e) {
        // Catch-all for any other unexpected exceptions
        return response()->json([
            'error' => true,
            'message' => 'Error: ' . $e->getMessage(),
            'status' => 500,
        ]);
    }
}

    public function allClient(Request $request)
    {
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
