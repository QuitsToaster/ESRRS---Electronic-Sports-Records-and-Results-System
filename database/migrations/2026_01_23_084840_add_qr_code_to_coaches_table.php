<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('coaches', function (Blueprint $table) {
        $table->string('qr_code')->nullable()->after('profile_photo');
    });
}

public function down()
{
    Schema::table('coaches', function (Blueprint $table) {
        $table->dropColumn('qr_code');
    });
}

};
