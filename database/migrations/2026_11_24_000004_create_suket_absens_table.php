<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suket_absens', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->string('jenis_absen'); // 'masuk' atau 'pulang'
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->string('nama_pegawai');
            $table->string('nip_pegawai')->nullable();
            $table->string('pangkat_pegawai')->nullable();
            $table->string('jabatan_pegawai');
            $table->date('tanggal_kejadian');
            $table->string('waktu_kejadian'); // misal: 07:30
            $table->text('alasan');
            $table->string('atasan_nama');
            $table->string('atasan_nip')->nullable();
            $table->string('atasan_pangkat')->nullable();
            $table->string('atasan_jabatan');
            $table->string('status')->default('disetujui');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suket_absens');
    }
};
