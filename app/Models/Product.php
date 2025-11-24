<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'description',
        'price',
        'stock',
        'image_url',
        'is_active',
        'is_approved',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relación: Un producto pertenece a un Usuario (Tienda/Refugio)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Relación: Un producto puede estar en muchos detalles de pedido
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}