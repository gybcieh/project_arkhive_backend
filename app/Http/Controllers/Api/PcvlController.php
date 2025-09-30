<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pcvl;
use App\Http\Resources\PcvlResource;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $kbplId = $request->query('kbpl_id');
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
            $query->where('kbbl_id', $kbblId);
        }

        if ($kbplId) {
            $query->where('temp_kbpl_id', $kbplId);
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

    public function fetchKbpls(Request $request)
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
        $query->where('temp_is_kbpl', true);
        $pcvls = $query->get()->sortBy('voters_name')->values();
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

    public function fetchNewKbpls(Request $request)
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
            $query->where('new_purok_id', $purok_id);
        }
        $query->where('is_kbpl', true);
        $pcvls = $query->get()->sortBy('voters_name')->values();
        $rows = $pcvls->count();

        // return PcvlResource::collection($pcvls);
        return response()->json([
            'rows' => $rows,
            'data' => PcvlResource::collection($pcvls)
        ]);
    }

    public function updateVoterTag(Request $request, Pcvl $pcvl)
    {
        // Prepare update data - start with empty array
        $updateData = [];

        // Conditionally add fields only if they are provided in the request
        if ($request->has('is_inc')) {
            $updateData['is_inc'] = $request->input('is_inc', false);
        }
        
        if ($request->has('is_jehovah')) {
            $updateData['is_jehovah'] = $request->input('is_jehovah', false);
        }
        
        if ($request->has('is_abroad')) {
            $updateData['is_abroad'] = $request->input('is_abroad', false);
        }
        
        if ($request->has('is_out_of_town')) {
            $updateData['is_out_of_town'] = $request->input('is_out_of_town', false);
        }
        
        if ($request->has('is_deceased')) {
            $updateData['is_deceased'] = $request->input('is_deceased', false);
        }

        // Handle A, B, K fields - only update if one of them is provided
        if ($request->has('is_a') || $request->has('is_b') || $request->has('is_k')) {
            $is_a = $request->input('is_a', false);
            $is_b = $request->input('is_b', false);
            $is_k = $request->input('is_k', false);

            // Apply mutual exclusivity logic
            if($is_a){
                $updateData['is_a'] = true;
                $updateData['is_b'] = false;
                $updateData['is_k'] = false;
            }else if($is_b){
                $updateData['is_a'] = false;
                $updateData['is_b'] = true;
                $updateData['is_k'] = false;
            }else if($is_k){
                $updateData['is_a'] = false;
                $updateData['is_b'] = false;
                $updateData['is_k'] = true;
            }
        }

        $pcvl->update($updateData);

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

    public function printKbblMembers(Request $request, Pcvl $pcvl)
    {
        $townId = $request->query('town_id');
        $barangayId = $request->query('barangay_id');

        if (!$townId || !$barangayId) {
            return response()->json(['error' => 'town_id and barangay_id are required'], 400);
        }

        // Get town and barangay names for the header
        $town = \App\Models\Town::find($townId);
        $barangay = \App\Models\Barangay::find($barangayId);

        if (!$town || !$barangay) {
            return response()->json(['error' => 'Invalid town_id or barangay_id'], 404);
        }

        // Get all KBBLs in the specified town and barangay
        $kbbls = Pcvl::with(['town', 'barangay', 'purok'])
            ->where('town_id', $townId)
            ->where('barangay_id', $barangayId)
            ->where('is_kbbl', true)
            ->orderBy('voters_name', 'asc')
            ->get();

        if ($kbbls->isEmpty()) {
            return response()->json(['error' => 'No KBBLs found for the specified town and barangay'], 404);
        }

        $kbblGroups = [];

        // For each KBBL, get their members (KBPLs) - include even if no members
        foreach ($kbbls as $kbbl) {
            $members = Pcvl::with(['town', 'barangay', 'purok'])
                ->where('town_id', $townId)
                ->where('barangay_id', $barangayId)
                ->where('is_kbpl', true)
                ->where('kbbl_id', $kbbl->id)
                ->orderBy('voters_name', 'asc')
                ->get();

            // Always include KBBL, even if no members
            $kbblGroups[] = [
                'kbbl_name' => $kbbl->voters_name,
                'kbbl_id' => $kbbl->id,
                'purok' => $kbbl->purok,
                'members' => $members
            ];
        }

        if (empty($kbblGroups)) {
            return response()->json(['error' => 'No KBBL members found'], 404);
        }

        // Generate PDF
        $pdf = Pdf::loadView('kbbl-members', [
            'kbblGroups' => $kbblGroups,
            'townName' => $town->name,
            'barangayName' => $barangay->name
        ]);

        $filename = 'KBBL_Members_' . str_replace(' ', '_', $town->name) . '_' . str_replace(' ', '_', $barangay->name) . '_' . date('Y-m-d') . '.pdf';

        // Check if client wants base64 response
        if ($request->query('format') === 'base64') {
            $pdfContent = $pdf->output();
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'pdf_base64' => base64_encode($pdfContent),
                'mime_type' => 'application/pdf'
            ]);
        }

        // Return PDF as blob/stream with proper headers (default)
        return $pdf->stream($filename);
    }
}