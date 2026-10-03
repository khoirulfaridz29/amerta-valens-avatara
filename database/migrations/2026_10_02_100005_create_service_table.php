<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')->constrained('alat')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('biaya', 15, 2)->nullable();
            $table->timestamps();

            $table->index(['tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};
