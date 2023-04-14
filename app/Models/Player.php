<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'team_id', 'role', 'batting_style', 'bowling_style', 'born', 'biography', 'status'];
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
