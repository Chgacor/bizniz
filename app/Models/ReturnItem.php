<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;

    // Pastikan nama tabel di database benar, misal: 'return_items'
    protected $table = 'return_items';
    protected $guarded = ['id'];

    // Relasi balik ke Header
    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class, 'return_id');
    }

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
