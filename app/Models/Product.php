<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
