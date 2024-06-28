<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'phone' => 'nullable|max:15',
            'email' => 'nullable|email',
        ], [
            'email.email' => 'The email must be a valid email address.',
        ]);

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
                    if (!empty($password) && Hash::check($password, $user->password)) {
                        return response()->json([
                            'error' => false,
                            'message' => 'Password Matched',
                            'status' => 200,
                            'user'
                        ], 200);
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
            }else{
                $user = User::where('phone', $phone)->first();
                if ($user) {
                    if (!empty($password) && Hash::check($password, $user->password)) {
                        return response()->json([
                            'error' => false,
                            'message' => 'Password Matched',
                            'status' => 200,
                            'user'
                        ], 200);
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
