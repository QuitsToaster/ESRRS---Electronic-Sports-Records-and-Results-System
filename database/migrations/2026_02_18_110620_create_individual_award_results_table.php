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
        Schema::create('individual_award_results', function (Blueprint $table) {
    $table->id();
    $table->foreignId('event_id')->constrained('events');
    $table->foreignId('sport_id')->constrained('sports');
    $table->foreignId('type_id')->nullable()->constrained('sport_types');
    $table->string('division');
    $table->string('category')->nullable();
    $table->string('sdo');
    $table->string('medal');
    $table->foreignId('athlete_id')->constrained('athletes');
    $table->foreignId('awarded_by')->constrained('users');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_award_results');
    }
};
