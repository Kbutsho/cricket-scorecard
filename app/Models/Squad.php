<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Squad extends Model
{
    use HasFactory;
    protected $fillable = [
        'match_id',
        'player_id',
        'player_name',
        'team_id',
        'team_name'
    ];
}
