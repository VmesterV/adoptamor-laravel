<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'delivery_type',
        'shipping_address',
        'payment_method',
        'status',
    ];

    // Relación: Un pedido tiene muchos detalles (items comprados)
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Relación: Un pedido pertenece a un Usuario (el comprador)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}