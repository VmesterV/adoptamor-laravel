@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
    
    <div class="text-center mb-10">
        <h1 class="text-body text-3xl font-bold text-gray-800">Panel de Gestión</h1>
        <p class="text-body text-gray-500 mt-2">Administra tu inventario, ventas y adopciones.</p>
    </div>
    
    @php
        $isStoreOnly = Auth::user()->role === 'store';
        $gridClass = $isStoreOnly ? 'grid-cols-1 max-w-2xl mx-auto' : 'grid-cols-1 md:grid-cols-2';
    @endphp

    <div class="grid {{ $gridClass }} gap-8 mb-16">
        
        <!-- Opción Mascota (Solo Refugios/Admin) -->
        @if(Auth::user()->role === 'shelter' || Auth::user()->role === 'admin')
            <div onclick="openModal('modal-pet')" class="bg-surface p-8 rounded-2xl shadow-sm hover:shadow-xl transition cursor-pointer border border-gray-100 group text-center relative overflow-hidden h-full flex flex-col justify-center items-center">
                <div class="absolute top-0 left-0 w-full h-2 bg-pink-400"></div>
                <div class="bg-pink-50 w-16 h-16 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-paw text-2xl text-pink-500"></i>
                </div>
                <h2 class="text-body text-xl font-bold text-gray-800">Publicar Mascota</h2>
                <p class="text-sm text-gray-400 mt-2">Busca un hogar para un rescatado</p>
            </div>
        @endif

        <!-- Opción Producto (Todos) -->
        <div onclick="openModal('modal-product')" class="bg-surface p-8 rounded-2xl shadow-sm hover:shadow-xl transition cursor-pointer border border-gray-100 group text-center relative overflow-hidden h-full flex flex-col justify-center items-center">
            <div class="absolute top-0 left-0 w-full h-2 bg-blue-400"></div>
            <div class="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition">
                <i class="fa-solid fa-box-open text-2xl text-blue-500"></i>
            </div>
            <h2 class="text-body text-xl font-bold text-gray-800">Vender Producto</h2>
            <p class="text-sm text-gray-400 mt-2">Publica accesorios o comida</p>
        </div>
    </div>

    <!-- SECCIÓN 2: PUBLICACIONES ACTIVAS -->

    <div class="bg-surface rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-12">
        <!-- Header con Buscador -->
        <div class="bg-surface p-6 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-body text-lg font-bold text-gray-700 flex items-center">
                <i class="fa-solid fa-layer-group mr-2 text-blue-400"></i> Publicaciones Activas
            </h3>
            
            <form action="{{ route('publish') }}" method="GET" class="flex w-full md:w-auto gap-2">
                <div class="relative w-full md:w-64">
                    <input type="text" name="search_active" value="{{ request('search_active') }}" 
                           placeholder="Buscar en todo..." 
                           class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-300 outline-none">
                    <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400 text-xs"></i>
                </div>
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 transition">Buscar</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface text-gray-500 text-xs uppercase border-b border-gray-100">
                    <tr class="text-body">
                        <th class="px-6 py-3 font-bold">Item</th>
                        <th class="px-6 py-3 font-bold">Detalle</th>
                        <th class="px-6 py-3 font-bold">Estado</th>
                        <th class="px-6 py-3 font-bold">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    {{-- Productos --}}
                    @foreach($myProducts as $prod)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 flex items-center gap-3">
                            <img src="{{ $prod->image_url }}" class="h-10 w-10 rounded object-cover border border-gray-200">
                            <div>
                                <div class="text-body font-bold text-gray-900">{{ $prod->name }}</div>
                                <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded uppercase">Producto</span>
                            </div>
                        </td>
                        <td class="text-body px-6 py-3 text-gray-500 text-xs">Stock: {{ $prod->stock }} <br> S/ {{ $prod->price }}</td>
                        <td class="px-6 py-3">
                            @if($prod->is_approved) <span class="text-green-600 font-bold text-xs bg-green-50 px-2 py-1 rounded">Aprobado</span>
                            @else <span class="text-yellow-600 font-bold text-xs bg-yellow-50 px-2 py-1 rounded">Pendiente</span> @endif
                        </td>
                        <td class="text-body px-6 py-3 text-gray-400 text-xs">{{ $prod->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach

                    {{-- Mascotas --}}
                    @foreach($myPets as $pet)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 flex items-center gap-3">
                            <img src="{{ $pet->image_url }}" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                            <div>
                                <div class="text-body font-bold text-gray-900">{{ $pet->name }}</div>
                                <span class="text-[10px] bg-pink-100 text-pink-700 px-1.5 py-0.5 rounded uppercase">Mascota</span>
                            </div>
                        </td>
                        <td class="text-body px-6 py-3 text-gray-500 text-xs">{{ $pet->breed }} <br> {{ $pet->age }}</td>
                        <td class="px-6 py-3">
                            @if($pet->is_approved) <span class="text-green-600 font-bold text-xs bg-green-50 px-2 py-1 rounded">Aprobado</span>
                            @else <span class="text-yellow-600 font-bold text-xs bg-yellow-50 px-2 py-1 rounded">Pendiente</span> @endif
                        </td>
                        <td class="text-body px-6 py-3 text-gray-400 text-xs">{{ $pet->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Paginación Productos y Mascotas -->
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $myProducts->appends(['search_active' => request('search_active')])->links() }}
                @if(Auth::user()->role !== 'store')
                    {{ $myPets->appends(['search_active' => request('search_active')])->links() }}
                @endif
            </div>
        </div>
    </div>

    <!-- LÓGICA DE GRID PARA SECCIONES INFERIORES -->

    <div class="grid grid-cols-1 {{ $isStoreOnly ? '' : 'lg:grid-cols-2' }} gap-8">
        
        <!-- SECCIÓN 3: VENTAS REALIZADAS -->

        <div class="bg-surface rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
            <div class="bg-surface p-4 border-b border-gray-100 bg-blue-50 flex flex-col sm:flex-row justify-between items-center gap-3">
                <h3 class="text-body font-bold text-gray-700 flex items-center"><i class="fa-solid fa-cash-register mr-2 text-blue-500"></i> Ventas</h3>
                
                <!-- Buscador de Ventas -->
                <form action="{{ route('publish') }}" method="GET" class="w-full sm:w-auto">
                    <div class="relative">
                        <input type="text" name="search_sales" value="{{ request('search_sales') }}" placeholder="Producto o Comprador..." 
                               class="w-full pl-7 pr-2 py-1.5 border border-blue-200 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none">
                        <i class="fa-solid fa-search absolute left-2 top-2 text-blue-300 text-xs"></i>
                    </div>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface text-gray-500 text-xs uppercase border-b border-gray-100">
                        <tr class="text-body">
                            <th class="px-4 py-3">Detalle</th>
                            <th class="px-4 py-3">Comprador</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($salesHistory as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="text-body font-bold text-gray-800">{{ $sale->product->name }}</div>
                                    <div class="text-body text-xs text-gray-400">{{ $sale->created_at->format('d M') }} | S/ {{ number_format($sale->unit_price * $sale->quantity, 2) }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-800">{{ $sale->order->user->name ?? 'Usuario' }}</div>
                                    <!-- Datos de contacto añadidos -->
                                    <div class="flex flex-col text-[11px] text-gray-500 mt-1 space-y-1">
                                        <a href="https://wa.me/51{{ $sale->order->user->phone }}" target="_blank" class="flex items-center hover:text-green-600 transition">
                                            <i class="fa-brands fa-whatsapp text-green-500 mr-1.5"></i> {{ $sale->order->user->phone ?? 'Sin número' }}
                                        </a>
                                        <span class="flex items-center">
                                            <i class="fa-solid fa-envelope text-gray-400 mr-1.5"></i> {{ $sale->order->user->email ?? 'Sin correo' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($sale->order->status === 'completed' || $sale->order->status === 'shipped')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold">ENTREGADO</span>
                                    @elseif($sale->order->status === 'cancelled')
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-[10px] font-bold">CANCELADO</span>
                                    @else
                                        <div class="flex justify-center gap-2">
                                            {{-- Botón Concretar --}}
                                            <form action="{{ route('sale.deliver', $sale->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-blue-100 text-blue-600 hover:bg-blue-200 px-2 py-1 rounded text-[10px] font-bold transition" title="Marcar como entregado">
                                                    CONCRETAR <i class="fa-solid fa-check ml-1"></i>
                                                </button>
                                            </form>
                                            
                                            {{-- Botón Cancelar (NUEVO) --}}
                                            <form action="{{ route('sale.cancel', $sale->id) }}" method="POST" onsubmit="return confirm('¿Cancelar esta venta? El stock será restaurado.');">
                                                @csrf
                                                <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-200 px-2 py-1 rounded text-[10px] font-bold transition" title="Cancelar Venta">
                                                    <i class="fa-solid fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-body p-6 text-center text-gray-400 text-xs">Sin ventas recientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-auto p-2 border-t border-gray-100">
                {{ $salesHistory->appends(['search_sales' => request('search_sales')])->links() }}
            </div>
        </div>

        <!-- SECCIÓN 4: FINALES FELICES (ADOPCIONES) -->

        @if(Auth::user()->role === 'shelter' || Auth::user()->role === 'admin')
        <div class="bg-surface rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
            <div class="bg-surface p-4 border-b border-gray-100 bg-pink-50 flex flex-col sm:flex-row justify-between items-center gap-3">
                <h3 class="text-body font-bold text-gray-700 flex items-center"><i class="fa-solid fa-heart mr-2 text-pink-500"></i> Solicitudes</h3>
                
                <!-- Buscador de Adopciones -->
                <form action="{{ route('publish') }}" method="GET" class="w-full sm:w-auto">
                    <div class="relative">
                        <input type="text" name="search_adoptions" value="{{ request('search_adoptions') }}" placeholder="Mascota o Adoptante..." 
                               class="w-full pl-7 pr-2 py-1.5 border border-pink-200 rounded text-xs focus:ring-1 focus:ring-pink-400 outline-none">
                        <i class="fa-solid fa-search absolute left-2 top-2 text-pink-300 text-xs"></i>
                    </div>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface text-gray-500 text-xs uppercase border-b border-gray-100">
                        <tr class="text-body">
                            <th class="px-4 py-3">Mascota</th>
                            <th class="px-4 py-3">Solicitante</th>
                            <th class="px-4 py-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($adoptionRequests as $adopt)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 flex items-center gap-2">
                                    <img src="{{ $adopt->pet->image_url }}" class="h-8 w-8 rounded-full object-cover border border-gray-200">
                                    <div>
                                        <div class="text-body font-bold text-gray-800 text-xs">{{ $adopt->pet->name }}</div>
                                        <div class="text-body text-[10px] text-gray-400">{{ $adopt->created_at->format('d M') }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-800">{{ $adopt->user->name }}</div>
                                    <!-- Datos de contacto añadidos -->
                                    <div class="flex flex-col text-[11px] text-gray-500 mt-1 space-y-1">
                                        <a href="https://wa.me/51{{ $adopt->user->phone }}" target="_blank" class="flex items-center hover:text-green-600 transition">
                                            <i class="fa-brands fa-whatsapp text-green-500 mr-1.5"></i> {{ $adopt->user->phone ?? 'Sin número' }}
                                        </a>
                                        <span class="flex items-center">
                                            <i class="fa-solid fa-envelope text-gray-400 mr-1.5"></i> {{ $adopt->user->email }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($adopt->status === 'approved')
                                        <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-[10px] font-bold">ADOPTADO</span>
                                    @elseif($adopt->status === 'rejected')
                                        <span class="text-gray-400 text-[10px] font-bold bg-gray-100 px-2 py-1 rounded">RECHAZADO</span>
                                    @else
                                        <div class="flex justify-center gap-2">
                                            {{-- Botón Aprobar --}}
                                            <form action="{{ route('adoption.approve', $adopt->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-pink-100 text-pink-600 hover:bg-pink-200 px-2 py-1 rounded text-[10px] font-bold transition" onclick="return confirm('¿Aprobar adopción? La mascota dejará de estar disponible.')">
                                                    APROBAR <i class="fa-solid fa-check ml-1"></i>
                                                </button>
                                            </form>

                                            {{-- Botón Rechazar (NUEVO) --}}
                                            <form action="{{ route('adoption.reject', $adopt->id) }}" method="POST" onsubmit="return confirm('¿Rechazar esta solicitud?');">
                                                @csrf
                                                <button type="submit" class="bg-gray-100 text-gray-500 hover:bg-gray-200 px-2 py-1 rounded text-[10px] font-bold transition" title="Rechazar solicitud">
                                                    <i class="fa-solid fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-body p-6 text-center text-gray-400 text-xs">No hay solicitudes pendientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-auto p-2 border-t border-gray-100">
                {{ $adoptionRequests->appends(['search_adoptions' => request('search_adoptions')])->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

<!-- ================= MODAL MASCOTA ================= -->
<div id="modal-pet" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full relative animate-fade-in my-8">
        <button onclick="closeModal('modal-pet')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-times text-xl"></i>
        </button>
        
        <div class="p-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fa-solid fa-dog text-pink-500 mr-2"></i> Nueva Mascota
            </h3>
            
            <form action="{{ route('publish.pet.store') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-pink-300 outline-none">
                </div>

                <!-- Tipo y Edad -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tipo</label>
                        <select name="type" class="w-full border border-gray-300 rounded-lg p-2 bg-white">
                            <option value="dog">Perro</option>
                            <option value="cat">Gato</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Edad (ej. 2 años)</label>
                        <input type="text" name="age" required class="w-full border border-gray-300 rounded-lg p-2">
                    </div>
                </div>

                <!-- Raza -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Raza</label>
                    <input type="text" name="breed" placeholder="Mestizo, Bulldog..." class="w-full border border-gray-300 rounded-lg p-2">
                </div>

                <!-- Imagen URL -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">URL de la Foto</label>
                    <input type="url" name="image_url" placeholder="https://..." required class="w-full border border-gray-300 rounded-lg p-2">
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Historia / Descripción</label>
                    <textarea name="description" rows="3" required class="w-full border border-gray-300 rounded-lg p-2"></textarea>
                </div>

                <button type="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 rounded-xl transition shadow-lg mt-2">
                    Enviar a Revisión
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL PRODUCTO ================= -->
<div id="modal-product" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full relative animate-fade-in my-8">
        <button onclick="closeModal('modal-product')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-times text-xl"></i>
        </button>
        
        <div class="p-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fa-solid fa-box-open text-blue-500 mr-2"></i> Nuevo Producto
            </h3>
            
            <form action="{{ route('publish.product.store') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Producto</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-300 outline-none">
                </div>

                <!-- Categoría y Precio -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Categoría</label>
                        <select name="category" class="w-full border border-gray-300 rounded-lg p-2 bg-white">
                            <option value="toy">Juguete</option>
                            <option value="food">Comida</option>
                            <option value="accessory">Accesorio</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Precio (S/)</label>
                        <input type="number" step="0.10" name="price" required class="w-full border border-gray-300 rounded-lg p-2">
                    </div>
                </div>

                <!-- Stock -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Stock Disponible</label>
                    <input type="number" name="stock" required class="w-full border border-gray-300 rounded-lg p-2">
                </div>

                <!-- Imagen URL -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">URL de la Foto</label>
                    <input type="url" name="image_url" placeholder="https://..." required class="w-full border border-gray-300 rounded-lg p-2">
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                    <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg p-2"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-xl transition shadow-lg mt-2">
                    Enviar Producto
                </button>
            </form>
        </div>
    </div>
</div>
@endsection