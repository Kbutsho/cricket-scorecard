<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Team;


class CricketMatch extends Model
{
    use HasFactory;
    protected $fillable = ['team_a_id', 'team_b_id', 'venue', 'format', 'over', 'time', 'status'];

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a_id', 'id');
    }
    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b_id' , 'id');
    }
}
