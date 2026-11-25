<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('konten');
            $table->string('warna_qr')->default('#213366');
            $table->string('warna_bg')->default('#ffffff');
            $table->boolean('use_logo_pu')->default(true);
            $table->integer('ukuran_logo')->default(22);
            $table->string('error_correction', 2)->default('H');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
