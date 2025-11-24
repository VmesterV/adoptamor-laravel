// resources/js/app.js

// 1. INICIALIZACIÓN
let cart = JSON.parse(localStorage.getItem('adoptamor_cart')) || [];

document.addEventListener('DOMContentLoaded', () => {
    updateCartUI();
    
    // Leer mensajes de sesión
    const successMsg = document.querySelector('meta[name="session-success"]')?.content;
    const errorMsg = document.querySelector('meta[name="session-error"]')?.content;

    if(successMsg) showToast(successMsg, 'success');
    if(errorMsg) showToast(errorMsg, 'error');

    // Inicializar tema
    const html = document.documentElement;
    if (localStorage.getItem('theme') === 'dark') { 
        html.setAttribute('data-theme', 'dark'); 
        document.getElementById('theme-icon-sun')?.classList.add('hidden'); 
        document.getElementById('theme-icon-moon')?.classList.remove('hidden'); 
    }
});

// 2. LÓGICA DEL CARRITO
window.addToCart = function(product) { 
    const existing = cart.find(p => p.id === product.id);
    if (existing) { existing.qty++; } else { cart.push({ ...product, qty: 1 }); }
    saveCart();
    showToast(`¡${product.name} agregado al carrito!`, 'success');
}

window.removeFromCart = function(id) {
    cart = cart.filter(p => p.id !== id);
    saveCart();
}

window.clearCart = function() {
    if(cart.length > 0 && confirm('¿Vaciar carrito?')) {
        cart = [];
        saveCart();
    }
}

function saveCart() {
    localStorage.setItem('adoptamor_cart', JSON.stringify(cart));
    updateCartUI();
}

function updateCartUI() {
    const container = document.getElementById('cart-items');
    const badge = document.getElementById('cart-badge');
    const totalEl = document.getElementById('cart-total');
    if(!container) return;

    const totalQty = cart.reduce((acc, item) => acc + item.qty, 0);
    if(badge) { badge.innerText = totalQty; badge.classList.toggle('hidden', totalQty === 0); }

    if (cart.length === 0) {
        // ACTUALIZADO: Usa clases en lugar de style=""
        container.innerHTML = `<div class="bg-surface flex flex-col items-center justify-center h-full text-gray-400 opacity-60"><i class="fa-solid fa-basket-shopping text-5xl mb-3 text-secondary"></i><p>Tu carrito está vacío</p></div>`;
        if(totalEl) totalEl.innerText = 'S/ 0.00';
        return;
    }

    let html = ''; let total = 0;
    cart.forEach(item => {
        total += item.price * item.qty;
        // ACTUALIZADO: Usa clases en lugar de style=""
        html += `<div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-custom transition hover:shadow-sm">
            <img src="${item.image}" class="w-14 h-14 object-cover rounded-md border border-custom">
            <div class="flex-grow min-w-0"><p class="font-bold text-sm truncate text-gray-800 dark:text-gray-200">${item.name}</p><p class="text-xs text-gray-500">S/ ${item.price} x ${item.qty}</p></div>
            <div class="text-right"><div class="font-bold text-sm mb-1 text-primary">S/ ${(item.price * item.qty).toFixed(2)}</div><button onclick="removeFromCart(${item.id})" class="text-gray-400 hover:text-red-500 transition text-xs"><i class="fa-solid fa-trash-can"></i></button></div></div>`;
    });
    container.innerHTML = html;
    if(totalEl) totalEl.innerText = 'S/ ' + total.toFixed(2);
}

window.processCheckout = async function() {
    if (cart.length === 0) return alert('Tu carrito está vacío');
    const deliveryType = document.querySelector('input[name="delivery_type"]:checked').value;
    const addressInput = document.getElementById('shipping_address');
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

    if (deliveryType === 'delivery' && !addressInput.value.trim()) {
        alert('Por favor ingresa una dirección de envío.');
        addressInput.focus();
        return;
    }

    const btn = document.querySelector('#checkout-form button');
    btn.disabled = true;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';

    const checkoutUrl = document.querySelector('meta[name="checkout-url"]').content;

    try {
        const response = await fetch(checkoutUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ cart: cart, delivery_type: deliveryType, address: addressInput.value, payment_method: paymentMethod })
        });
        const result = await response.json();
        if (result.success) {
            cart = []; saveCart(); toggleCart();
            showToast('¡Compra exitosa! Gracias por tu apoyo.', 'success');
        } else {
            showToast('Error: ' + result.message, 'error');
        }
    } catch (error) {
        showToast('Hubo un error de conexión.', 'error');
        console.error(error);
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

// 3. UTILS UI
window.openModal = id => document.getElementById(id).classList.remove('hidden');
window.closeModal = id => document.getElementById(id).classList.add('hidden');

window.toggleCart = () => {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    sidebar.classList.toggle('translate-x-full');
    overlay.classList.toggle('hidden');
    updateCartUI();
}
window.toggleAddress = show => {
    document.getElementById('address-field').classList.toggle('hidden', !show);
}
window.toggleMobileMenu = () => {
    const drawer = document.getElementById('mobile-menu-drawer');
    const backdrop = document.getElementById('mobile-menu-backdrop');
    drawer.classList.toggle('-translate-x-full');
    backdrop.classList.toggle('opacity-0');
    backdrop.classList.toggle('pointer-events-none');
}

// 4. TOAST SYSTEM
window.showToast = (message, type = 'success') => {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    const bgClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? '<i class="fa-solid fa-check-circle"></i>' : '<i class="fa-solid fa-circle-exclamation"></i>';
    
    toast.className = `${bgClass} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 transform transition-all duration-300 translate-y-10 opacity-0 min-w-[300px]`;
    toast.innerHTML = `<div class="text-xl">${icon}</div><div class="font-bold text-sm">${message}</div>`;
    container.appendChild(toast);
    
    requestAnimationFrame(() => toast.classList.remove('translate-y-10', 'opacity-0'));
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// 5. AUTH UI
window.switchAuthView = viewName => {
    const container = document.getElementById('auth-container');
    ['login', 'roles', 'register'].forEach(v => document.getElementById('view-'+v).classList.add('hidden'));
    
    if (viewName === 'register') {
        container.classList.remove('max-w-sm'); container.classList.add('max-w-2xl');
    } else {
        container.classList.add('max-w-sm'); container.classList.remove('max-w-2xl');
    }
    document.getElementById('view-'+viewName).classList.remove('hidden');
}

window.selectRole = role => {
    const inputRole = document.getElementById('input-role');
    if(inputRole) inputRole.value = role;
    
    const icon = document.getElementById('role-icon-display');
    const title = document.getElementById('role-title-display');
    const addr = document.getElementById('address-fields');
    const inputs = ['input-dept', 'input-prov', 'input-dist', 'input-addr'];

    if (role === 'person') {
        icon.innerHTML = '<i class="fa-solid fa-user text-pink-500"></i>'; title.innerText = 'Cuenta Personal';
        addr.classList.add('hidden'); inputs.forEach(id => document.getElementById(id).removeAttribute('required'));
    } else {
        icon.innerHTML = role === 'shelter' ? '<i class="fa-solid fa-paw text-blue-500"></i>' : '<i class="fa-solid fa-store text-purple-500"></i>';
        title.innerText = role === 'shelter' ? 'Cuenta Refugio' : 'Cuenta Tienda';
        addr.classList.remove('hidden'); inputs.forEach(id => document.getElementById(id).setAttribute('required', 'true'));
    }
    switchAuthView('register');
}

window.toggleUserMenu = (e) => { e.stopPropagation(); document.getElementById('user-menu-dropdown').classList.toggle('hidden'); }
window.addEventListener('click', (e) => {
    const dd = document.getElementById('user-menu-dropdown');
    const btn = document.getElementById('user-menu-btn');
    if (dd && !dd.classList.contains('hidden') && !btn.contains(e.target) && !dd.contains(e.target)) dd.classList.add('hidden');
});

const themeToggle = document.getElementById('theme-toggle');
if(themeToggle) themeToggle.addEventListener('click', () => {
    const html = document.documentElement;
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next); 
    localStorage.setItem('theme', next);
    document.getElementById('theme-icon-sun').classList.toggle('hidden'); 
    document.getElementById('theme-icon-moon').classList.toggle('hidden');
});

let currentType = '';
let sliderTimeout;

// Inicialización de eventos específicos del Home
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    
    // Solo si existe el buscador (estamos en Home), agregamos el listener
    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') applyFilters();
        });
    }
});

// Actualizar el slider doble de edad
window.updateDualSlider = function() {
    const minRange = document.getElementById('age-min');
    const maxRange = document.getElementById('age-max');
    const labelMin = document.getElementById('label-min');
    const labelMax = document.getElementById('label-max');

    // Validación por si no estamos en la página correcta
    if (!minRange || !maxRange) return;

    if (parseInt(minRange.value) > parseInt(maxRange.value)) {
        const temp = minRange.value;
        minRange.value = maxRange.value;
        maxRange.value = temp;
    }
    labelMin.innerText = minRange.value == 0 ? 'Cachorro' : minRange.value + ' años';
    labelMax.innerText = maxRange.value + ' años';
    
    clearTimeout(sliderTimeout);
    sliderTimeout = setTimeout(applyFilters, 600);
}

// Filtrar por Perro/Gato
window.filterType = function(type) {
    currentType = type === currentType ? '' : type;
    document.querySelectorAll('.filter-btn').forEach(btn => {
        // Usamos las mismas clases de Tailwind que tenías
        if(btn.dataset.val === currentType) {
            btn.classList.add('bg-pink-100', 'border-pink-300', 'text-pink-600', 'dark:bg-pink-900', 'dark:border-pink-700', 'dark:text-pink-300');
        } else {
            btn.classList.remove('bg-pink-100', 'border-pink-300', 'text-pink-600', 'dark:bg-pink-900', 'dark:border-pink-700', 'dark:text-pink-300');
        }
    });
    applyFilters();
}

// Limpiar todos los filtros
window.clearFilters = function() {
    const search = document.getElementById('search-input');
    if(search) search.value = '';
    
    ['filter-dept', 'filter-prov', 'filter-dist'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.value = '';
    });

    const min = document.getElementById('age-min');
    const max = document.getElementById('age-max');
    if(min && max) { min.value = 0; max.value = 15; }

    const sort = document.getElementById('sort-select');
    if(sort) sort.value = 'newest';

    currentType = '';
    updateDualSlider(); // Actualiza visualmente el slider
    
    document.querySelectorAll('.filter-btn').forEach(btn => 
        btn.classList.remove('bg-pink-100', 'border-pink-300', 'text-pink-600', 'dark:bg-pink-900', 'dark:border-pink-700', 'dark:text-pink-300')
    );
    applyFilters();
}

window.toggleFiltersMobile = function() { 
    const container = document.getElementById('filters-container');
    if(container) container.classList.toggle('hidden'); 
}

// Función principal de filtrado (AJAX)
window.applyFilters = function() {
    // IMPORTANTE: Obtenemos la ruta desde el input hidden HTML
    const urlInput = document.getElementById('home-url');
    if(!urlInput) return; // Seguridad

    const params = new URLSearchParams({
        search: document.getElementById('search-input')?.value || '',
        type: currentType,
        sort: document.getElementById('sort-select')?.value || 'newest',
        department: document.getElementById('filter-dept')?.value || '',
        province: document.getElementById('filter-prov')?.value || '',
        district: document.getElementById('filter-dist')?.value || '',
        min_age: document.getElementById('age-min')?.value || 0,
        max_age: document.getElementById('age-max')?.value || 15
    });

    const url = `${urlInput.value}?${params.toString()}`;
    const grid = document.getElementById('pets-grid-container');
    
    if(grid) {
        grid.style.opacity = '0.5';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                grid.innerHTML = html; 
                grid.style.opacity = '1'; 
                window.history.pushState({path: url}, '', url);
            });
    }
}