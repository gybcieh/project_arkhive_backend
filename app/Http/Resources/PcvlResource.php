<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TownResource;
use App\Http\Resources\BarangayResource;
use App\Http\Resources\PurokResource;

class PcvlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'town' => new TownResource($this->whenLoaded('town')),
            'barangay' => new BarangayResource($this->whenLoaded('barangay')),
            'purok' => new PurokResource($this->whenLoaded('purok')),
            'voter_type_id' => $this->voter_type_id,
            'ordinal' => $this->ordinal,
            'precinct_number' => $this->precinct_number,
            'voters_name' => $this->voters_name,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'is_a' => $this->is_a,
            'is_b' => $this->is_b,
            'is_k' => $this->is_k,
            'is_inc' => $this->is_inc,
            'is_jehovah' => $this->is_jehovah,
            'is_out_of_town' => $this->is_out_of_town,
            'is_abroad' => $this->is_abroad,
            'is_deceased' => $this->is_deceased,
            'is_kbbl' => $this->is_kbbl,
            'is_kbpl' => $this->is_kbpl,
            'is_kbpm' => $this->is_kbpm,
            'kbbl_id' => $this->kbbl_id,
            'kbpl_id' => $this->kbpl_id,
            'is_family_head' => $this->is_family_head,
            'family_head_id' => $this->family_head_id,
            'is_assistor' => $this->is_assistor,
            'assistor_id' => $this->assistor_id,
            'year' => $this->year,
            'deleted' => $this->deleted,
            'temp_is_kbbl' => $this->temp_is_kbbl,
            'temp_is_kbpl' => $this->temp_is_kbpl,
            'temp_is_kbpm' => $this->temp_is_kbpm,
            'temp_kbbl_id' => $this->temp_kbbl_id,
            'temp_kbpl_id' => $this->temp_kbpl_id,
        ];
    }
}
