<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('persediaan_produk_jadis', function (Blueprint $table) {
            $table->id();

            $table->year('tahun');
            $table->integer('bulan');
            $table->integer('stok');
            $table->integer('stok_sisa');

            $table->foreignId('produk_id')->constrained('produks')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persediaan_produk_jadis');
    }
};
