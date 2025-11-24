@forelse($pets as $pet)
    <div class="bg-surface rounded-2xl shadow-sm hover:shadow-md transition duration-300 border border-custom overflow-hidden group relative">
        
        <div class="aspect-[3/2] w-full bg-gray-100 relative overflow-hidden cursor-pointer" 
             onclick="openModal('detail-pet-{{ $pet->id }}')">
            <img src="{{ $pet->image_url ?? 'https://via.placeholder.com/400' }}" 
                 alt="{{ $pet->name }}" 
                 class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
            
            <div class="absolute top-3 left-3 bg-surface/90 backdrop-blur-sm px-2 py-1 rounded-lg text-xs font-bold text-body shadow-sm flex items-center gap-1">
                <i class="fa-solid fa-home text-primary"></i> {{ $pet->user->name ?? 'Refugio' }}
            </div>
        </div>

        <div class="p-4">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-lg font-bold text-body truncate w-32">{{ $pet->name }}</h3>
                    <p class="text-xs text-body opacity-60 font-semibold uppercase tracking-wide">{{ $pet->breed }}</p>
                </div>
                <i class="fa-solid {{ $pet->type == 'dog' ? 'fa-dog' : 'fa-cat' }} text-body opacity-30 text-xl"></i>
            </div>

            <div class="flex items-center gap-2 text-xs text-body opacity-70 mt-3 pt-3 border-t border-custom">
                <span><i class="fa-regular fa-calendar mr-1"></i> {{ $pet->age }}</span>
                <span class="ml-auto"><i class="fa-solid fa-location-dot mr-1"></i> {{ $pet->user->district }}</span>
            </div>

            <div class="mt-4">
                @auth
                    <button onclick="openModal('adopt-modal-{{ $pet->id }}')" class="w-full border border-custom hover:border-primary text-primary font-bold py-2 rounded-lg transition text-sm hover:bg-surface shadow-sm">
                        Adoptar
                    </button>
                @else
                    <button onclick="openModal('login-modal')" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold py-2 rounded-lg transition text-sm dark:bg-gray-700 dark:text-gray-300">
                        Login
                    </button>
                @endauth
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- MODAL DETALLE MASCOTA --}}
    {{-- ================================================= --}}
    <div id="detail-pet-{{ $pet->id }}" class="hidden fixed inset-0 z-[60] flex items-center justify-center modal-backdrop p-4" 
         onclick="if(event.target === this) closeModal('detail-pet-{{ $pet->id }}')">
        
        <div class="bg-surface rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden relative flex flex-col md:flex-row animate-scale-in max-h-[90vh] md:max-h-auto overflow-y-auto border border-custom">
            
            <button onclick="closeModal('detail-pet-{{ $pet->id }}')" class="absolute top-4 right-4 z-10 bg-white/80 rounded-full p-2 text-gray-500 hover:text-red-500 transition shadow-sm">
                <i class="fa-solid fa-times text-xl"></i>
            </button>

            <div class="w-full md:w-1/2 h-64 md:h-auto relative bg-gray-100">
                <img src="{{ $pet->image_url }}" class="w-full h-full object-cover">
                <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/60 to-transparent p-6">
                    <h2 class="text-3xl font-extrabold text-white">{{ $pet->name }}</h2>
                    <p class="text-white/90 text-sm"><i class="fa-solid fa-location-dot mr-1"></i> {{ $pet->user->district }}, {{ $pet->user->department }}</p>
                </div>
            </div>

            <div class="w-full md:w-1/2 p-8 bg-surface">
                
                <div class="flex gap-4 mb-6">
                    <div class="text-center p-3 rounded-xl border border-custom flex-1 bg-gray-50 dark:bg-white/5">
                        <span class="block text-xs text-body opacity-60 uppercase">Edad</span>
                        <span class="font-bold text-body">{{ $pet->age }}</span>
                    </div>
                    <div class="text-center p-3 rounded-xl border border-custom flex-1 bg-gray-50 dark:bg-white/5">
                        <span class="block text-xs text-body opacity-60 uppercase">Sexo</span>
                        <span class="font-bold text-body">{{ $pet->type == 'dog' ? 'Perro' : 'Gato' }}</span>
                    </div>
                    <div class="text-center p-3 rounded-xl border border-custom flex-1 bg-gray-50 dark:bg-white/5">
                        <span class="block text-xs text-body opacity-60 uppercase">Raza</span>
                        <span class="font-bold text-body truncate">{{ $pet->breed }}</span>
                    </div>
                </div>

                <h3 class="font-bold text-body text-lg mb-2">Sobre mí</h3>
                <p class="text-body opacity-80 leading-relaxed mb-8 text-sm md:text-base">
                    {{ $pet->description }}
                </p>

                <div class="border-t border-custom pt-6">
                    <h4 class="text-xs font-bold text-body opacity-50 uppercase tracking-wider mb-4">Publicado por</h4>
                    <div class="flex items-center gap-4">
                        <img src="{{ $pet->user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($pet->user->name) }}" class="w-12 h-12 rounded-full object-cover border border-custom">
                        <div>
                            <p class="font-bold text-body">{{ $pet->user->name }}</p>
                            <p class="text-xs text-body opacity-60">{{ $pet->user->address ?? 'Dirección no pública' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    @auth
                         <button onclick="closeModal('detail-pet-{{ $pet->id }}'); openModal('adopt-modal-{{ $pet->id }}')" class="w-full btn-primary font-bold py-3 rounded-xl shadow-lg hover:opacity-90 transition">
                            <i class="fa-solid fa-heart mr-2"></i> Iniciar Adopción
                        </button>
                    @else
                        <button onclick="closeModal('detail-pet-{{ $pet->id }}'); openModal('login-modal')" class="w-full bg-gray-800 text-white font-bold py-3 rounded-xl hover:bg-gray-900 transition">
                            Inicia sesión para contactar
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FORMULARIO ADOPCIÓN --}}
    @auth
    <div id="adopt-modal-{{ $pet->id }}" class="hidden fixed inset-0 z-[70] flex items-center justify-center modal-backdrop p-4" onclick="if(event.target === this) closeModal('adopt-modal-{{ $pet->id }}')">
        <div class="bg-surface p-6 rounded-2xl max-w-sm w-full m-4 shadow-2xl relative border border-custom">
            <button onclick="closeModal('adopt-modal-{{ $pet->id }}')" class="absolute top-2 right-2 text-body opacity-50 hover:opacity-100"><i class="fa-solid fa-times"></i></button>
            <h3 class="text-xl font-bold mb-2 text-body">Adoptar a {{ $pet->name }}</h3>
            <p class="text-body opacity-70 text-sm mb-4">Se enviará una solicitud a <strong>{{ $pet->user->name }}</strong> con tus datos.</p>
            <form action="{{ route('adopt.store', $pet->id) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('adopt-modal-{{ $pet->id }}')" class="flex-1 py-2 border border-custom rounded-lg font-bold text-body opacity-70 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</button>
                    <button type="submit" class="flex-1 py-2 btn-primary text-white rounded-lg font-bold shadow-lg hover:opacity-90">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
    @endauth

@empty
    <div class="col-span-full text-center py-12 text-body opacity-50">
        <i class="fa-solid fa-search text-4xl mb-2"></i>
        <p>No se encontraron mascotas</p>
    </div>
@endforelse