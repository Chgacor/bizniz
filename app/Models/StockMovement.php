<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public $timestamps = false; // Disable automatic timestamps

    // Or if you want to keep created_at but not updated_at:
    // const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'description',
        'created_at', // Add this if you want to manually manage created_at
    ];
}
