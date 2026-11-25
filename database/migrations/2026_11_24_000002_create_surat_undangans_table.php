<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_undangans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->string('sifat')->default('Biasa');
            $table->string('lampiran')->default('-');
            $table->string('hal');
            $table->date('tanggal_surat');
            $table->string('tempat');
            $table->string('hari_tanggal_acara')->nullable();
            $table->string('waktu_acara')->nullable();
            $table->text('agenda')->nullable();
            $table->text('catatan')->nullable();
            $table->string('penandatangan_nama');
            $table->string('penandatangan_nip')->nullable();
            $table->string('penandatangan_pangkat')->nullable();
            $table->string('penandatangan_jabatan');
            $table->string('status')->default('draft'); // draft, final, diarsipkan
            $table->string('qr_code_token')->nullable()->unique();
            $table->json('extra_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_undangans');
    }
};
