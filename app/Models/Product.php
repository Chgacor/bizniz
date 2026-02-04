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

    // Tambahkan method ini di dalam class Product extends Model
    public static function generateId($name)
    {
        $words = explode(' ', strtoupper(preg_replace('/[^a-zA-Z0-9 ]/', '', $name)));
        $initials = '';

        foreach ($words as $word) {
            $initials .= substr($word, 0, 1);
        }

        $initials = substr($initials, 0, 3);

        if (strlen($initials) < 2) {
            $initials = substr(strtoupper($name), 0, 2);
        }

        $nextId = (self::withTrashed()->max('id') ?? 0) + 1;

        return $initials . str_pad($nextId, 8, '0', STR_PAD_LEFT);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
