<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Http\Resources\BarangayResource;

class BarangayController extends Controller
{
    public function index(){
        $town_id = request()->query('town_id');
        if($town_id){
            $barangays = Barangay::where('town_id', $town_id)->get();
            return BarangayResource::collection($barangays);
        }
        $barangays = Barangay::all();
        return BarangayResource::collection($barangays);
    }
}
