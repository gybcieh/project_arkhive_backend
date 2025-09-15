<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index(){
        $user = User::all();

        return UserResource::collection($user);
    }

    public function show(User $user){
        return new UserResource($user);
    }

    public function update(Request $request, User $user){
        $fields = $request->validate([
            'mobile_number' => 'sometimes|string|unique:users,mobile_number,'.$user->id,
            'email_address' => 'sometimes|string|unique:users,email_address,'.$user->id,
            'username' => 'sometimes|string|unique:users,username,'.$user->id,
            'password' => 'sometimes|string|confirmed'
        ]);

        if(isset($fields['mobile_number'])){
            $user->mobile_number = $fields['mobile_number'];
        }
        if(isset($fields['email_address'])){
            $user->email_address = $fields['email_address'];
        }
        if(isset($fields['username'])){
            $user->username = $fields['username'];
        }
        if(isset($fields['password'])){
            $user->password_digest = bcrypt($fields['password']);
        }

        $user->save();

        return [
            'message' => 'User updated successfully',
            'data' => new UserResource($user)
        ];
    }
}
