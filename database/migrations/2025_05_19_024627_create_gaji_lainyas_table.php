<?php

use App\Models\Gaji;
use App\Models\GajiKaryawan;
use App\Models\Penggajian;
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
        Schema::create('gaji_lainyas', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(GajiKaryawan::class)->constrained('gaji_karyawans')->onDelete('cascade');
                    
            $table->string('type');
            $table->string('nama');
            $table->integer('total');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_lainyas');
    }
};
