<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->nullable()->constrained('proyek')->nullOnDelete();
            $table->date('tanggal');
            $table->string('jenis');
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan');
            $table->timestamps();

            $table->index(['tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_kas');
    }
};
