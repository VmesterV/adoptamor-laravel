@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row gap-6 py-6">
    
    <!-- LEFT SIDE -->
    <aside class="w-full md:w-1/5 space-y-6 h-full">
        
        <!-- Buscador Sticky -->
        <div class="sticky top-24 z-30 bg-surface p-4 rounded-xl shadow-sm border border-custom transition-colors">
            <h3 class="font-bold text-body mb-3"><i class="fa-solid fa-search mr-2 text-primary"></i>Buscar</h3>
            <input type="text" id="store-search" placeholder="Producto..." 
                   class="w-full p-2 input-custom rounded-lg focus:ring-2 focus:ring-pink-300 outline-none transition text-sm text-body"
                   onkeypress="if(event.key === 'Enter') applyStoreFilters()">
        </div>

        <!-- Filtros -->
        <div class="bg-surface rounded-xl shadow-sm border border-custom overflow-hidden transition-colors">
            
            <!-- Toggle Móvil -->
            <div class="md:hidden p-4 bg-surface border-b border-custom flex justify-between items-center cursor-pointer" onclick="toggleStoreFiltersMobile()">
                <h3 class="font-bold text-body">Filtros Tienda</h3>
                <i id="store-filter-arrow" class="fa-solid fa-chevron-down text-body opacity-50"></i>
            </div>

            <div class="hidden md:block p-4" id="store-filters">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="hidden md:block font-bold text-body">Filtros</h3>
                    <button onclick="clearStoreFilters()" class="text-xs text-red-400 hover:text-red-500 font-bold ml-auto md:ml-0">Limpiar</button>
                </div>

                <!-- Ubicación -->
                <div class="mb-4 space-y-2">
                    <label class="block text-sm font-semibold text-body opacity-80">Ubicación</label>
                    <input type="text" id="store-dept" placeholder="Departamento" onchange="applyStoreFilters()" class="w-full p-2 input-custom rounded-lg text-xs outline-none text-body">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" id="store-prov" placeholder="Provincia" onchange="applyStoreFilters()" class="w-full p-2 input-custom rounded-lg text-xs outline-none text-body">
                        <input type="text" id="store-dist" placeholder="Distrito" onchange="applyStoreFilters()" class="w-full p-2 input-custom rounded-lg text-xs outline-none text-body">
                    </div>
                </div>

                <!-- Categoría -->
                <div class="mb-5 border-b border-custom pb-4">
                    <label class="block text-sm font-semibold text-body opacity-80 mb-2">Categoría</label>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <label class="flex items-center cursor-pointer text-sm text-body"><input type="radio" name="category" value="" checked onchange="applyStoreFilters()" class="mr-2 text-blue-500"> Todas</label>
                        <label class="flex items-center cursor-pointer text-sm text-body"><input type="radio" name="category" value="food" onchange="applyStoreFilters()" class="mr-2 text-blue-500"> Comida</label>
                        <label class="flex items-center cursor-pointer text-sm text-body"><input type="radio" name="category" value="toy" onchange="applyStoreFilters()" class="mr-2 text-blue-500"> Juguetes</label>
                        <label class="flex items-center cursor-pointer text-sm text-body"><input type="radio" name="category" value="accessory" onchange="applyStoreFilters()" class="mr-2 text-blue-500"> Accesorios</label>
                    </div>
                </div>

                <!-- SLIDER DE PRECIO DOBLE -->
                <div class="mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">Precio (S/)</label>
                    <div class="relative w-full h-8 mb-2">
                        <div class="absolute top-2 left-0 right-0 h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full"></div>
                        <!-- Rango: 0 a 500 Soles -->
                        <input type="range" id="min-price" min="0" max="500" value="0" step="5" 
                               class="absolute pointer-events-none w-full h-1.5 appearance-none bg-transparent z-20 top-2 [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-blue-500 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:appearance-none hover:[&::-webkit-slider-thumb]:scale-110 transition"
                               oninput="updatePriceSlider()">
                        <input type="range" id="max-price" min="0" max="500" value="500" step="5" 
                               class="absolute pointer-events-none w-full h-1.5 appearance-none bg-transparent z-20 top-2 [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-blue-500 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:appearance-none hover:[&::-webkit-slider-thumb]:scale-110 transition"
                               oninput="updatePriceSlider()">
                    </div>
                    <div class="flex justify-between text-xs font-bold text-gray-500 dark:text-gray-400">
                        <span id="label-min-price">S/ 0</span>
                        <span id="label-max-price">S/ 500+</span>
                    </div>
                </div>

                <!-- Orden -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">Orden</label>
                    <select id="store-sort" onchange="applyStoreFilters()" class="w-full p-2 input-custom rounded-lg text-sm outline-none bg-transparent text-body cursor-pointer">
                        <option value="newest" class="text-black">Lo más nuevo</option>
                        <option value="price_asc" class="text-black">Precio: Bajo a Alto</option>
                        <option value="price_desc" class="text-black">Precio: Alto a Bajo</option>
                    </select>
                </div>
            </div>
        </div>
    </aside>

    <!-- BODY -->
    <section class="w-full md:w-4/5">
        <h2 class="text-2xl font-bold text-body mb-6 ml-2">Tienda Solidaria</h2>
        
        <div id="products-grid-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @include('components.product-grid')
        </div>

        <div class="mt-8 px-2">
            {{ $products->links() }}
        </div>
    </section>
</div>

<script>
    let sliderTimeout;

    function updatePriceSlider() {
        const min = document.getElementById('min-price');
        const max = document.getElementById('max-price');
        
        if (parseInt(min.value) > parseInt(max.value)) {
            const temp = min.value; min.value = max.value; max.value = temp;
        }
        document.getElementById('label-min-price').innerText = 'S/ ' + min.value;
        document.getElementById('label-max-price').innerText = 'S/ ' + max.value + (max.value == 500 ? '+' : '');
        
        clearTimeout(sliderTimeout);
        sliderTimeout = setTimeout(applyStoreFilters, 600);
    }

    function toggleStoreFiltersMobile() {
        document.getElementById('store-filters').classList.toggle('hidden');
    }

    function applyStoreFilters() {
        const params = new URLSearchParams({
            search: document.getElementById('store-search').value,
            category: document.querySelector('input[name="category"]:checked').value,
            min_price: document.getElementById('min-price').value,
            max_price: document.getElementById('max-price').value,
            department: document.getElementById('store-dept').value,
            province: document.getElementById('store-prov').value,
            district: document.getElementById('store-dist').value,
            sort: document.getElementById('store-sort').value
        });
        const url = `{{ route('store') }}?${params.toString()}`;
        const grid = document.getElementById('products-grid-container');
        grid.style.opacity = '0.5';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(res => res.text()).then(html => {
            grid.innerHTML = html; grid.style.opacity = '1'; window.history.pushState({path: url}, '', url);
        });
    }
    
    function clearStoreFilters() {
        document.getElementById('store-search').value = '';
        document.querySelector('input[name="category"][value=""]').checked = true;
        document.getElementById('min-price').value = 0;
        document.getElementById('max-price').value = 500;
        document.getElementById('store-dept').value = '';
        document.getElementById('store-prov').value = '';
        document.getElementById('store-dist').value = '';
        document.getElementById('store-sort').value = 'newest';
        updatePriceSlider();
        applyStoreFilters();
    }
</script>
@endsection