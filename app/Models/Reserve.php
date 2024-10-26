<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
{
    use HasFactory;

    protected $table = 'reserves'; // Nombre de la tabla

    protected $fillable = [
        'currency', 
        'qty_passengers', 
        'adult', 
        'child', 
        'baby',
    ];

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class, 'reserve_id', 'id');
    }
}
