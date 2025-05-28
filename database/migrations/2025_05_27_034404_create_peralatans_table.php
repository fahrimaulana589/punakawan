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
        Schema::create('peralatans', function (Blueprint $table) {
            $table->id();

            $table->string('nama');
            $table->date('tanggal_aktif');
            $table->date('tanggal_nonaktif')->nullable();
            $table->integer('harga')->default(0);
            $table->integer('umur_ekonomis')->default(0); // dalam bulan
            $table->integer('nilai_sisa')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peralatans');
    }
};
