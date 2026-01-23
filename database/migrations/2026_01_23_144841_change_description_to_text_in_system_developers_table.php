<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    \Illuminate\Support\Facades\Schema::table('system_developers', function ($table) {
        $table->text('description')->change();
    });
}

public function down()
{
    \Illuminate\Support\Facades\Schema::table('system_developers', function ($table) {
        $table->string('description', 255)->change();
    });
}

};
