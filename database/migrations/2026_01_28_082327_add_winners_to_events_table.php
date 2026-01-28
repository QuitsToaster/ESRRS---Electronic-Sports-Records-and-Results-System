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
        Schema::table('events', function (Blueprint $table) {
    $table->foreignId('gold_school_id')->nullable()->constrained('schools');
    $table->foreignId('silver_school_id')->nullable()->constrained('schools');
    $table->foreignId('bronze_school_id')->nullable()->constrained('schools');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
};
