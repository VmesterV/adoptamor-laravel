@extends('layouts.app')

@section('content')
<input type="hidden" id="home-url" value="{{ route('home') }}">

<div class="flex flex-col md:flex-row gap-6 py-6">
    
    <aside class="w-full md:w-1/5 space-y-6 h-full"> 
        
        <div class="sticky top-24 z-30 bg-surface p-4 rounded-xl shadow-sm border border-custom transition-colors">
            <h3 class="font-bold mb-3 text-body">
                <i class="fa-solid fa-search mr-2 text-primary"></i>Buscar
            </h3>
            <input type="text" id="search-input" placeholder="Nombre, raza..." 
                   class="w-full p-2 rounded-lg focus:ring-2 focus:ring-pink-300 outline-none transition text-sm input-custom text-body">
        </div>

        <div class="bg-surface rounded-xl shadow-sm border border-custom overflow-hidden transition-colors">
            
            <div class="md:hidden p-4 bg-surface border-b border-custom flex justify-between items-center cursor-pointer" onclick="toggleFiltersMobile()">
                <h3 class="font-bold text-body">Filtros Avanzados</h3>
                <i id="filter-arrow" class="fa-solid fa-chevron-down text-gray-400"></i>
            </div>

            <div id="filters-container" class="hidden md:block p-4 transition-all">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="hidden md:block font-bold text-body">Filtros</h3>
                    <button onclick="clearFilters()" class="text-xs text-red-400 hover:text-red-500 font-bold">Limpiar</button>
                </div>

                <div class="mb-5 space-y-2 border-b border-custom pb-4">
                    <label class="block text-sm font-semibold text-body opacity-80">Ubicación</label>
                    <input type="text" id="filter-dept" placeholder="Departamento" 
                           value="{{ auth()->check() && !request('search') ? auth()->user()->department : '' }}"
                           onchange="applyFilters()"
                           class="w-full p-2 rounded-lg text-xs input-custom text-body focus:ring-1 focus:ring-pink-300 outline-none">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" id="filter-prov" placeholder="Provincia" onchange="applyFilters()" class="w-full p-2 rounded-lg text-xs input-custom text-body outline-none">
                        <input type="text" id="filter-dist" placeholder="Distrito" onchange="applyFilters()" class="w-full p-2 rounded-lg text-xs input-custom text-body outline-none">
                    </div>
                </div>

                <div class="mb-6 border-b border-custom pb-4">
                    <label class="block text-sm font-semibold text-body opacity-80 mb-4">Edad</label>
                    <div class="relative w-full h-8 mb-2">
                        <div class="absolute top-2 left-0 right-0 h-1.5 bg-gray-200 rounded-full opacity-50"></div>
                        
                        <input type="range" id="age-min" min="0" max="15" value="0" step="1" 
                               class="absolute pointer-events-none w-full h-1.5 appearance-none bg-transparent z-20 top-2 [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-pink-400 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:appearance-none hover:[&::-webkit-slider-thumb]:scale-110 transition"
                               oninput="updateDualSlider()">
                        <input type="range" id="age-max" min="0" max="15" value="15" step="1" 
                               class="absolute pointer-events-none w-full h-1.5 appearance-none bg-transparent z-20 top-2 [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-pink-400 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:appearance-none hover:[&::-webkit-slider-thumb]:scale-110 transition"
                               oninput="updateDualSlider()">
                    </div>
                    <div class="flex justify-between text-xs font-bold text-body opacity-70">
                        <span id="label-min">Cachorro</span>
                        <span id="label-max">15 años</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-body opacity-80 mb-2">Tipo</label>
                    <div class="flex gap-2">
                        <button onclick="filterType('dog')" class="filter-btn flex-1 py-2 border border-custom rounded-lg hover:opacity-80 text-sm transition text-body" data-val="dog"><i class="fa-solid fa-dog"></i></button>
                        <button onclick="filterType('cat')" class="filter-btn flex-1 py-2 border border-custom rounded-lg hover:opacity-80 text-sm transition text-body" data-val="cat"><i class="fa-solid fa-cat"></i></button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-body opacity-80 mb-2">Orden</label>
                    <select id="sort-select" onchange="applyFilters()" class="w-full p-2 border border-custom rounded-lg bg-transparent text-body text-sm outline-none cursor-pointer">
                        <option value="newest" class="text-black">Más Recientes</option>
                        <option value="oldest" class="text-black">Más Antiguos</option>
                    </select>
                </div>
            </div>
        </div>
    </aside>

    <section class="w-full md:w-4/5">
        <h2 class="text-2xl font-bold mb-6 ml-2 text-body">Amigos esperando un hogar</h2>
        
        <div id="pets-grid-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @include('components.pet-grid')
        </div>
        
        <div class="mt-8 px-2">
            {{ $pets->links() }}
        </div>
    </section>

</div>
@endsection