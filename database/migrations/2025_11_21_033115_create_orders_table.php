<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Comprador
            
            $table->decimal('total', 10, 2);
            
            // Tipo de entrega
            $table->enum('delivery_type', ['pickup', 'delivery']); 
            
            // Dirección de envío (Puede ser NULL si es 'pickup')
            $table->text('shipping_address')->nullable(); 
            
            // Método de pago
            $table->enum('payment_method', ['card', 'yape']);
            
            // Estado del pedido
            $table->enum('status', ['pending', 'paid', 'shipped', 'completed', 'cancelled'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
