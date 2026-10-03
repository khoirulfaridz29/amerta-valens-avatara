<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->foreignId('bon_id')->nullable()->after('proyek_id')->constrained('bon')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bon_id');
        });
    }
};
