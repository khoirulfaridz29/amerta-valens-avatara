<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bon', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->string('customer');
            $table->foreignId('proyek_id')->nullable()->constrained('proyek')->nullOnDelete();
            $table->date('tanggal');
            $table->date('jatuh_tempo')->nullable();
            $table->decimal('total', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon');
    }
};
