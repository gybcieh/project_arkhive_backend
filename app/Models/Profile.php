<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Town;
use App\Models\Barangay;
use App\Models\Purok;

class Profile extends Model
{
    protected $fillable = [
        'town_id',
        'barangay_id',
        'purok_id',
        'pcvl_id',
        'last_name',
        'first_name',
        'ext_name',
        'middle_name',
        'nick_name',
        'birth_date',
        'gender',
        'religion',
        'tribe',
        'civil_status',
        'occupation',
        'ed_attainment',
        'org_affiliation',
        'mobile_number',
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
}

