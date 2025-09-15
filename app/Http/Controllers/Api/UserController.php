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
}
