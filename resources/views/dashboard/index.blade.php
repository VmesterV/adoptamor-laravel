@extends('layouts.app')

@section('content')
<div class="py-8">
    
    <!-- Encabezado -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Panel de Administración</h1>
            <p class="text-gray-500">Bienvenido, {{ Auth::user()->name }}. Tienes tareas pendientes.</p>
        </div>
        <div class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-bold border border-blue-200">
            <i class="fa-solid fa-calendar mr-2"></i> {{ now()->format('d M, Y') }}
        </div>
    </div>

    <!-- 1. Tarjetas de Estadísticas (KPIs) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Usuarios -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 bg-purple-100 text-purple-600 rounded-full mr-4">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold">Usuarios Totales</p>
                <p class="text-2xl font-extrabold text-gray-800">{{ $stats['total_users'] }}</p>
            </div>
        </div>

        <!-- Mascotas -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 bg-pink-100 text-pink-600 rounded-full mr-4">
                <i class="fa-solid fa-paw text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold">Mascotas Totales</p>
                <p class="text-2xl font-extrabold text-gray-800">{{ $stats['total_pets'] }}</p>
            </div>
        </div>

        <!-- Ventas -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 bg-green-100 text-green-600 rounded-full mr-4">
                <i class="fa-solid fa-money-bill-wave text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold">Ingresos Totales</p>
                <p class="text-2xl font-extrabold text-gray-800">S/ {{ number_format($stats['income'], 2) }}</p>
            </div>
        </div>
        
        <!-- Pendientes (Alerta) -->
        <div class="bg-orange-50 p-6 rounded-2xl shadow-sm border border-orange-200 flex items-center">
            <div class="p-3 bg-orange-100 text-orange-600 rounded-full mr-4">
                <i class="fa-solid fa-bell text-xl"></i>
            </div>
            <div>
                <p class="text-orange-600 text-sm font-bold">Pendientes de Aprobar</p>
                <p class="text-2xl font-extrabold text-orange-700">
                    {{ $pendingPets->count() + $pendingProducts->count() }}
                </p>
            </div>
        </div>
    </div>

    <!-- 2. Área de Moderación (Tabs) -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden" x-data="{ tab: 'pets' }">
        
        <!-- Tab Headers -->
        <div class="flex border-b border-gray-100">
            <button @click="tab = 'pets'" 
                :class="{'border-b-2 border-pink-500 text-pink-600 bg-pink-50': tab === 'pets', 'text-gray-500 hover:bg-gray-50': tab !== 'pets'}"
                class="flex-1 py-4 text-center font-bold transition">
                <i class="fa-solid fa-dog mr-2"></i> Mascotas Pendientes ({{ $pendingPets->count() }})
            </button>
            <button @click="tab = 'products'" 
                :class="{'border-b-2 border-blue-500 text-blue-600 bg-blue-50': tab === 'products', 'text-gray-500 hover:bg-gray-50': tab !== 'products'}"
                class="flex-1 py-4 text-center font-bold transition">
                <i class="fa-solid fa-box-open mr-2"></i> Productos Pendientes ({{ $pendingProducts->count() }})
            </button>
        </div>

        <!-- Contenido -->
        <div class="p-6">
            
            <!-- TAB: MASCOTAS -->
            <div x-show="tab === 'pets'" class="space-y-4">
                @forelse($pendingPets as $pet)
                    <div class="flex flex-col md:flex-row items-center bg-white border border-gray-200 p-4 rounded-xl shadow-sm hover:shadow-md transition">
                        <!-- Foto -->
                        <img src="{{ $pet->image_url ?? 'https://via.placeholder.com/150' }}" class="w-24 h-24 object-cover rounded-lg mb-4 md:mb-0 md:mr-6">
                        
                        <!-- Info -->
                        <div class="flex-grow text-center md:text-left">
                            <h3 class="font-bold text-lg">{{ $pet->name }} <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">{{ $pet->breed }}</span></h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Publicado por: <strong class="text-gray-700">{{ $pet->user->name }}</strong> (Refugio)
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                <i class="fa-regular fa-clock"></i> {{ $pet->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Acciones -->
                        <div class="flex gap-3 mt-4 md:mt-0">
                            <form action="{{ route('admin.pet.approve', $pet->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-100 text-green-700 px-4 py-2 rounded-lg font-bold hover:bg-green-200 transition">
                                    <i class="fa-solid fa-check"></i> Aprobar
                                </button>
                            </form>
                            <form action="{{ route('admin.pet.reject', $pet->id) }}" method="POST" onsubmit="return confirm('¿Rechazar esta publicación?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg font-bold hover:bg-red-200 transition">
                                    <i class="fa-solid fa-times"></i> Rechazar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <i class="fa-solid fa-check-circle text-4xl mb-3 text-green-200"></i>
                        <p>¡Todo al día! No hay mascotas pendientes de revisión.</p>
                    </div>
                @endforelse
            </div>

            <!-- TAB: PRODUCTOS -->
            <div x-show="tab === 'products'" class="space-y-4" style="display: none;">
                @forelse($pendingProducts as $product)
                    <div class="flex flex-col md:flex-row items-center bg-white border border-gray-200 p-4 rounded-xl shadow-sm hover:shadow-md transition">
                        <!-- Foto -->
                        <img src="{{ $product->image_url ?? 'https://via.placeholder.com/150' }}" class="w-24 h-24 object-cover rounded-lg mb-4 md:mb-0 md:mr-6">
                        
                        <!-- Info -->
                        <div class="flex-grow text-center md:text-left">
                            <h3 class="font-bold text-lg">{{ $product->name }} <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded">S/ {{ $product->price }}</span></h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Vendedor: <strong class="text-gray-700">{{ $product->user->name }}</strong>
                            </p>
                            <p class="text-sm text-gray-400 mt-1">Stock: {{ $product->stock }} un.</p>
                        </div>

                        <!-- Acciones -->
                        <div class="flex gap-3 mt-4 md:mt-0">
                            <form action="{{ route('admin.product.approve', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-100 text-green-700 px-4 py-2 rounded-lg font-bold hover:bg-green-200 transition">
                                    <i class="fa-solid fa-check"></i> Aprobar
                                </button>
                            </form>
                            <form action="{{ route('admin.product.reject', $product->id) }}" method="POST" onsubmit="return confirm('¿Rechazar este producto?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg font-bold hover:bg-red-200 transition">
                                    <i class="fa-solid fa-times"></i> Rechazar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <i class="fa-solid fa-check-circle text-4xl mb-3 text-green-200"></i>
                        <p>¡Excelente! No hay productos pendientes.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

<!-- Pequeño script para manejar los tabs (usando AlpineJS sintaxis manual o Vanilla JS simple) -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection