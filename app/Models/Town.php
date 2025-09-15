<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    protected $fillable = [
        'name',
        'abbr',
        'category',
    ];
}
