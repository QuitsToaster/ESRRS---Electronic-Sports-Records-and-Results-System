<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_add_status_to_all_award_results_table.php
public function up()
{
    Schema::table('all_award_results', function (Blueprint $table) {
        $table->string('status')->default('approved')->after('awarded_by');
    });
}

public function down()
{
    Schema::table('all_award_results', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
