<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('sports', function (Blueprint $table) {
        $table->json('levels')->nullable();     // e.g., ["Elementary","Secondary"]
        $table->json('categories')->nullable(); // e.g., ["Boys","Girls"]
    });
}

public function down()
{
    Schema::table('sports', function (Blueprint $table) {
        $table->dropColumn(['levels','categories']);
    });
}

};
