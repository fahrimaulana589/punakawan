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
        Schema::dropIfExists('gajis');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::create('gajis', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal');
            $table->string('nama');
            $table->integer('total');

            $table->timestamps();
        });
    }
};
