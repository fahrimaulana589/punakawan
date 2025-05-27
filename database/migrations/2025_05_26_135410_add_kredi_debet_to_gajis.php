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
        Schema::table('gajis', function (Blueprint $table) {
            $table->foreignId('debet_id')->constrained('akuns')->onDelete('restrict');
            $table->foreignId('kredit_id')->constrained('akuns')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gajis', function (Blueprint $table) {
            $table->dropForeign(['debet_id']);
            $table->dropForeign(['kredit_id']);
            $table->dropColumn(['debet_id', 'kredit_id']);
        });
    }
};
