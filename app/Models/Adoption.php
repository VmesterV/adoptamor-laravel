<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adoption extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'user_id',
        'status',
    ];

    // Relación: Una solicitud de adopción es para una Mascota específica
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    // Relación: Una solicitud es realizada por un Usuario (el adoptante)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}