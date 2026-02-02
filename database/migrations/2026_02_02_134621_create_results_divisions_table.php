<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsDivisionsTable extends Migration
{
    public function up()
    {
        Schema::create('results_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete();
            $table->foreignId('type_id')->nullable()->constrained('sport_types')->cascadeOnDelete();
            $table->string('division'); // Elementary / Secondary
            $table->string('sdo');      // SDO name
            $table->string('category'); // Boys / Girls / Mixed
            $table->string('medal');    // Gold / Silver / Bronze
            $table->foreignId('awarded_by')->constrained('users');
            $table->timestamps();

            $table->unique(['event_id','sport_id','type_id','division','sdo','category','medal'], 'unique_result_per_sdo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('results_divisions');
    }
}
