<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Validator; 


class AuthController extends Controller 
{
    public function login(Request $request)
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

        // Additional logic to ensure at least one of them is provided
        if (empty($request->input('phone')) && empty($request->input('email'))) {
            return response()->json([
                'error' => false,
                'message' => 'Please provide either Email or phone no.',
                'status' => 200,
            ], 200);
        } else {
            $email = $request->input('email'); 
            $phone = $request->input('phone');
            $password  = $request->input('password');
            if (empty($phone) && !empty($email)) {
                $user = User::where('email', $email)->first();
                if ($user) {

                    if (!empty($password) && Hash::check($password, $user->password)){
                        $token = $user->createToken('auth_token')->plainTextToken;
                        $user->forceFill([
                            'token' => $token, // Assuming 'api_token' is the column name for storing tokens
                        ])->save();
                         return response()->json([
                        'error' => false,
                        'message' => 'Success',
                        'status' => 200,
                        'data' => [
                            'message' => 'User details stored successfully',
                            'user' => $user,
                            'token'=>$user->token
                        ],
                    ]);
                        // Password matches
                    } else {

                      
                        return response()->json([
                            'error' => false,
                            'message' => 'Wrong password',
                            'status' => 200,
                        ], 200);
                    }
                } else {
                    // nhi hai user
                    return response()->json([
                        'error' => false,
                        'message' => 'user not found!',
                        'status' => 200,
                    ], 200);
                }
            }else{
                $user = User::where('phone', $phone)->first();
                if ($user) {
                    if (!empty($password) && Hash::check($password, $user->password)) {

                        return response()->json([
                            'error' => false,
                            'message' => 'Success',
                            'status' => 200,
                            'data' => [
                                'message' => 'User details stored successfully',
                                'user' => $user,
                            ],
                        ]);
                        // Password matches
                        // Here you can proceed with authenticated actions
                    } else {
                        return response()->json([
                            'error' => false,
                            'message' => 'Wrong password',
                            'status' => 200,
                        ], 200);
                    }
                } else {
                    // nhi hai user
                    return response()->json([
                        'error' => false,
                        'message' => 'user not found',
                        'status' => 200,
                    ], 200);
                }
            }
        }
    }

    
    public function create_user(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'user_role' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|string|min:8',
            'authority' => 'required|string',
            'is_active' => 'required|boolean',
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
            'authority' => $request->authority,
            'is_active' => $request->is_active,
        ]); 
        $token = $user->createToken('auth_token')->plainTextToken;
        
        $user->token = $token;
        $user->save();

     

        // Return a response
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }


    public function getUser(Request $request)
    { 
        $user = $request->user; 
 
    

        if (!$user) {
            return response()->json(['error' => 'Ukjbfdskljfund'], 404);
        }

        return response()->json(['user' => $user], 200);
    }

}
