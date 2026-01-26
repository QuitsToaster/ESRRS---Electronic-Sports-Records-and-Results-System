<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->integer('game_time_left')->default(0); // seconds
            $table->integer('shot_clock_left')->default(0); // seconds
            $table->timestamp('game_timer_started_at')->nullable();
            $table->timestamp('shot_clock_started_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'game_time_left',
                'shot_clock_left',
                'game_timer_started_at',
                'shot_clock_started_at'
            ]);
        });
    }
};
