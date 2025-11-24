<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importante importar esto

class Pet extends Model
{
    use HasFactory, SoftDeletes; // Activamos SoftDeletes aquí

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'age',
        'breed',
        'description',
        'image_url',
        'status',
        'is_approved',
        'published_at',
    ];

    // Convertimos estos campos automáticamente a tipos nativos
    protected $casts = [
        'is_approved' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relación: Una mascota pertenece a un Usuario (Refugio)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Una mascota puede tener una adopción
    public function adoption()
    {
        return $this->hasOne(Adoption::class);
    }
}