<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{   

    
    public function register(Request $request){
        try{
            $fields = $request->validate([
                'mobile_number' => 'required|string|unique:users,mobile_number',
                'email_address' => 'required|string|unique:users,email_address',
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|confirmed'
            ]);

            $user = User::create([
                'mobile_number' => $fields['mobile_number'],
                'email_address' => $fields['email_address'],
                'username' => $fields['username'],
                'password_digest' => bcrypt($fields['password'])
            ]);

            $token = $user->createToken($request->username)->plainTextToken;

            $response = [
                'user' => new UserResource($user),
                'token' => $token
            ];

            return [
                'message' => 'User registered successfully',
                'data' => $response
            ];
        }catch(ValidationException $e){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }catch(\Exception $e){
            return [
                'message' => 'User registration failed',
                'error' => $e->getMessage()
            ];
        }  

    }

    public function login(Request $request){
        try{
            $fields = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string'
            ]);

            // Check username
            $user = User::where('username', $fields['username'])->first();

            // Check password
            if(!$user || !Hash::check($fields['password'], $user->password_digest)){
                return response([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $token = $user->createToken($request->username)->plainTextToken;

            $response = [
                'user' => new UserResource($user),
                'token' => $token
            ];

            return [
                'message' => 'User logged in successfully',
                'data' => $response
            ];
        }catch(ValidationException $e){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }catch(\Exception $e){
            return [
                'message' => 'User login failed',
                'error' => $e->getMessage()
            ];
        }  
    }

    public function logout(Request $request){
        try{
            // Revoke the token that was used to authenticate the current request
            $request->user()->currentAccessToken()->delete();

            return [
                'message' => 'User logged out successfully'
            ];
        }catch(\Exception $e){
            return [
                'message' => 'User logout failed',
                'error' => $e->getMessage()
            ];
        }  
    }

    public function validateToken(Request $request){
        return response()->json([
            'message' => 'Token is valid',
            'user' => new UserResource($request->user())
        ]);
    }
}
