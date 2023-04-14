<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;
    protected $fillable = [
        'match_id',
        'score_line',
        'run',
        'ball',
        'batsman_id',
        'bowler_id',
        'battingTeam_id',
        'bowlingTeam_id',
        'wicket',
        'extra'
    ];
}
