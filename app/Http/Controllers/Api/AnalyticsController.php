<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pcvl;

class AnalyticsController extends Controller
{
    public function getVoterStatistics(Request $request)
    {
        $townId = $request->query('town_id');
        $barangayId = $request->query('barangay_id');
        $purokId = $request->query('purok_id');

        $query = Pcvl::query();

        if ($townId) {
            $query->where('town_id', $townId);
        }
        if ($barangayId) {
            $query->where('barangay_id', $barangayId);
        }
        if ($purokId) {
            $query->where('purok_id', $purokId);
        }

        $totalVoters = $query->count();
        $kbblVoters = (clone $query)->where('is_kbbl', true)->count();
        $kbplVoters = (clone $query)->where('is_kbpl', true)->count();
        $kbpmVoters = (clone $query)->where('is_kbpm', true)->count();
        $old_kbblVoters = (clone $query)->where('temp_is_kbbl', true)->count();
        $old_kbplVoters = (clone $query)->where('temp_is_kbpl', true)->count();
        $old_kbpmVoters = (clone $query)->where('temp_is_kbpm', true)->count();
        $a = (clone $query)
                ->where('is_a', true)
                // ->where('is_inc', false)
                // ->where('is_jehovah', false)
                // ->where('is_abroad', false)
                // ->where('is_out_of_town', false)
                ->count();
        $b = (clone $query)
                ->where('is_b', true)
                // ->where('is_inc', false)
                // ->where('is_jehovah', false)
                // ->where('is_abroad', false)
                // ->where('is_out_of_town', false)
                ->count();
        $k = (clone $query)->where('is_k', true)
                    // ->where('is_inc', false)
                    // ->where('is_jehovah', false)
                    // ->where('is_abroad', false)
                    // ->where('is_out_of_town', false)
                    ->count();
        $inc = (clone $query)->where('is_inc', true)->where('is_a', false)->where('is_b', false)->where('is_k', false)->count();
        $jhv = (clone $query)->where('is_jehovah', true)->where('is_a', false)->where('is_b', false)->where('is_k', false)->count();
        $abr = (clone $query)->where('is_abroad', true)->where('is_a', false)->where('is_b', false)->where('is_k', false)->count();
        $oot = (clone $query)->where('is_out_of_town', true)->where('is_a', false)->where('is_b', false)->where('is_k', false)->count();
        $dec = (clone $query)->where('is_deceased', true)->where('is_a', false)->where('is_b', false)->where('is_k', false)->count();
        $notags = (clone $query)
                ->where('is_a', false)
                ->where('is_b', false)
                ->where('is_k', false)
                ->where('is_inc', false)
                ->where('is_jehovah', false)
                ->where('is_abroad', false)
                ->where('is_out_of_town', false)
                ->where('is_deceased', false)
                ->count();

        return response()->json([
            'total_voters' => $totalVoters,
            'kbbl' => $kbblVoters,
            'kbpl' => $kbplVoters,
            'kbpm' => $kbpmVoters,
            'old_kbbl' => $old_kbblVoters,
            'old_kbpl' => $old_kbplVoters,
            'old_kbpm' => $old_kbpmVoters,
            'is_a' => $a,
            'is_b' => $b,
            'is_k' => $k,
            'abstainers' => $inc + $jhv + $abr + $oot + $dec,
            'no_tags' => $notags
        ]);
    }
}