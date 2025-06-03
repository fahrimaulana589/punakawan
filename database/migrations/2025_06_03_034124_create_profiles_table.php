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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            $table->string('nama');
            $table->string('alamat');
            $table->string('handphone');
            $table->string('logo')->nullable();
            $table->string('email_server')->nullable();
            $table->integer('email_port')->nullable();
            $table->string('email_password')->nullable();
            $table->string('email_username')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
