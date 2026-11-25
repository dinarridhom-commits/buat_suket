<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getNamaGelarAttribute()
    {
        return $this->nama_surat ?: $this->nama;
    }

    public function getJabatanDefinitifAttribute()
    {
        return $this->jabatan_surat ?: $this->jabatan;
    }

    public function suketAbsens()
    {
        return $this->hasMany(SuketAbsen::class);
    }
}
