@forelse($products as $product)
    <div class="bg-surface rounded-xl shadow-sm hover:shadow-lg transition duration-300 border border-custom overflow-hidden group flex flex-col h-full relative">
        
        <div class="aspect-[3/2] w-full bg-gray-100 relative overflow-hidden cursor-pointer" 
             onclick="openModal('detail-prod-{{ $product->id }}')">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                 class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
            
            <span class="absolute top-2 right-2 bg-black/60 text-white text-[10px] font-bold px-2 py-1 rounded backdrop-blur-sm">
                {{ $product->user->name ?? 'Tienda' }}
            </span>
        </div>

        <div class="p-4 flex flex-col flex-grow">
            <span class="text-xs font-bold text-primary uppercase tracking-wider mb-1">
                {{ $product->category }}
            </span>
            
            <h3 class="font-bold text-body text-sm md:text-base leading-tight mb-2 line-clamp-2">
                {{ $product->name }}
            </h3>

            <div class="mt-auto pt-3 flex items-center justify-between border-t border-custom">
                <span class="text-lg font-bold text-primary">S/ {{ number_format($product->price, 2) }}</span>
                
                <button onclick="addToCart({ id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', price: {{ $product->price }}, image: '{{ $product->image_url }}' })" 
                        class="bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-600 hover:text-white h-10 w-10 rounded-full flex items-center justify-center transition shadow-sm">
                    <i class="fa-solid fa-cart-plus"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL DETALLE RÁPIDO --}}
    <div id="detail-prod-{{ $product->id }}" class="hidden fixed inset-0 z-[60] flex items-center justify-center modal-backdrop p-4" 
         onclick="if(event.target === this) closeModal('detail-prod-{{ $product->id }}')">
        
        <div class="bg-surface rounded-2xl shadow-2xl w-full max-w-2xl relative flex flex-col md:flex-row overflow-hidden border border-custom animate-scale-in">
            
            <button onclick="closeModal('detail-prod-{{ $product->id }}')" class="absolute top-3 right-3 z-10 text-body opacity-60 hover:text-red-500 transition bg-surface rounded-full p-1 shadow-sm">
                <i class="fa-solid fa-times text-lg"></i>
            </button>

            <div class="w-full md:w-1/2 h-64 md:h-auto bg-gray-100 relative">
                <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
            </div>

            <div class="w-full md:w-1/2 p-6 flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold text-primary uppercase tracking-wide">{{ $product->category }}</span>
                    <h2 class="text-2xl font-bold text-body mb-2 leading-tight">{{ $product->name }}</h2>
                    <p class="text-3xl font-bold text-primary mb-4">S/ {{ number_format($product->price, 2) }}</p>
                    
                    <p class="text-sm text-body opacity-70 mb-4 line-clamp-4">
                        {{ $product->description }}
                    </p>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-custom mt-4">
                    <img src="{{ $product->user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->user->name) }}" class="w-10 h-10 rounded-full border border-custom object-cover">
                    <div>
                        <p class="text-xs text-body opacity-50 font-bold uppercase">Vendido por</p>
                        <p class="text-sm font-bold text-body">{{ $product->user->name }}</p>
                        <p class="text-xs text-body opacity-60">{{ $product->user->department }}, {{ $product->user->district }}</p>
                    </div>
                </div>

                <button onclick="addToCart({
                            id: {{ $product->id }}, 
                            name: '{{ addslashes($product->name) }}', 
                            price: {{ $product->price }}, 
                            image: '{{ $product->image_url ?? '' }}',
                            seller: '{{ addslashes($product->user->name ?? '') }}'
                        }); closeModal('detail-prod-{{ $product->id }}')" 
                        class="mt-6 w-full btn-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition shadow-lg flex items-center justify-center gap-2">
                    <i class="fa-solid fa-cart-plus"></i> Agregar al Carrito
                </button>
            </div>
        </div>
    </div>
@empty
    <div class="col-span-full text-center py-12 text-body opacity-50">
        <i class="fa-solid fa-store text-4xl mb-2 text-gray-300"></i>
        <p>No se encontraron productos</p>
    </div>
@endforelse