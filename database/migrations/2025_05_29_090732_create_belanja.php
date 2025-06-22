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
        Schema::create('belanjas', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal');
            $table->integer('total');
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('restrict');
          
            $table->foreignId('konsumsi_id')->constrained('konsumsis')->onDelete('restrict');
          

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('belanjas');
    }
};
