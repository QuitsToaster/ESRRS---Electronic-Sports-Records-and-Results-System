<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('games', function (Blueprint $table) {
        $table->integer('game_duration')->nullable()->default(48); // total game duration in minutes
        $table->integer('shot_clock')->nullable()->default(24);    // shot clock in seconds
    });
}

public function down(): void
{
    Schema::table('games', function (Blueprint $table) {
        $table->dropColumn(['game_duration', 'shot_clock']);
    });
}

};
