<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    protected $table = 'itineraries'; // Nombre de la tabla

    protected $fillable = [
        'reserve_id', 
        'departure_city', 
        'arrival_city', 
        'departure_hour',
    ];

    public function reserve()
    {
        return $this->belongsTo(Reserve::class, 'reserve_id', 'id');
    }
}
