<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    // Relación: Este detalle pertenece a una Orden principal
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relación: Este detalle corresponde a un Producto específico
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}