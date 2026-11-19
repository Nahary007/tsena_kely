<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TelemetryController;
use Illuminate\Support\Facades\Route;

// Service Accueil: Vitrine et commande
Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::post('/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');

// Service Stock: Gestion et reapprovisionnement
Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
Route::post('/stock/{product}/restock', [StockController::class, 'restock'])->name('stock.restock');

// Service Commandes: Suivi et detail
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// Observabilite et Diagnostic (OpenTelemetry, Prometheus, Logs, Chaos)
Route::get('/telemetry', [TelemetryController::class, 'dashboard'])->name('telemetry.dashboard');
Route::post('/telemetry/settings', [TelemetryController::class, 'updateSettings'])->name('telemetry.settings');
Route::post('/telemetry/simulate', [TelemetryController::class, 'simulate'])->name('telemetry.simulate');
Route::get('/telemetry/trace/{traceId}', [TelemetryController::class, 'trace'])->name('telemetry.trace');

// Endpoint de scraping pour Prometheus
Route::get('/metrics', [TelemetryController::class, 'metrics'])->name('telemetry.metrics');
