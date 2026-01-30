<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'product_code',
        'category',
        'type',
        'buy_price',
        'sell_price',
        'stock_quantity',
        'image_path',
    ];

    /**
     * The "Boot" method handles the secure auto-generation logic.
     * This ensures no controller logic can bypass the code generation rule.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Logic: Initials of Name + DDMMYYYY
            $initials = collect(explode(' ', $product->name))
                ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                ->join('');

            $date = Carbon::now()->format('dmY');

            // Final Code: YBLLM29012026
            $baseCode = $initials . $date;

            // Safety: Ensure Uniqueness. If exists, append a suffix.
            $code = $baseCode;
            $count = 1;
            while (static::where('product_code', $code)->exists()) {
                $code = $baseCode . '-' . $count++;
            }

            $product->product_code = $code;
        });
    }

    // Relationship for Audit Trail
    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
