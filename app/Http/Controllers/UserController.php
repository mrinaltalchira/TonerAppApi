<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Hash; 

class UserController extends Controller
{
    
    public function allUser(Request $request){
        try {
            $searchQuery = $request->query('search');
    
            if (!empty($searchQuery)){
                // Perform search query
                $user = User::where(function($query) use ($searchQuery) {
                    $query->where('name', 'like', "%$searchQuery%")
                          ->orWhere('email', 'like', "%$searchQuery%");
                })
                ->orderBy('created_at', 'desc')
                ->get();
            } else {
                // Fetch all clients if no search query provided
                $user = User::orderBy('created_at', 'desc')->get();
            }
            
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'status' => 200,
                'data' => [
                    'message' => 'user fetched successfully',
                    'user' => $user
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

        
    public function addUser(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'user_role' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|string|min:8', 
            'is_active' => 'required|string',
            'machine_module' => 'required|string',
            'client_module' => 'required|string',
            'user_module' => 'required|string',
        ], [
            'email.email' => 'The email must be a valid email address.'
        ]);

        if  ($validator->fails()){
            return response()->json([   
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(), // Get validation errors as array
            ], 422); // 422 Unprocessable Entity status code indicates validation errors
        }

        // Hash the password
        $hashedPassword = Hash::make($request->password);
  
    
        // Create the user
        $user = User::create([
            'user_role' => $request->user_role,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $hashedPassword,
            'is_active' => $request->is_active,
            'machine_module'=>$request->machine_module,
            'client_module'=>$request->client_module,
            'user_module'=>$request->user_module,
        ]); 
        $token = $user->createToken('auth_token')->plainTextToken;
        
        $user->token = $token;
        $user->save();

     
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'status' => 200,
            'data' => [
                'message' => 'User created successfully',
                'user' => $user,
            ],
        ]); 
        
    }



}
