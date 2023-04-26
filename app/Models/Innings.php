<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Innings extends Model
{
    use HasFactory;
    protected $table = 'innings';
    protected $fillable = ['match_id', 'battingTeam_id', 'bowlingTeam_id', 'innings', 'status'];

    // public function match()
    // {
    //     return $this->belongsTo(CricketMatch::class, 'match_id', 'id');
    // }
}
