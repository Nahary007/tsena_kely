@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- En-tete Service Stock -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Service Stock</h1>
            <p class="text-sm text-slate-500 mt-1">Dev 3 : Verification de quantite, decrementation atomique et emission de logs structures JSON avec trace_id.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                Service Actif
            </span>
        </div>
    </div>

    <!-- Indicateurs rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Articles totaux en stock</span>
            <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $totalItems }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider block">Stocks en alerte (<= 5)</span>
            <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ $lowStockCount }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider block">Ruptures de stock</span>
            <span class="text-2xl font-bold text-rose-600 mt-1 block">{{ $outOfStockCount }}</span>
        </div>
    </div>

    <!-- Tableau de controle du stock -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Inventaire et Niveaux de Stock</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4">Produit</th>
                        <th class="py-3 px-4">Categorie</th>
                        <th class="py-3 px-4">Prix</th>
                        <th class="py-3 px-4">Quantite dispo</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4 text-right">Reapprovisionnement rapide</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($products as $product)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $product->name }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $product->category }}</td>
                            <td class="py-3 px-4 font-mono text-slate-700">{{ number_format($product->price, 0, ',', ' ') }} Ar</td>
                            <td class="py-3 px-4 font-bold {{ $product->stock <= 0 ? 'text-rose-600' : ($product->stock <= 5 ? 'text-amber-600' : 'text-slate-900') }}">
                                {{ $product->stock }}
                            </td>
                            <td class="py-3 px-4">
                                @if($product->stock > 5)
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold">Normal</span>
                                @elseif($product->stock > 0)
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold">Critique</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-semibold">Rupture</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <form action="{{ route('stock.restock', $product->id) }}" method="POST" class="inline-flex items-center space-x-1">
                                    @csrf
                                    <input type="number" name="quantity" value="10" min="1" max="1000" class="w-16 border border-slate-300 rounded px-2 py-1 text-xs">
                                    <button type="submit" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded font-medium">Ajouter</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
