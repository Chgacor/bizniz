<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price_at_sale',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_sale' => 'integer',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
