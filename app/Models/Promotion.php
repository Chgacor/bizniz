<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function isValid()
    {
        if (!$this->is_active) return false;

        $today = now()->startOfDay();

        if ($this->start_date && $today->lt($this->start_date)) return false;
        if ($this->end_date && $today->gt($this->end_date)) return false;

        return true;
    }
}
