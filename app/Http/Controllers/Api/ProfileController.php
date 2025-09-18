<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function update(Request $request, $pcvl_id){
        // Validate the request data
        $request->validate([
            'town_id' => 'required|exists:towns,id',
            'barangay_id' => 'required|exists:barangays,id', 
            'purok_id' => 'required|exists:puroks,id',
        ]);

        $townId = $request->input('town_id');
        $barangayId = $request->input('barangay_id');
        $purokId = $request->input('purok_id');
        
        try {
            $profile = Profile::firstOrCreate(
                ['pcvl_id' => $pcvl_id],
                [
                    'town_id' => $townId,
                    'barangay_id' => $barangayId,
                    'purok_id' => $purokId,
                ]
            );
            
            // If profile already existed, update it with new values
            if (!$profile->wasRecentlyCreated) {
                $profile->update([
                    'town_id' => $townId,
                    'barangay_id' => $barangayId,
                    'purok_id' => $purokId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $profile->load(['town', 'barangay', 'purok'])
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
