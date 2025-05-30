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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produk_id')->constrained('produks')->onDelete('restrict');
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('restrict');
                        
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('total');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
