<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('event_school', function (Blueprint $table) {
        $table->id();
        $table->foreignId('event_id')->constrained()->cascadeOnDelete();
        $table->foreignId('school_id')->constrained()->cascadeOnDelete();
        $table->timestamps();

        $table->unique(['event_id', 'school_id']);
    });
}

public function down()
{
    Schema::dropIfExists('event_school');
}

};
