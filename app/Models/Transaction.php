<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{

    protected $guarded = [];
    protected $fillable = [
        'invoice_code',
        'user_id',
        'customer_id',
        'total_amount',
        'cash_received',
        'change_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'cash_received' => 'integer',
        'change_amount' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
