<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratUndangan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal_surat' => 'date',
        'extra_data' => 'array',
    ];

    public function penerimas()
    {
        return $this->hasMany(SuratUndanganPenerima::class)->orderBy('urutan');
    }
}
