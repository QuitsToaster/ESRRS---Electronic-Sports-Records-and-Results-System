<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('games', function (Blueprint $table) {
        $table->string('team_a')->nullable()->change();
        $table->string('team_b')->nullable()->change();
    });
}

public function down()
{
    Schema::table('games', function (Blueprint $table) {
        $table->string('team_a')->nullable(false)->change();
        $table->string('team_b')->nullable(false)->change();
    });
}

};
