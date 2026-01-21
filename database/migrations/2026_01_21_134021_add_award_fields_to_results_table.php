<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {

            // Sport where the medal was earned
            $table->foreignId('sport_id')
                  ->after('event_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Competition level
            $table->enum('division', ['Elementary', 'Secondary'])
                  ->after('sport_id');

            // Gender / category
            $table->enum('category', ['Boys', 'Girls', 'Mixed'])
                  ->after('division');

            // Optional: team name for team sports
            $table->string('team_name')
                  ->nullable()
                  ->after('athlete_id');

            // Optional: admin who awarded the medal
            $table->foreignId('awarded_by')
                  ->nullable()
                  ->after('medal')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropForeign(['awarded_by']);

            $table->dropColumn([
                'sport_id',
                'division',
                'category',
                'team_name',
                'awarded_by',
            ]);
        });
    }
};
