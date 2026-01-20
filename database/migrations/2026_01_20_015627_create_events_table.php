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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');
            $table->enum('level', ['Division','Regional','National']);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('venue');
            $table->string('age_group'); // Elementary/Secondary
            $table->enum('category', ['Boys','Girls']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
