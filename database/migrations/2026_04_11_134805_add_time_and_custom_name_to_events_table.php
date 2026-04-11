<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('events', function (Blueprint $table) {
        $table->time('event_time')->nullable()->after('start_date');
        $table->string('custom_name')->nullable()->after('name');
    });
}

public function down()
{
    Schema::table('events', function (Blueprint $table) {
        $table->dropColumn(['event_time', 'custom_name']);
    });
}
};
