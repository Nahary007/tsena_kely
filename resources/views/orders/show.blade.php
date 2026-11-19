@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('orders.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">
            &larr; Retour aux commandes
        </a>
        <a href="{{ route('telemetry.trace', $order->trace_id) }}" class="text-xs bg-emerald-50 text-emerald-800 border border-emerald-200 px-3 py-1.5 rounded-lg font-medium hover:bg-emerald-100 transition">
            Examiner la trace OpenTelemetry
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Commande</span>
                <h1 class="text-xl font-bold font-mono text-slate-900">{{ $order->order_number }}</h1>
            </div>
            <div>
                @if($order->status === 'completed')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Succes</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">Echec</span>
                @endif
            </div>
        </div>

        @if($order->status === 'failed')
            <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <span class="font-bold">Motif de l'echec :</span> {{ $order->failure_reason }}
            </div>
        @endif

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">Client</span>
                <span class="font-semibold text-slate-800">{{ $order->customer_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Email / Tel</span>
                <span class="text-slate-700">{{ $order->customer_email ?: '-' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Date</span>
                <span class="text-slate-700">{{ $order->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Duree d'execution</span>
                <span class="font-mono text-slate-800">{{ $order->execution_time_ms }} ms</span>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Articles inclus</h3>
            <div class="divide-y divide-slate-100 border border-slate-200 rounded-lg overflow-hidden">
                @forelse($order->items as $item)
                    <div class="p-3 flex items-center justify-between text-xs bg-white">
                        <div>
                            <span class="font-semibold text-slate-800">{{ $item->product->name }}</span>
                            <span class="text-slate-400 ml-2">{{ number_format($item->unit_price, 0, ',', ' ') }} Ar x {{ $item->quantity }}</span>
                        </div>
                        <span class="font-mono font-bold text-slate-900">{{ number_format($item->total_price, 0, ',', ' ') }} Ar</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-slate-400">Aucun article valide enregistre.</div>
                @endforelse
            </div>
            <div class="mt-3 flex justify-between text-sm font-bold text-slate-900 px-1">
                <span>Total Facture</span>
                <span>{{ number_format($order->total_amount, 0, ',', ' ') }} Ar</span>
            </div>
        </div>
    </div>
</div>
@endsection
