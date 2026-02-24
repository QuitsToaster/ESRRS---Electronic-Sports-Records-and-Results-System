<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('all_award_results', function (Blueprint $table) {
            $table->text('revision_message')->nullable()->after('status');
        });

        Schema::table('individual_award_results', function (Blueprint $table) {
            $table->text('revision_message')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('all_award_results', function (Blueprint $table) {
            $table->dropColumn('revision_message');
        });

        Schema::table('individual_award_results', function (Blueprint $table) {
            $table->dropColumn('revision_message');
        });
    }
};