<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('coach_id')->nullable()->after('athlete_id');

            // Optional: add foreign key constraint
            $table->foreign('coach_id')->references('id')->on('coaches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['coach_id']);
            $table->dropColumn('coach_id');
        });
    }
};
