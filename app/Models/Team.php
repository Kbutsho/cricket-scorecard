<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'head_coach','home_venue_id'];
    public function teamPlayers()
    {
        return $this->hasMany(Player::class, 'team_id', 'id')->with('team');
    }
    public function homeVenue()
    {
        return $this->belongsTo(Venue::class, 'home_venue_id', 'id');
    }
}
