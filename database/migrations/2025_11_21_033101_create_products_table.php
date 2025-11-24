<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Relación con Tienda o Refugio
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            // Categorías definidas en tus reglas
            $table->enum('category', ['toy', 'food', 'accessory']); 
            $table->text('description')->nullable();
            
            // Decimal para dinero (10 dígitos en total, 2 decimales)
            $table->decimal('price', 10, 2); 
            $table->integer('stock');
            
            $table->string('image_url')->nullable();
            
            // is_active: Para que el vendedor lo oculte temporalmente sin borrarlo
            $table->boolean('is_active')->default(true);
            
            // is_approved: Para que el Admin apruebe la venta
            $table->boolean('is_approved')->default(false);
            
            $table->timestamp('published_at')->nullable();
            
            // SoftDeletes: Borrado lógico
            $table->softDeletes();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};