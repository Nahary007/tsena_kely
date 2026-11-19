<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    // Modele Commande pour le service Commandes
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_amount',
        'status',
        'failure_reason',
        'trace_id',
        'execution_time_ms',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'execution_time_ms' => 'float',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
