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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('school_id')->constrained()->onDelete('cascade'); // Athlete belongs to a school
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');  // Athlete participates in a sport
            $table->enum('division', ['Elementary', 'Secondary']);
            $table->enum('category', ['Boys', 'Girls']);
            $table->enum('status', ['Active', 'Disqualified', 'Injured'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
