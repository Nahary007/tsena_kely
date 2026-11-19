@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        @if($order->status === 'completed')
            <div class="text-center pb-6 border-b border-slate-100">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 mb-2">
                    Commande Reussie
                </span>
                <h1 class="text-2xl font-bold text-slate-900">Merci pour votre commande</h1>
                <p class="text-sm text-slate-500 mt-1">Votre commande a ete enregistree avec succes dans PostgreSQL.</p>
            </div>
        @else
            <div class="text-center pb-6 border-b border-slate-100">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 mb-2">
                    Echec de Traitement
                </span>
                <h1 class="text-2xl font-bold text-slate-900">La commande n'a pas pu aboutir</h1>
                <p class="text-sm text-rose-600 mt-1 font-medium">{{ $order->failure_reason }}</p>
            </div>
        @endif

        <!-- Informations d observabilite et traces W3C -->
        <div class="mt-6 bg-slate-50 border border-slate-200 rounded-lg p-4 space-y-3">
            <div class="flex items-center justify-between text-xs">
                <span class="font-semibold text-slate-600">Numero de commande</span>
                <span class="font-mono font-bold text-slate-900">{{ $order->order_number }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="font-semibold text-slate-600">Client</span>
                <span class="text-slate-900">{{ $order->customer_name }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="font-semibold text-slate-600">Duree de traitement (latence)</span>
                <span class="font-mono text-slate-900">{{ $order->execution_time_ms }} ms</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="font-semibold text-slate-600">Identifiant de Trace (OpenTelemetry)</span>
                <a href="{{ route('telemetry.trace', $order->trace_id) }}" class="font-mono text-emerald-600 hover:text-emerald-800 underline truncate max-w-xs">
                    {{ $order->trace_id }}
                </a>
            </div>
        </div>

        @if($order->items->isNotEmpty())
            <div class="mt-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Articles commandes</h3>
                <div class="divide-y divide-slate-100 border-t border-b border-slate-100">
                    @foreach($order->items as $item)
                        <div class="py-2.5 flex justify-between items-center text-xs">
                            <div>
                                <span class="font-medium text-slate-800">{{ $item->product->name }}</span>
                                <span class="text-slate-400 ml-1">x {{ $item->quantity }}</span>
                            </div>
                            <span class="font-mono font-semibold text-slate-900">{{ number_format($item->total_price, 0, ',', ' ') }} Ar</span>
                        </div>
                    @endforeach
                </div>
                <div class="pt-3 flex justify-between text-sm font-bold text-slate-900">
                    <span>Total facture</span>
                    <span>{{ number_format($order->total_amount, 0, ',', ' ') }} Ar</span>
                </div>
            </div>
        @endif

        <div class="mt-8 flex justify-center space-x-3">
            <a href="{{ route('shop.index') }}" onclick="clearCart()" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium">
                Retour a la boutique
            </a>
            <a href="{{ route('telemetry.trace', $order->trace_id) }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-medium">
                Examiner la trace & les logs
            </a>
        </div>
    </div>
</div>
@endsection
