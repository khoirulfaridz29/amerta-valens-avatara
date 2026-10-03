<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')->constrained('alat')->cascadeOnDelete();
            $table->foreignId('proyek_id')->nullable()->constrained('proyek')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('hm_awal', 8, 2)->nullable();
            $table->decimal('hm_akhir', 8, 2)->nullable();
            $table->decimal('solar_jerigen', 8, 2)->nullable();
            $table->decimal('solar_liter', 10, 2)->default(0);
            $table->string('foto_hm_awal')->nullable();
            $table->string('foto_hm_akhir')->nullable();
            $table->string('foto_lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_harian');
    }
};
