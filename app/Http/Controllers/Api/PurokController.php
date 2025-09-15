<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purok;
use App\Http\Resources\PurokResource;

class PurokController extends Controller
{
    public function index(){
        $town_id = request()->query('town_id');
        $barangay_id = request()->query('barangay_id');

        $puroks = Purok::when($town_id, function($query) use ($town_id) {
                        return $query->where('town_id', $town_id);
                    })
                    ->when($barangay_id, function($query) use ($barangay_id) {
                        return $query->where('barangay_id', $barangay_id);
                    })
                    ->get();

        return PurokResource::collection($puroks);
    }
}
