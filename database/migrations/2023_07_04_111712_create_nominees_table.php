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
        Schema::create('nominees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('award_id')->constrained();
            $table->string('slug');
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->string('link')->nullable();
            $table->string('link_title')->nullable();
            $table->text('flavour_text')->nullable();
            $table->integer('result')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['award_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominees');
    }
};
