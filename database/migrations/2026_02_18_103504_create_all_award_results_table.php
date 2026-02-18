<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('all_award_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('sport_id');
            $table->unsignedBigInteger('type_id')->nullable();
            $table->string('division'); // e.g. Elementary, Secondary
            $table->string('category')->nullable(); // Boys/Girls/Para
            $table->string('sdo'); // Division/School/Organization
            $table->string('medal'); // Gold/Silver/Bronze
            $table->unsignedBigInteger('count')->default(0);
            $table->unsignedBigInteger('awarded_by');
            $table->timestamps();

            $table->foreign('sport_id')->references('id')->on('sports');
            $table->foreign('type_id')->references('id')->on('sport_types');
            $table->foreign('awarded_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('all_award_results');
    }
};
