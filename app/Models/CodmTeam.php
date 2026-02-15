<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodmTeam extends Model
{
    protected $fillable = [
        'name',
        'players',
    ];

    protected $casts = [
        'players' => 'array',
    ];
}
