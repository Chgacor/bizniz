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
        'product_code', // Controller yang akan isi ini
        'category',
        'type',
        'buy_price',
        'sell_price',
        'stock_quantity',
        'image_path',
    ];

    // Hubungan ke tabel history stok
    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
