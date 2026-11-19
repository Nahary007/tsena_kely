@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- En-tete vitrine Accueil -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Boutique Tsena Kely</h1>
            <p class="text-sm text-slate-500 mt-1">Service Accueil : Point d'entree recevant les requetes d'achats et coordonnant Stock et Commandes.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                Service Accueil En Ligne
            </span>
        </div>
    </div>

    <!-- Barre de filtrage et recherche -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <a href="{{ route('shop.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ !request('category') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Tous</a>
            @foreach($categories as $cat)
                <a href="{{ route('shop.index', ['category' => $cat]) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ request('category') === $cat ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
        <form action="{{ route('shop.index') }}" method="GET" class="w-full sm:w-64">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit..." class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-500">
        </form>
    </div>

    <!-- Grille des articles -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($products as $product)
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600">{{ $product->category }}</span>
                        @if($product->stock > 5)
                            <span class="text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">En stock ({{ $product->stock }})</span>
                        @elseif($product->stock > 0)
                            <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Stock faible ({{ $product->stock }})</span>
                        @else
                            <span class="text-xs font-medium text-rose-700 bg-rose-50 px-2 py-0.5 rounded">Rupture (0)</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">{{ $product->name }}</h3>
                    <p class="text-xs text-slate-500 mb-4">{{ $product->description }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 block">Prix unitaire</span>
                        <span class="text-base font-bold text-slate-900">{{ number_format($product->price, 0, ',', ' ') }} Ar</span>
                    </div>
                    <button type="button" 
                            onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }})"
                            {{ $product->stock <= 0 ? 'disabled' : '' }}
                            class="px-3 py-1.5 text-xs font-medium rounded-lg {{ $product->stock > 0 ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                        {{ $product->stock > 0 ? 'Ajouter' : 'Epuise' }}
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-400 text-sm">
                Aucun produit trouve.
            </div>
        @endforelse
    </div>
</div>
@endsection
