<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('athletes', function (Blueprint $table) {
        $table->string('qr_code')->unique()->after('status');
    });
}

public function down()
{
    Schema::table('athletes', function (Blueprint $table) {
        $table->dropColumn('qr_code');
    });
}

};
