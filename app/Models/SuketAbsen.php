<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuketAbsen extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal_kejadian' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
