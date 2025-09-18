<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TownResource;
use App\Http\Resources\BarangayResource;
use App\Http\Resources\PurokResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'town' => new TownResource($this->whenLoaded('town')),
            'barangay' => new BarangayResource($this->whenLoaded('barangay')),
            'purok' => new PurokResource($this->whenLoaded('purok')),
            'pcvl_id' => $this->pcvl_id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'ext_name' => $this->ext_name,
            'middle_name' => $this->middle_name,
            'nick_name' => $this->nick_name,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'religion' => $this->religion,
            'tribe' => $this->tribe,
            'civil_status' => $this->civil_status,
            'occupation' => $this->occupation,
            'ed_attainment' => $this->ed_attainment,
            'org_affiliation' => $this->org_affiliation,
            'mobile_number' => $this->mobile_number,
        ];
    }
}
