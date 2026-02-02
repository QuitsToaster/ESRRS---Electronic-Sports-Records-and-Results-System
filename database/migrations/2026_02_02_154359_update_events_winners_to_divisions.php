<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('events', function (Blueprint $table) {
        // Drop foreign keys first
        $table->dropForeign(['gold_school_id']);
        $table->dropForeign(['silver_school_id']);
        $table->dropForeign(['bronze_school_id']);

        // Then drop columns
        $table->dropColumn(['gold_school_id', 'silver_school_id', 'bronze_school_id']);

        // Add new division winner columns
        $table->unsignedBigInteger('gold_division_id')->nullable()->after('category');
        $table->unsignedBigInteger('silver_division_id')->nullable()->after('gold_division_id');
        $table->unsignedBigInteger('bronze_division_id')->nullable()->after('silver_division_id');

        // Add foreign keys
        $table->foreign('gold_division_id')->references('id')->on('divisions')->onDelete('set null');
        $table->foreign('silver_division_id')->references('id')->on('divisions')->onDelete('set null');
        $table->foreign('bronze_division_id')->references('id')->on('divisions')->onDelete('set null');
    });
}


public function down()
{
    Schema::table('events', function (Blueprint $table) {
        // Drop new foreign keys first
        $table->dropForeign(['gold_division_id']);
        $table->dropForeign(['silver_division_id']);
        $table->dropForeign(['bronze_division_id']);

        // Drop new columns
        $table->dropColumn(['gold_division_id', 'silver_division_id', 'bronze_division_id']);

        // Add old school winner columns back
        $table->unsignedBigInteger('gold_school_id')->nullable();
        $table->unsignedBigInteger('silver_school_id')->nullable();
        $table->unsignedBigInteger('bronze_school_id')->nullable();

        // Re-add foreign keys
        $table->foreign('gold_school_id')->references('id')->on('schools')->onDelete('set null');
        $table->foreign('silver_school_id')->references('id')->on('schools')->onDelete('set null');
        $table->foreign('bronze_school_id')->references('id')->on('schools')->onDelete('set null');
    });
}


};
