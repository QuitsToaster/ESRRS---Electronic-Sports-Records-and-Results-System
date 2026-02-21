<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('all_award_results', function (Blueprint $table) {
            $table->string('validation_file')->nullable()->after('status');
        });

        Schema::table('individual_award_results', function (Blueprint $table) {
            $table->string('validation_file')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('all_award_results', function (Blueprint $table) {
            $table->dropColumn('validation_file');
        });

        Schema::table('individual_award_results', function (Blueprint $table) {
            $table->dropColumn('validation_file');
        });
    }
};
