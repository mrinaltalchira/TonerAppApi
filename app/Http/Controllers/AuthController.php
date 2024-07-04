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




}
