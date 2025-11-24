<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <meta name="checkout-url" content="{{ route('checkout') }}">
    @if(session('toast_success'))
        <meta name="session-success" content="{{ session('toast_success') }}">
    @endif
    @if(session('toast_error'))
        <meta name="session-error" content="{{ session('toast_error') }}">
    @endif

    <title>AdoptAmor</title>
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen overflow-x-hidden">

    <header class="fixed w-full z-40 bg-surface shadow-sm border-b border-custom h-16 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="flex justify-between items-center h-full">
                
                <div class="flex items-center gap-3">
                    <button onclick="toggleMobileMenu()" class="md:hidden text-gray-600 hover:text-pink-500 focus:outline-none transition">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>

                    <a href="{{ route('home') }}" class="ajax-link flex items-center group">
                        <i class="fa-solid fa-paw text-2xl sm:text-3xl mr-2 text-primary"></i>
                        <span class="font-extrabold text-xl sm:text-2xl tracking-tight group-hover:opacity-80 transition">
                            Adopt<span class="text-primary">Amor</span>
                        </span>
                    </a>
                </div>

                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="font-bold hover:text-gray-400 transition text-body">Mascotas</a>
                    <a href="{{ route('store') }}" class="font-bold hover:text-gray-400 transition text-body">Tienda</a>
                    
                    @if(auth()->check() && in_array(auth()->user()->role, ['shelter', 'store', 'admin']))
                        <a href="{{ route('publish') }}" class="font-bold hover:text-gray-400 transition flex items-center gap-1 text-body">
                            <i class="fa-solid fa-list-check"></i> Gestión
                        </a>
                    @endif

                    <a href="{{ route('contact') }}" class="font-bold hover:text-gray-400 transition text-body">Contáctanos</a>
                </nav>

                <div class="flex items-center space-x-2 sm:space-x-4">
                    <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fa-solid fa-sun text-yellow-500" id="theme-icon-sun"></i>
                        <i class="fa-solid fa-moon text-blue-300 hidden" id="theme-icon-moon"></i>
                    </button>

                    <button id="cart-toggle-btn" onclick="toggleCart()" class="relative p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition">
                        <i class="fa-solid fa-shopping-cart text-xl"></i>
                        <span id="cart-badge" class="absolute top-0 right-0 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center hidden bg-secondary">0</span>
                    </button>

                    @auth
                        <div class="relative">
                            <button id="user-menu-btn" onclick="toggleUserMenu(event)" class="flex items-center space-x-2 focus:outline-none">
                                <span class="hidden lg:block font-bold text-sm">{{ Auth::user()->name }}</span>
                                <img src="{{ Auth::user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" 
                                     class="h-8 w-8 rounded-full border-2 object-cover border-primary">
                            </button>
                            
                            <div id="user-menu-dropdown" class="absolute right-0 mt-2 w-48 bg-surface rounded-md shadow-lg py-1 hidden border border-custom z-50">
                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Dashboard</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-red-500">
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <button onclick="openModal('login-modal')" class="px-4 py-1 rounded-full font-bold text-sm text-white hover:opacity-90 transition whitespace-nowrap btn-primary">
                            Ingresar
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <div>
        <div id="mobile-menu-backdrop" onclick="toggleMobileMenu()" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300"></div>
        <div id="mobile-menu-drawer" class="fixed inset-y-0 left-0 w-64 bg-surface shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 flex flex-col border-r border-custom">
            <div class="p-4 border-b border-custom flex justify-between items-center">
                <span class="font-bold text-lg text-primary">Menú</span>
                <button onclick="toggleMobileMenu()" class="text-gray-500 hover:text-red-500 text-xl focus:outline-none">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <nav class="flex-grow p-4 space-y-4">
                <a href="{{ route('home') }}" class="block font-bold text-lg hover:text-pink-500 transition border-b border-custom pb-2 text-body">Mascotas</a>
                <a href="{{ route('store') }}" class="block font-bold text-lg hover:text-pink-500 transition border-b border-custom pb-2 text-body">Tienda</a>
                @if(auth()->check() && in_array(auth()->user()->role, ['shelter', 'store', 'admin']))
                    <a href="{{ route('publish') }}" class="block font-bold text-lg hover:text-pink-500 transition border-b border-custom pb-2 text-body">Gestión</a>
                @endif
                <a href="{{ route('contact') }}" class="block font-bold text-lg hover:text-pink-500 transition border-b border-custom pb-2 text-body">Contáctanos</a>
            </nav>
        </div>
    </div>

    <main id="main-content" class="flex-grow pt-20 px-4 sm:px-6 lg:px-8 w-full max-w-[95%] mx-auto transition-all">
        @yield('content')
    </main>
    
    <div id="cart-overlay" onclick="toggleCart()" class="fixed inset-0 bg-black/30 z-40 hidden transition-opacity duration-300"></div>

    <div id="cart-sidebar" class="fixed inset-y-0 right-0 w-full sm:w-96 bg-surface shadow-2xl transform translate-x-full transition-transform duration-300 z-50 border-l border-custom flex flex-col">
        
        <div class="p-4 border-b border-custom flex justify-between items-center bg-main">
            <h2 class="text-lg font-bold flex items-center">
                <i class="fa-solid fa-bag-shopping mr-2 text-primary"></i> Tu Carrito
            </h2>
            <div class="flex items-center gap-3">
                <button onclick="clearCart()" class="text-xs font-bold underline hover:no-underline text-gray-500 hover:text-red-500">Vaciar</button>
                <button onclick="toggleCart()" class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-times text-xl"></i></button>
            </div>
        </div>

        <div id="cart-items" class=" bg-surface flex-grow overflow-y-auto p-4 space-y-4"></div>

        <div class="p-5 bg-surface border-t border-custom shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
            @auth
                <div id="checkout-form" class="space-y-4 text-sm ">
                    <div>
                        <label class="font-bold block mb-2 text-body">Método de Entrega</label>
                        <div class="flex gap-2">
                            <label class="flex items-center border border-custom px-3 py-2 rounded-lg cursor-pointer w-1/2 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <input type="radio" name="delivery_type" value="pickup" checked onchange="toggleAddress(false)" class="mr-2 focus:ring-0"> Recojo
                            </label>
                            <label class="flex items-center border border-custom px-3 py-2 rounded-lg cursor-pointer w-1/2 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <input type="radio" name="delivery_type" value="delivery" onchange="toggleAddress(true)" class="mr-2 focus:ring-0"> Delivery
                            </label>
                        </div>
                    </div>  
                    <div id="address-field" class="hidden">
                        <input type="text" id="shipping_address" placeholder="Dirección exacta..." class="w-full border border-custom rounded-lg p-2 bg-transparent focus:ring-1 focus:ring-pink-300 outline-none">
                    </div>
                    <div>
                        <label class="font-bold block mb-2 text-body">Método de Pago</label>
                        <div class="flex gap-2">
                            <label class="flex items-center border border-custom px-3 py-2 rounded-lg cursor-pointer w-1/2 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <input type="radio" name="payment_method" value="card" checked class="mr-2 focus:ring-0"> Tarjeta
                            </label>
                            <label class="flex items-center border border-custom px-3 py-2 rounded-lg cursor-pointer w-1/2 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <input type="radio" name="payment_method" value="yape" class="mr-2 focus:ring-0"> Yape
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2 border-t border-dashed border-custom mt-2">
                        <span>Total:</span>
                        <span id="cart-total" class="text-primary">S/ 0.00</span>
                    </div>
                    <button onclick="processCheckout()" class="w-full text-white font-bold py-3 rounded-xl hover:opacity-90 transition shadow-lg flex justify-center items-center btn-primary">
                        <i class="fa-solid fa-credit-card mr-2"></i> Pagar Ahora
                    </button>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-gray-500 mb-2">Inicia sesión para finalizar tu compra</p>
                    <button onclick="openModal('login-modal')" class="w-full bg-gray-800 text-white font-bold py-2 rounded-lg hover:bg-gray-900 transition">Ingresar</button>
                </div>
            @endauth
        </div>
    </div>

    <div id="login-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop p-4">
        <div id="auth-container" class="bg-surface rounded-2xl shadow-2xl w-full max-w-sm relative border border-custom transition-all duration-500 overflow-hidden">
            <button onclick="closeModal('login-modal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition z-10"><i class="fa-solid fa-times text-xl"></i></button>
            
            <div id="view-login" class="p-8 animate-fade-in">
                <h2 class="text-2xl font-bold mb-6 text-center text-primary">Bienvenido</h2>
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold mb-1">Email</label>
                        <input type="email" name="email" required class="w-full p-2 border border-custom rounded-lg focus:ring-2 outline-none transition input-custom">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Contraseña</label>
                        <input type="password" name="password" required class="w-full p-2 border border-custom rounded-lg focus:ring-2 outline-none transition input-custom">
                    </div>
                    <button type="submit" class="w-full text-white font-bold py-2 rounded-lg hover:opacity-90 transition shadow-md btn-primary">Iniciar Sesión</button>
                </form>
                <div class="mt-6 pt-4 border-t border-custom text-center">
                    <p class="text-xs text-gray-400 mb-2">¿Aún no tienes cuenta?</p>
                    <button onclick="switchAuthView('roles')" class="text-sm font-bold hover:underline text-secondary">Crear Cuenta Nueva</button>
                </div>
            </div>

            <div id="view-roles" class="hidden p-8 animate-fade-in">
                <h2 class="text-xl font-bold mb-4 text-center text-gray-700">Elige tu perfil</h2>
                <div class="space-y-3">
                    <div onclick="selectRole('person')" class="cursor-pointer border border-custom rounded-xl p-4 hover:bg-gray-50 transition group hover:border-pink-300 relative overflow-hidden">
                        <div class="flex items-center gap-3">
                            <div class="bg-pink-50 p-2 rounded-full text-pink-500"><i class="fa-solid fa-user"></i></div>
                            <div><h3 class="font-bold text-sm text-gray-800">Persona</h3><p class="text-[10px] text-gray-500">Adoptar y comprar.</p></div>
                        </div>
                    </div>
                    <div onclick="selectRole('shelter')" class="cursor-pointer border border-custom rounded-xl p-4 hover:bg-gray-50 transition group hover:border-blue-300 relative overflow-hidden">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-50 p-2 rounded-full text-blue-500"><i class="fa-solid fa-paw"></i></div>
                            <div><h3 class="font-bold text-sm text-gray-800">Refugio</h3><p class="text-[10px] text-gray-500">Publicar mascotas.</p></div>
                        </div>
                    </div>
                    <div onclick="selectRole('store')" class="cursor-pointer border border-custom rounded-xl p-4 hover:bg-gray-50 transition group hover:border-purple-300 relative overflow-hidden">
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-50 p-2 rounded-full text-purple-500"><i class="fa-solid fa-store"></i></div>
                            <div><h3 class="font-bold text-sm text-gray-800">Tienda</h3><p class="text-[10px] text-gray-500">Vender productos.</p></div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 text-center"><button onclick="switchAuthView('login')" class="text-xs text-gray-400 hover:text-gray-600">Volver al login</button></div>
            </div>

            <div id="view-register" class="hidden p-0 animate-fade-in flex flex-col md:flex-row h-full">
                <div class="hidden md:flex w-1/3 bg-gray-50 p-6 flex-col items-center justify-center text-center border-r border-custom">
                    <div id="role-icon-display" class="w-16 h-16 rounded-full flex items-center justify-center text-3xl mb-4 bg-white shadow-sm"></div>
                    <h3 id="role-title-display" class="font-bold text-lg text-gray-800 mb-2"></h3>
                    <button onclick="switchAuthView('roles')" class="mt-8 text-xs text-blue-500 underline">Cambiar Rol</button>
                </div>
                <div class="w-full md:w-2/3 p-8">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Crear Cuenta</h2>
                    <form action="{{ route('register') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="role" id="input-role">
                        <div class="grid grid-cols-1 gap-3">
                            <input type="text" name="name" placeholder="Nombre completo" required class="w-full p-2 text-sm border border-custom rounded-lg bg-transparent focus:ring-1 outline-none">
                            <input type="email" name="email" placeholder="Correo" required class="w-full p-2 text-sm border border-custom rounded-lg bg-transparent focus:ring-1 outline-none">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="password" name="password" placeholder="Contraseña" required class="w-full p-2 text-sm border border-custom rounded-lg bg-transparent focus:ring-1 outline-none">
                                <input type="password" name="password_confirmation" placeholder="Confirmar" required class="w-full p-2 text-sm border border-custom rounded-lg bg-transparent focus:ring-1 outline-none">
                            </div>
                        </div>
                        <div id="address-fields" class="hidden space-y-3 pt-2 border-t border-custom mt-2">
                            <p class="text-xs font-bold text-gray-500 uppercase">Ubicación</p>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="department" id="input-dept" placeholder="Dpto" class="w-full p-2 text-sm border border-custom rounded-lg">
                                <input type="text" name="province" id="input-prov" placeholder="Prov" class="w-full p-2 text-sm border border-custom rounded-lg">
                            </div>
                            <input type="text" name="district" id="input-dist" placeholder="Distrito" class="w-full p-2 text-sm border border-custom rounded-lg">
                            <input type="text" name="address" id="input-addr" placeholder="Dirección" class="w-full p-2 text-sm border border-custom rounded-lg">
                        </div>
                        <button type="submit" class="w-full text-white font-bold py-2 rounded-lg hover:opacity-90 transition shadow-md mt-4 btn-primary">Registrarse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    @stack('scripts')
</body>
</html>