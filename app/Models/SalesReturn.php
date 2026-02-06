<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $table = 'returns'; // Pastikan nama tabel di database 'returns'
    protected $guarded = ['id'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Detail Barang
    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
}
