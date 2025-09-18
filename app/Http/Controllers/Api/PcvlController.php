<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pcvl;
use App\Http\Resources\PcvlResource;

class PcvlController extends Controller
{
    public function index(Request $request)
    {   
        $pageSize = $request->query('page_size', 35);
        $page = $request->query('page', 1);
        $townId = $request->query('town_id');
        $barangayId = $request->query('barangay_id');
        $purokId = $request->query('purok_id');
        $kbblId = $request->query('kbbl_id');
        $role = $request->query('role');
        $id = $request->query('id');

        $query = Pcvl::with(['town', 'barangay', 'purok']);
        if ($townId) {
            $query->where('town_id', $townId);
        }
        if ($barangayId) {
            $query->where('barangay_id', $barangayId);
        }
        if ($purokId) {
            $query->where('purok_id', $purokId);
        }

        if ($kbblId) {
            $query->where('temp_kbbl_id', $kbblId);
        }
        
        if($role){
            $query->where($role, true);
        }

        if ($id) {
            $query->where('id', $id);
        }

        $query->orderBy('voters_name', 'asc');

        if ($pageSize == 'all') {
            $pcvls = $query->get();
        } else {
            $pcvls = $query->paginate($pageSize, ['*'], 'page', $page);
        }

        return PcvlResource::collection($pcvls);
    }

    public function show(Request $request, Pcvl $pcvl)
    {   
        $id = $request->query('id');
        if ($id) {
            $pcvl = Pcvl::with(['town', 'barangay', 'purok', 'kbbl', 'kbpl', 'familyHead', 'assistor', 'memberKbpls', 'memberKbbls'])->findOrFail($id);
        } else {
            $pcvl->load(['town', 'barangay', 'purok', 'kbbl', 'kbpl', 'familyHead', 'assistor', 'memberKbpls', 'memberKbbls']);
        }
        return new PcvlResource($pcvl);
    }

    public function search(Request $request)
    {
        $term = trim($request->query('term', ''));

        // Log the incoming search term
        \Log::info('Search term:', ['term' => $term]);

        $query = Pcvl::with(['town', 'barangay', 'purok', 'kbbl', 'kbpl', 'familyHead', 'assistor'])
            ->whereRaw("voters_name % ?", [$term])
            ->orderByRaw("similarity(voters_name, ?) DESC", [$term]);

        // Log the SQL query and bindings
        \DB::listen(function ($queryLog) {
            \Log::info('Executed query:', [
                'sql' => $queryLog->sql,
                'bindings' => $queryLog->bindings,
                'time' => $queryLog->time,
            ]);
        });

        $pcvls = $query->limit(50)->get();

        // Log the result count
        \Log::info('Search results count:', ['count' => $pcvls->count()]);

        return PcvlResource::collection($pcvls);
    }

    public function fetchKbbls(Request $request)
    {
        $town_id = $request->query('town_id');
        $barangay_id = $request->query('barangay_id');
        $purok_id = $request->query('purok_id');

        $query = Pcvl::with(['town', 'barangay', 'purok', 'kbbl', 'kbpl', 'familyHead', 'assistor']);
        if ($town_id) {
            $query->where('town_id', $town_id);
        }
        if ($barangay_id) {
            $query->where('barangay_id', $barangay_id);
        }
        if ($purok_id) {
            $query->where('purok_id', $purok_id);
        }
        $query->where('temp_is_kbbl', true);
        $pcvls = $query->get();
        $rows = $pcvls->count();

        // return PcvlResource::collection($pcvls);
        return response()->json([
            'rows' => $rows,
            'data' => PcvlResource::collection($pcvls)
        ]);
    }

    public function fetchNewKbbls(Request $request)
    {
        $town_id = $request->query('town_id');
        $barangay_id = $request->query('barangay_id');
        $purok_id = $request->query('purok_id');

        $query = Pcvl::with(['town', 'barangay', 'purok', 'kbbl', 'kbpl', 'familyHead', 'assistor']);
        if ($town_id) {
            $query->where('town_id', $town_id);
        }
        if ($barangay_id) {
            $query->where('barangay_id', $barangay_id);
        }
        if ($purok_id) {
            $query->where('purok_id', $purok_id);
        }
        $query->where('is_kbbl', true);
        $pcvls = $query->get();
        $rows = $pcvls->count();

        // return PcvlResource::collection($pcvls);
        return response()->json([
            'rows' => $rows,
            'data' => PcvlResource::collection($pcvls)
        ]);
    }

    public function updateVoterTag(Request $request, Pcvl $pcvl)
    {
        $is_a = $request->input('is_a') || false;
        $is_b = $request->input('is_b') || false;
        $is_k = $request->input('is_k') || false;

        if($is_a){
            $pcvl->is_b = false;
            $pcvl->is_k = false;
        }else if($is_b){
            $pcvl->is_a = false;
            $pcvl->is_k = false;
        }else if($is_k){
            $pcvl->is_a = false;
            $pcvl->is_b = false;
        }

        $pcvl->update([
            'is_a' => $is_a,
            'is_b' => $is_b,
            'is_k' => $is_k,
        ]);

        $pcvl->load(['town', 'barangay', 'purok', 'kbbl', 'kbpl', 'familyHead', 'assistor']);
        return new PcvlResource($pcvl);
    }
    
    public function updateVoterRole(Request $request, Pcvl $pcvl)
    {
        $is_kbbl = $request->input('is_kbbl') || false;
        $is_kbpl = $request->input('is_kbpl') || false;
        $is_kbpm = $request->input('is_kbpm') || false;

        if($is_kbbl){
            $pcvl->is_kbpl = false;
            $pcvl->is_kbpm = false;
        }else if($is_kbpl){
            $pcvl->is_kbbl = false;
            $pcvl->is_kbpm = false;
        }else if($is_kbpm){
            $pcvl->is_kbbl = false;
            $pcvl->is_kbpl = false;
        }

        $pcvl->update([
            'is_kbbl' => $is_kbbl,
            'is_kbpl' => $is_kbpl,
            'is_kbpm' => $is_kbpm,
        ]);

        $pcvl->load(['town', 'barangay', 'purok', 'kbbl', 'kbpl', 'familyHead', 'assistor']);
        return new PcvlResource($pcvl);
    }

    public function updateHeadId(Request $request, Pcvl $pcvl)
    {
        $updateData = [];
        
        if ($request->has('kbbl_id')) {
            $updateData['kbbl_id'] = $request->input('kbbl_id');
        }
        
        if ($request->has('kbpl_id')) {
            $updateData['kbpl_id'] = $request->input('kbpl_id');
        }

        if (!empty($updateData)) {
            $pcvl->update($updateData);
        }

        $pcvl->load(['town', 'barangay', 'purok', 'kbbl', 'kbpl', 'familyHead', 'assistor']);
        return new PcvlResource($pcvl);
    }
}
