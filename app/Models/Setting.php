<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group'];

    // Kita tidak butuh timestamps (created_at/updated_at) untuk setting sederhana
    public $timestamps = false;
}
