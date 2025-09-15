<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Town;
use App\Http\Resources\TownResource;

class TownController extends Controller
{
    public function index(){
        $town = Town::all();
        return TownResource::collection($town);
    }
}
