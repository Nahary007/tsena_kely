@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- En-tete observabilite -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Console d'Observabilite & Diagnostic (P20)</h1>
            <p class="text-sm text-slate-500 mt-1">Supervision globale, injection d'anomalies (Chaos Testing) et exploration des logs structures JSON.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('telemetry.metrics') }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-mono bg-slate-900 hover:bg-slate-800 text-white font-medium">
                Scraping Prometheus (/metrics)
            </a>
        </div>
    </div>

    <!-- Metriques cles -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Commandes</span>
            <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $totalOrders }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block">Succes</span>
            <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ $successfulOrders }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider block">Echecs</span>
            <span class="text-2xl font-bold text-rose-600 mt-1 block">{{ $failedOrders }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider block">Latence Moyenne</span>
            <span class="text-2xl font-bold text-blue-600 mt-1 block">{{ $avgLatency }} ms</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Interrupteurs de pannes (Dev 1) -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h2 class="text-base font-bold text-slate-900">Interrupteurs de Pannes (Demo)</h2>
                <span class="text-xs text-slate-400 font-medium">Chaos Testing</span>
            </div>

            <form action="{{ route('telemetry.settings') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Latence artificielle injectee (ms)</label>
                    <div class="flex items-center space-x-3">
                        <input type="range" name="chaos_latency_ms" min="0" max="3000" step="100" value="{{ $settings['chaos_latency_ms'] }}" oninput="document.getElementById('latVal').innerText = this.value + ' ms'" class="w-full h-2 bg-slate-200 rounded-lg cursor-pointer">
                        <span id="latVal" class="text-xs font-mono font-bold text-slate-700 w-16 text-right">{{ $settings['chaos_latency_ms'] }} ms</span>
                    </div>
                    <span class="text-[11px] text-slate-400">Simule des ralentissements reseau ou requetes SQL lentes.</span>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Taux d'erreurs aleatoires (HTTP 500)</label>
                    <div class="flex items-center space-x-3">
                        <input type="range" name="chaos_error_rate" min="0" max="100" step="5" value="{{ $settings['chaos_error_rate'] }}" oninput="document.getElementById('errVal').innerText = this.value + ' %'" class="w-full h-2 bg-slate-200 rounded-lg cursor-pointer">
                        <span id="errVal" class="text-xs font-mono font-bold text-slate-700 w-16 text-right">{{ $settings['chaos_error_rate'] }} %</span>
                    </div>
                    <span class="text-[11px] text-slate-400">Provoque des exceptions internes aleatoires.</span>
                </div>

                <div class="pt-2 border-t border-slate-100 space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="chaos_stock_down" value="1" {{ $settings['chaos_stock_down'] ? 'checked' : '' }} class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                        <span class="text-xs font-semibold text-slate-800">Couper le service Stock (Code 503 simule)</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="chaos_orders_down" value="1" {{ $settings['chaos_orders_down'] ? 'checked' : '' }} class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                        <span class="text-xs font-semibold text-slate-800">Couper le service Commandes (Code 503 simule)</span>
                    </label>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-medium transition">
                        Appliquer les reglages de panne
                    </button>
                </div>
            </form>
        </div>

        <!-- Simulateur de trafic continu (Dev 1) -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h2 class="text-base font-bold text-slate-900">Simulateur de Trafic Utilisateur</h2>
                    <span class="text-xs text-slate-400 font-medium">Injection continue</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed mb-4">
                    Genere des requetes d'achats simulees pour peupler les dashboards Grafana, mesurer les latences et analyser les comportements lors de pannes.
                </p>

                <form action="{{ route('telemetry.simulate') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Nombre de requetes a generer</label>
                        <select name="count" class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 bg-white">
                            <option value="5">5 requetes d'utilisateurs</option>
                            <option value="10" selected>10 requetes d'utilisateurs</option>
                            <option value="25">25 requetes d'utilisateurs</option>
                            <option value="50">50 requetes d'utilisateurs</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition">
                        Lancer le trafic simule
                    </button>
                </form>
            </div>

            <div class="mt-4 p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-600 font-mono">
                <span class="font-bold text-slate-700 block mb-1">Commande CLI continue :</span>
                php artisan tsena:simulate --requests=100 --delay=200
            </div>
        </div>
    </div>

    <!-- Tableau des logs de telemetrie (Dev 3 & RSI 2) -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Logs Structures JSON & Traces (Dev 3 / RSI 2)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Retrouvez tous les logs relies par un meme Trace ID (Loki & Tempo).</p>
            </div>
            <form action="{{ route('telemetry.dashboard') }}" method="GET" class="flex items-center space-x-2">
                <input type="text" name="trace_id" value="{{ request('trace_id') }}" placeholder="Filtrer par Trace ID..." class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 font-mono w-48 sm:w-64">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-medium">Filtrer</button>
                @if(request('trace_id'))
                    <a href="{{ route('telemetry.dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800 px-1">Reinit</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider font-mono">
                        <th class="py-2.5 px-4">Heure</th>
                        <th class="py-2.5 px-4">Service</th>
                        <th class="py-2.5 px-4">Niveau</th>
                        <th class="py-2.5 px-4">Message</th>
                        <th class="py-2.5 px-4">Trace ID</th>
                        <th class="py-2.5 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-4 text-slate-500">{{ $log->created_at->format('H:i:s.u') }}</td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase
                                    {{ $log->service === 'accueil' ? 'bg-sky-50 text-sky-700' : ($log->service === 'stock' ? 'bg-amber-50 text-amber-700' : 'bg-purple-50 text-purple-700') }}">
                                    {{ $log->service }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4">
                                @if($log->level === 'error')
                                    <span class="text-rose-600 font-bold uppercase">ERROR</span>
                                @elseif($log->level === 'warning')
                                    <span class="text-amber-600 font-bold uppercase">WARN</span>
                                @else
                                    <span class="text-slate-600 uppercase">INFO</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 font-sans text-slate-800 max-w-xs truncate">{{ $log->message }}</td>
                            <td class="py-2.5 px-4 text-emerald-700 truncate max-w-[150px]">{{ $log->trace_id }}</td>
                            <td class="py-2.5 px-4 text-right">
                                <a href="{{ route('telemetry.trace', $log->trace_id) }}" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded text-[11px] font-sans font-medium">Trace</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 font-sans">Aucun log enregistre pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
