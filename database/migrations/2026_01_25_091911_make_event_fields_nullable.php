<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('sport_id')->nullable()->change();
            $table->string('level')->nullable()->change();
            $table->string('age_group')->nullable()->change();
            $table->string('category')->nullable()->change();
            $table->string('venue')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('sport_id')->nullable(false)->change();
            $table->string('level')->nullable(false)->change();
            $table->string('age_group')->nullable(false)->change();
            $table->string('category')->nullable(false)->change();
            $table->string('venue')->nullable(false)->change();
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
        });
    }
};
