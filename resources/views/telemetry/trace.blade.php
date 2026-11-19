@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('telemetry.dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">
            &larr; Retour a la console de diagnostic
        </a>
        <span class="text-xs font-mono bg-slate-100 text-slate-700 px-2.5 py-1 rounded">
            Trace Context W3C
        </span>
    </div>

    <!-- Resume de la Trace -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Identifiant Global de Trace (Trace ID)</span>
                <h1 class="text-lg font-mono font-bold text-emerald-800 break-all">{{ $traceId }}</h1>
            </div>
            @if($order)
                <div class="text-right">
                    <span class="text-xs text-slate-400 block">Statut Commande</span>
                    @if($order->status === 'completed')
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Succes ({{ $order->execution_time_ms }} ms)</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">Echec ({{ $order->execution_time_ms }} ms)</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Representation visuelle du flux distribué -->
        <div class="mt-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Parcours Distribué de la Requête (Accueil &rarr; Stock &rarr; Commandes)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                <div class="p-3 rounded-lg border border-sky-200 bg-sky-50">
                    <span class="font-bold text-sky-900 block mb-1">1. Service Accueil</span>
                    <span class="text-sky-700 block">Reception de la requete client, generation du trace_id et routage.</span>
                </div>
                <div class="p-3 rounded-lg border border-amber-200 bg-amber-50">
                    <span class="font-bold text-amber-900 block mb-1">2. Service Stock</span>
                    <span class="text-amber-700 block">Verification quantites disponibles et decrementation atomique.</span>
                </div>
                <div class="p-3 rounded-lg border border-purple-200 bg-purple-50">
                    <span class="font-bold text-purple-900 block mb-1">3. Service Commandes</span>
                    <span class="text-purple-700 block">Persistance dans PostgreSQL et aggregation des metriques de latence.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste chronologique des Spans / Logs JSON pour cette trace -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Logs et Événements Associés à cette Trace (Tempo / Loki)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Tous les logs enregistrés avec le Trace ID courant par les différents services.</p>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($logs as $idx => $l)
                <div class="p-4 hover:bg-slate-50 transition">
                    <div class="flex items-center justify-between text-xs mb-1.5 font-mono">
                        <div class="flex items-center space-x-2">
                            <span class="text-slate-400">#{{ $idx + 1 }}</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase
                                {{ $l->service === 'accueil' ? 'bg-sky-100 text-sky-800' : ($l->service === 'stock' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ $l->service }}
                            </span>
                            @if($l->level === 'error')
                                <span class="text-rose-600 font-bold uppercase">ERROR</span>
                            @elseif($l->level === 'warning')
                                <span class="text-amber-600 font-bold uppercase">WARN</span>
                            @else
                                <span class="text-slate-500 uppercase">INFO</span>
                            @endif
                            <span class="text-slate-500 font-sans font-medium">{{ $l->message }}</span>
                        </div>
                        <span class="text-slate-400 text-[11px]">{{ $l->created_at->format('H:i:s.u') }}</span>
                    </div>

                    <div class="mt-2 bg-slate-900 text-slate-200 p-3 rounded-lg font-mono text-[11px] overflow-x-auto">
                        <pre>{{ json_encode([
                            'timestamp' => $l->created_at->toISOString(),
                            'trace_id'  => $l->trace_id,
                            'span_id'   => $l->span_id,
                            'service'   => $l->service,
                            'level'     => $l->level,
                            'message'   => $l->message,
                            'context'   => $l->context ?? [],
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Aucun log enregistre pour cette trace.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
