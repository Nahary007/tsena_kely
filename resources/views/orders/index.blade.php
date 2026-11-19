@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- En-tete Service Commandes -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Service Commandes</h1>
            <p class="text-sm text-slate-500 mt-1">Dev 2 : Enregistrement dans PostgreSQL, comptage des commandes reussies/echouees et correlation avec OpenTelemetry.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                PostgreSQL Connecte
            </span>
        </div>
    </div>

    <!-- Metriques du service Commandes -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total commandes traitees</span>
            <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $totalOrders }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block">Commandes reussies</span>
            <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ $successfulOrders }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider block">Commandes echouees</span>
            <span class="text-2xl font-bold text-rose-600 mt-1 block">{{ $failedOrders }}</span>
        </div>
    </div>

    <!-- Filtrage par statut -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center space-x-2">
        <span class="text-xs font-semibold text-slate-500 mr-2">Filtrer :</span>
        <a href="{{ route('orders.index') }}" class="px-3 py-1 rounded-lg text-xs font-medium {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Tous</a>
        <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="px-3 py-1 rounded-lg text-xs font-medium {{ request('status') === 'completed' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Reussies</a>
        <a href="{{ route('orders.index', ['status' => 'failed']) }}" class="px-3 py-1 rounded-lg text-xs font-medium {{ request('status') === 'failed' ? 'bg-rose-700 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Echouees</a>
    </div>

    <!-- Tableau des commandes -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4">Numero</th>
                        <th class="py-3 px-4">Client</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Montant</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4">Latence</th>
                        <th class="py-3 px-4">Trace ID (Loki / Tempo)</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ $ord->order_number }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-800">{{ $ord->customer_name }}</div>
                                @if($ord->customer_email)
                                    <div class="text-slate-400 text-[11px]">{{ $ord->customer_email }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $ord->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="py-3 px-4 font-mono font-semibold text-slate-800">{{ number_format($ord->total_amount, 0, ',', ' ') }} Ar</td>
                            <td class="py-3 px-4">
                                @if($ord->status === 'completed')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-semibold">Succes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-semibold" title="{{ $ord->failure_reason }}">Echec</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $ord->execution_time_ms }} ms</td>
                            <td class="py-3 px-4 font-mono">
                                <a href="{{ route('telemetry.trace', $ord->trace_id) }}" class="text-emerald-700 hover:text-emerald-900 underline truncate block max-w-[140px]">
                                    {{ $ord->trace_id }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('orders.show', $ord->id) }}" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Aucune commande enregistree.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
