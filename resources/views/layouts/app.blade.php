<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Tsena Kely') }} - Mini Boutique en Ligne</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col font-sans">
    <!-- Barre de navigation principale -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="{{ route('shop.index') }}" class="flex items-center space-x-2">
                    <span class="text-xl font-bold tracking-tight text-emerald-700">TSENA KELY</span>
                    <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2 py-0.5 rounded">Observabilite P20</span>
                </a>
                <nav class="hidden md:flex space-x-1 text-sm font-medium">
                    <a href="{{ route('shop.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('shop.*') ? 'bg-slate-100 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">Boutique</a>
                    <a href="{{ route('stock.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('stock.*') ? 'bg-slate-100 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">Service Stock</a>
                    <a href="{{ route('orders.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('orders.*') ? 'bg-slate-100 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">Service Commandes</a>
                    <a href="{{ route('telemetry.dashboard') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('telemetry.*') ? 'bg-slate-100 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">Diagnostic & Pannes</a>
                </nav>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('telemetry.metrics') }}" target="_blank" class="hidden sm:inline-flex text-xs border border-slate-300 text-slate-700 hover:bg-slate-100 px-2.5 py-1.5 rounded font-mono">
                    /metrics (Prometheus)
                </a>
                <button id="openCartBtn" type="button" class="relative inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-3.5 py-1.5 rounded-lg transition">
                    <span>Panier</span>
                    <span id="cartCountBadge" class="ml-2 bg-emerald-800 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Notifications flash -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Modal Panier et Validation -->
    <div id="cartModal" class="fixed inset-0 bg-slate-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Votre Panier</h3>
                <button type="button" id="closeCartBtn" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>
            <div class="p-6">
                <div id="cartEmptyState" class="text-center py-6 text-slate-500 text-sm">
                    Votre panier est vide.
                </div>
                <div id="cartItemsList" class="space-y-3 max-h-60 overflow-y-auto mb-4 divide-y divide-slate-100">
                    <!-- Items injectes dynamiquement -->
                </div>
                <div id="cartSummary" class="border-t border-slate-200 pt-3 flex justify-between font-semibold text-slate-900 mb-4 hidden">
                    <span>Total a payer</span>
                    <span id="cartTotalText">0 Ar</span>
                </div>

                <form id="checkoutForm" action="{{ route('shop.checkout') }}" method="POST" class="space-y-3 hidden">
                    @csrf
                    <input type="hidden" name="cart_items" id="cartItemsInput">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Nom du client *</label>
                        <input type="text" name="customer_name" required placeholder="Ex: Rabe Jean" class="w-full text-sm border border-slate-300 rounded px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" name="customer_email" placeholder="rabe@test.mg" class="w-full text-sm border border-slate-300 rounded px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Telephone</label>
                            <input type="text" name="customer_phone" placeholder="0340000000" class="w-full text-sm border border-slate-300 rounded px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>
                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" id="cancelCartBtn" class="px-3 py-1.5 rounded text-sm text-slate-600 hover:bg-slate-100">Fermer</button>
                        <button type="submit" class="px-4 py-1.5 rounded text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-medium">Confirmer la commande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pied de page -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-6 text-center text-xs text-slate-500">
        <p>Tsena Kely - Architecture 3 services (Accueil, Stock, Commandes) - PostgreSQL - OpenTelemetry</p>
    </footer>

    <!-- Script Panier Global -->
    <script>
        let cart = JSON.parse(localStorage.getItem('tsena_cart') || '[]');

        function updateCartUI() {
            const badge = document.getElementById('cartCountBadge');
            const totalCount = cart.reduce((acc, item) => acc + item.qty, 0);
            if (badge) badge.innerText = totalCount;

            const emptyState = document.getElementById('cartEmptyState');
            const itemsList = document.getElementById('cartItemsList');
            const summary = document.getElementById('cartSummary');
            const form = document.getElementById('checkoutForm');
            const totalText = document.getElementById('cartTotalText');
            const itemsInput = document.getElementById('cartItemsInput');

            if (!itemsList) return;

            itemsList.innerHTML = '';
            if (cart.length === 0) {
                emptyState.classList.remove('hidden');
                summary.classList.add('hidden');
                form.classList.add('hidden');
            } else {
                emptyState.classList.add('hidden');
                summary.classList.remove('hidden');
                form.classList.remove('hidden');

                let total = 0;
                cart.forEach((item, index) => {
                    const subtotal = item.price * item.qty;
                    total += subtotal;

                    const row = document.createElement('div');
                    row.className = 'pt-2 flex items-center justify-between text-sm';
                    row.innerHTML = `
                        <div>
                            <div class="font-medium text-slate-800">${item.name}</div>
                            <div class="text-xs text-slate-500">${item.price.toLocaleString('fr-FR')} Ar x ${item.qty}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold text-slate-900">${subtotal.toLocaleString('fr-FR')} Ar</span>
                            <button type="button" onclick="removeFromCart(${index})" class="text-rose-500 hover:text-rose-700 text-xs font-semibold px-1">&times;</button>
                        </div>
                    `;
                    itemsList.appendChild(row);
                });

                totalText.innerText = total.toLocaleString('fr-FR') + ' Ar';
                itemsInput.value = JSON.stringify(cart);
            }

            localStorage.setItem('tsena_cart', JSON.stringify(cart));
        }

        function addToCart(id, name, price, maxStock) {
            const existing = cart.find(i => i.id === id);
            if (existing) {
                if (existing.qty < maxStock) {
                    existing.qty += 1;
                } else {
                    alert('Stock maximum disponible atteint (' + maxStock + ')');
                    return;
                }
            } else {
                if (maxStock < 1) {
                    alert('Produit en rupture de stock');
                    return;
                }
                cart.push({ id, name, price, qty: 1 });
            }
            updateCartUI();
            openCartModal();
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartUI();
        }

        function clearCart() {
            cart = [];
            updateCartUI();
        }

        const modal = document.getElementById('cartModal');
        function openCartModal() {
            if (modal) modal.classList.remove('hidden');
        }
        function closeCartModal() {
            if (modal) modal.classList.add('hidden');
        }

        document.getElementById('openCartBtn')?.addEventListener('click', openCartModal);
        document.getElementById('closeCartBtn')?.addEventListener('click', closeCartModal);
        document.getElementById('cancelCartBtn')?.addEventListener('click', closeCartModal);

        document.addEventListener('DOMContentLoaded', updateCartUI);
    </script>
</body>
</html>
