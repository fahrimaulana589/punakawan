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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('debet_id')->constrained('akuns')->onDelete('restrict');
            $table->foreignId('kredit_id')->constrained('akuns')->onDelete('restrict');
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('restrict');
            
            $table->integer('total');
            $table->date('tanggal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
