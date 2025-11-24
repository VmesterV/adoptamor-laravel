@extends('layouts.app')

@section('content')
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb simple -->
        <nav class="text-sm mb-8 text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-pink-500">Inicio</a> 
            <span class="mx-2">/</span>
            <a href="{{ route('store') }}" class="hover:text-pink-500">Tienda</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $product->name }}</span>
        </nav>

        <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
            <!-- Columna Izquierda: Imagen -->
            <div class="flex flex-col">
                <div class="w-full aspect-w-1 aspect-h-1 bg-gray-100 rounded-lg overflow-hidden sm:aspect-w-2 sm:aspect-h-3">
                    <img src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-center object-cover hover:scale-105 transition duration-500">
                </div>
            </div>

            <!-- Columna Derecha: Info -->
            <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                <!-- Badge Vendedor -->
                <div class="mb-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">
                        Vendido por: {{ $product->user->name }}
                    </span>
                    @if($product->stock < 5)
                        <span class="ml-2 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">
                            ¡Quedan pocos!
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ $product->name }}</h1>

                <div class="mt-3">
                    <h2 class="sr-only">Información del producto</h2>
                    <p class="text-3xl text-gray-900 font-bold">S/ {{ $product->price }}</p>
                </div>

                <div class="mt-6">
                    <h3 class="sr-only">Descripción</h3>
                    <div class="text-base text-gray-700 space-y-6">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col space-y-4">
                    
                    <!-- Selector de Cantidad (Visual) -->
                    <div class="w-32">
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Cantidad</label>
                        <select id="quantity-selector" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm rounded-md border">
                            @for($i = 1; $i <= min($product->stock, 10); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Botón Agregar -->
                    <button onclick="addToCartWithQty()" 
                            class="max-w-xs w-full bg-pink-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 shadow-lg transition transform hover:-translate-y-1">
                        <i class="fa-solid fa-cart-plus mr-2"></i> Agregar al Carrito
                    </button>
                    
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fa-solid fa-shield-alt text-green-500"></i> Compra segura. El 100% de las ganancias van al vendedor verificado.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function addToCartWithQty() {
        const qty = parseInt(document.getElementById('quantity-selector').value);
        const product = {
            id: {{ $product->id }},
            name: '{{ addslashes($product->name) }}',
            price: {{ $product->price }},
            image: '{{ $product->image_url }}',
            seller: '{{ addslashes($product->user->name) }}'
        };

        // Lógica manual para agregar varias veces según la cantidad seleccionada
        // Nota: Idealmente tu función addToCart debería aceptar cantidad, pero esto es un parche rápido compatible con tu JS actual.
        let cart = JSON.parse(localStorage.getItem('adoptamor_cart')) || [];
        const existing = cart.find(p => p.id === product.id);
        
        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({ ...product, qty: qty });
        }
        
        localStorage.setItem('adoptamor_cart', JSON.stringify(cart));
        
        // Actualizar UI globalmente (función definida en layout/app.js)
        // Necesitamos recargar la UI del carrito. Si tu app.js expone updateCartUI, llámala.
        // Si no, un reload simple funciona o despachar un evento.
        window.location.reload(); // Simple para asegurar que se vea reflejado
    }
</script>
@endsection