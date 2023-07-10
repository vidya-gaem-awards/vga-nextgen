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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('steam_id')->nullable();
            $table->string('discord_id')->nullable();
            $table->string('primary_connection')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamp('first_login')->nullable();
            $table->timestamp('last_login')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->unique('steam_id');
            $table->unique('discord_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
