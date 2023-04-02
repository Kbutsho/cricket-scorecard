<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    
    public function homeVenue()
    {
        return $this->belongsTo(Venue::class, 'home_venue_id');
    }
}
