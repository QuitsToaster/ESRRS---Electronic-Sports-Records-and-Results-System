<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('contact_number', 13)->nullable(); // +63XXXXXXXXX max 13 chars including +63
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('contact_number');
    });
}
};
