@extends('layouts.app')

@section('content')
<div class="bg-surface transition-colors">
    
    <div class="relative bg-surface border-b border-custom py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-body sm:text-5xl md:text-6xl">
                <span class="block text-primary">AdoptAmor</span>
                <span class="block text-body opacity-60 text-3xl mt-2">Uniendo corazones, cambiando vidas</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-body opacity-80">
                Somos más que una plataforma; somos el puente entre el abandono y un hogar lleno de amor.
            </p>
        </div>
    </div>

    <div class="py-12 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                <div class="bg-surface p-8 rounded-2xl border border-custom text-center hover:shadow-lg transition duration-300">
                    <div class="bg-blue-100 dark:bg-blue-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-rocket text-2xl text-blue-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-body mb-4">Nuestra Misión</h3>
                    <p class="text-body opacity-70 leading-relaxed">
                        Facilitar el proceso de adopción de mascotas en Perú, conectando refugios responsables con familias amorosas a través de una plataforma tecnológica segura, transparente y solidaria, que además permite a los refugios autodesarrollarse mediante la venta de productos.
                    </p>
                </div>

                <div class="bg-surface p-8 rounded-2xl border border-custom text-center hover:shadow-lg transition duration-300">
                    <div class="bg-pink-100 dark:bg-pink-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-eye text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-body mb-4">Nuestra Visión</h3>
                    <p class="text-body opacity-70 leading-relaxed">
                        Ser la plataforma líder en bienestar animal en Latinoamérica, logrando que ningún animal rescatado pase más de un mes sin hogar y creando una cultura de adopción responsable y apoyo continuo a los albergues.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <div class="py-16 bg-gray-50 dark:bg-white/5 border-y border-custom">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-8 md:mb-0 md:pr-10">
                <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                     alt="Perros jugando" 
                     class="rounded-2xl shadow-xl w-full h-80 object-cover border border-custom">
            </div>
            <div class="md:w-1/2">
                <h2 class="text-3xl font-extrabold text-body mb-4">Nuestra Historia</h2>
                <div class="w-20 h-1 bg-primary mb-6 rounded-full"></div>
                <p class="text-body opacity-80 mb-4 text-lg">
                    AdoptAmor nació en 2025 con una idea simple: <strong>la tecnología puede salvar vidas</strong>.
                </p>
                <p class="text-body opacity-70 mb-4 leading-relaxed">
                    Todo comenzó cuando nos dimos cuenta de lo difícil que era encontrar información centralizada sobre adopciones en Perú. Los refugios hacían un trabajo titánico pero invisible.
                </p>
                <p class="text-body opacity-70 leading-relaxed">
                    Decidimos crear un espacio donde no solo pudieras adoptar, sino también ayudar comprando productos esenciales, creando así un círculo virtuoso de ayuda. Hoy, conectamos cientos de patitas con sus nuevos hogares cada mes.
                </p>
            </div>
        </div>
    </div>

    <div class="py-16 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-body">Ponte en contacto</h2>
                <p class="mt-4 text-body opacity-60">¿Tienes dudas sobre el proceso de adopción o quieres registrar tu refugio?</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-6 border border-transparent hover:border-custom rounded-xl transition">
                    <i class="fa-solid fa-envelope text-4xl text-primary mb-4"></i>
                    <h3 class="text-lg font-bold text-body">Correo Electrónico</h3>
                    <p class="text-body opacity-60 mb-2">Para consultas generales</p>
                    <a href="mailto:contacto@adoptamor.pe" class="text-primary font-bold hover:underline">contacto@adoptamor.com</a>
                </div>

                <div class="p-6 border border-transparent hover:border-custom rounded-xl transition">
                    <i class="fa-brands fa-whatsapp text-4xl text-green-500 mb-4"></i>
                    <h3 class="text-lg font-bold text-body">WhatsApp</h3>
                    <p class="text-body opacity-60 mb-2">Soporte rápido (9am - 6pm)</p>
                    <a href="#" class="text-green-500 font-bold hover:underline">+51 987 654 321</a>
                </div>

                <div class="p-6 border border-transparent hover:border-custom rounded-xl transition">
                    <i class="fa-solid fa-location-dot text-4xl text-blue-400 mb-4"></i>
                    <h3 class="text-lg font-bold text-body">Oficinas</h3>
                    <p class="text-body opacity-60">Cusco, Perú</p>
                    <p class="text-body opacity-40 text-sm mt-1">(Atención presencial solo con cita)</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection