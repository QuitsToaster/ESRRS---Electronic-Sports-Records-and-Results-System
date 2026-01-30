<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('games', function (Blueprint $table) {
        $table->text('live_video_url')->nullable()->after('venue');
    });
}

public function down()
{
    Schema::table('games', function (Blueprint $table) {
        $table->dropColumn('live_video_url');
    });
}

};
