<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('team_a');
            $table->string('team_b');
            $table->integer('score_a')->nullable();
            $table->integer('score_b')->nullable();
            $table->enum('status', ['Upcoming', 'Ongoing', 'Finished'])->default('Upcoming');
            $table->dateTime('game_time')->nullable();
            $table->string('venue')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
