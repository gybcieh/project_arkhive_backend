<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Town;
use App\Models\Barangay;
use App\Models\Purok;

class Pcvl extends Model
{
    protected $fillable = [
        'town_id',
        'barangay_id',
        'purok_id',
        'voter_type_id',
        'ordinal',
        'precinct_number',
        'voters_name',
        'birth_date',
        'gender',
        'is_a',
        'is_b',
        'is_k',
        'is_inc',
        'is_jehovah',
        'is_out_of_town',
        'is_abroad',
        'is_deceased',
        'is_kbbl',
        'is_kbpl',
        'is_kbpm',
        'kbbl_id',
        'kbpl_id',
        'is_family_head',
        'family_head_id',
        'is_assistor',
        'assistor_id',
        'year',
        'deleted',
        'temp_is_kbbl',
        'temp_is_kbpl',
        'temp_is_kbpm',
        'temp_kbbl_id',
        'temp_kbpl_id'
    ];
    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }

    // Self-referencing relationships
    public function kbbl()
    {
        return $this->belongsTo(Pcvl::class, 'kbbl_id')
                    ->select(['id', 'town_id', 'barangay_id', 'purok_id', 'voters_name', 'is_a', 'is_b', 'is_k', 'is_kbbl', 'is_kbpl', 'is_kbpm', 'precinct_number'])
                    ->with(['town', 'barangay', 'purok']);
    }

    public function kbpl()
    {
        return $this->belongsTo(Pcvl::class, 'kbpl_id')
                    ->select(['id', 'town_id', 'barangay_id', 'purok_id', 'voters_name', 'is_a', 'is_b', 'is_k', 'is_kbbl', 'is_kbpl', 'is_kbpm', 'kbbl_id', 'precinct_number'])
                    ->with(['town', 'barangay', 'purok', 'kbbl']);
    }

    // You might also want relationships for family_head and assistor
    public function familyHead()
    {
        return $this->belongsTo(Pcvl::class, 'family_head_id')
                    ->select(['id', 'town_id', 'barangay_id', 'purok_id', 'voters_name', 'is_a', 'is_b', 'is_k', 'is_kbbl', 'is_kbpl', 'is_kbpm', 'precinct_number']);
    }

    public function assistor()
    {
        return $this->belongsTo(Pcvl::class, 'assistor_id')
                    ->select(['id', 'town_id', 'barangay_id', 'purok_id', 'voters_name', 'is_a', 'is_b', 'is_k', 'is_kbbl', 'is_kbpl', 'is_kbpm', 'precinct_number']);
    }

    public function memberKbpls(){
        return $this->hasMany(Pcvl::class, 'kbpl_id')
                    ->with(['town', 'barangay', 'purok'])
                    ->select(['id', 'town_id', 'barangay_id', 'purok_id', 'kbpl_id', 'voters_name', 'is_a', 'is_b', 'is_k']);
    }

    public function memberKbbls(){
        return $this->hasMany(Pcvl::class, 'kbbl_id')
                    ->with(['town', 'barangay', 'purok'])
                    ->select(['id', 'town_id', 'barangay_id', 'purok_id', 'kbbl_id', 'voters_name', 'is_a', 'is_b', 'is_k']);
    }
}


