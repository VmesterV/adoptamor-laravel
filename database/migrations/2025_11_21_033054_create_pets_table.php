<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            
            // Relación con el Refugio (User)
            // Si el usuario se borra, se borran sus mascotas (cascade)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            $table->string('name');
            $table->enum('type', ['dog', 'cat']); // Solo Perros y Gatos
            $table->string('age'); // Ej: "2 años"
            $table->string('breed')->nullable(); // Raza
            $table->text('description');
            $table->string('image_url')->nullable(); // URL de la foto
            
            // Estado de adopción
            $table->enum('status', ['available', 'adopted'])->default('available');
            
            // Aprobación del ADMIN (Regla de negocio)
            $table->boolean('is_approved')->default(false); 
            
            // Fecha para ordenar (Lo más reciente primero)
            $table->timestamp('published_at')->nullable(); 
            
            // SoftDeletes: Crea la columna 'deleted_at'
            // Permite restaurar mascotas borradas accidentalmente
            $table->softDeletes(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};