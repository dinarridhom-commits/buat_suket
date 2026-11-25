<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_undangan_penerimas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_undangan_id')->constrained('surat_undangans')->cascadeOnDelete();
            $table->text('nama_penerima');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_undangan_penerimas');
    }
};
