<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pet;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. USUARIOS (Credenciales para pruebas)
        
        // ADMIN (Para entrar al Dashboard)
        $admin = User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        // REFUGIO (Para tener mascotas)
        $shelter = User::create([
            'name' => 'Refugio Patitas Felices',
            'email' => 'refugio@gmail.com',
            'phone' => '902932988',
            'password' => Hash::make('12345678'),
            'role' => 'shelter',
            'department' => 'Cusco',
            'province' => 'Cusco',
            'district' => 'Cusco',
            'address' => 'Av. Santiango 123',
            'photo_url' => 'https://cdn-icons-png.flaticon.com/512/3448/3448616.png',
        ]);

        // TIENDA (Para tener productos)
        $store = User::create([
            'name' => 'Super PetShop',
            'email' => 'ventas@gmail.com',
            'phone' => '902932988',
            'password' => Hash::make('12345678'),
            'role' => 'store',
            'department' => 'Cusco',
            'province' => 'Cusco',
            'district' => 'Cusco',
            'photo_url' => 'https://cdn-icons-png.flaticon.com/512/1198/1198368.png',
        ]);

        // PERSONA (Para probar compras/adopción)
        User::create([
            'name' => 'Alexander',
            'email' => 'alex@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'person',
            'phone' => '969394421',
        ]);

        // 2. MASCOTAS (PETS)

        // Mascota Aprobada (Visible en Home)
        Pet::create([
            'user_id' => $shelter->id,
            'name' => 'Max',
            'type' => 'dog',
            'age' => '2 años',
            'breed' => 'Golden Retriever',
            'description' => 'Max es un perro muy juguetón, le encantan los niños y correr en el parque. Está vacunado.',
            'image_url' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600&q=80',
            'status' => 'available',
            'is_approved' => true, // APROBADO
            'published_at' => now(),
        ]);

        // Mascota Aprobada (Visible en Home)
        Pet::create([
            'user_id' => $shelter->id,
            'name' => 'Luna',
            'type' => 'cat',
            'age' => '5 meses',
            'breed' => 'Mestizo',
            'description' => 'Pequeña gatita rescatada. Es un poco tímida al principio pero muy cariñosa.',
            'image_url' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80',
            'status' => 'available',
            'is_approved' => true, // APROBADO
            'published_at' => now()->subDays(2), // Publicado hace 2 días
        ]);

        // Mascota Pendiente (Para aprobar en Dashboard)
        Pet::create([
            'user_id' => $shelter->id,
            'name' => 'Rocky',
            'type' => 'dog',
            'age' => '4 años',
            'breed' => 'Bulldog',
            'description' => 'Rocky busca un hogar tranquilo. Ideal para personas mayores.',
            'image_url' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&w=600&q=80',
            'status' => 'available',
            'is_approved' => false, // PENDIENTE (Ver en Dashboard)
        ]);

        // 3. PRODUCTOS (PRODUCTS)

        // Producto Aprobado (Visible en Tienda)
        Product::create([
            'user_id' => $store->id,
            'name' => 'Saco de Comida Premium 15kg',
            'category' => 'food',
            'description' => 'Alimento balanceado para perros adultos de todas las razas.',
            'price' => 145.50,
            'stock' => 20,
            'image_url' => 'https://i.postimg.cc/TY0SdSC7/COMIDA-PARA-PERRO.webp',
            'is_active' => true,
            'is_approved' => true, // APROBADO
            'published_at' => now(),
        ]);

        // Producto Aprobado
        Product::create([
            'user_id' => $store->id,
            'name' => 'Juguete Hueso de Goma',
            'category' => 'toy',
            'description' => 'Indestructible, ideal para perros con mordida fuerte.',
            'price' => 25.00,
            'stock' => 50,
            'image_url' => 'https://images.unsplash.com/photo-1576201836106-db1758fd1c97?auto=format&fit=crop&w=600&q=80',
            'is_active' => true,
            'is_approved' => true,
            'published_at' => now(),
        ]);

        // Producto Pendiente (Para aprobar en Dashboard)
        Product::create([
            'user_id' => $shelter->id, // El refugio también vende algo para recaudar fondos
            'name' => 'Pañoleta Solidaria',
            'category' => 'accessory',
            'description' => 'Compra esta pañoleta y ayuda a alimentar a un cachorro.',
            'price' => 15.00,
            'stock' => 100,
            'image_url' => 'https://images.unsplash.com/photo-1551717743-49959800b1f6?auto=format&fit=crop&w=600&q=80',
            'is_active' => true,
            'is_approved' => false, // PENDIENTE
        ]);
    }
}
