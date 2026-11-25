<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->nullable()->index();
            $table->string('nama');
            $table->string('pangkat')->nullable();
            $table->string('kelas')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('rangkap')->nullable();
            $table->string('unit')->nullable()->index();
            $table->string('satker')->nullable();
            $table->string('ket')->nullable();
            $table->string('jabatan_surat')->nullable();
            $table->string('rangkap_surat')->nullable();
            $table->string('nama_surat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
