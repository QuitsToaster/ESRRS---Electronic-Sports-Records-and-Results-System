<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forwarded_results', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('event_id');
    $table->unsignedBigInteger('sport_id');
    $table->unsignedBigInteger('type_id')->nullable();
    $table->string('division');
    $table->string('category')->nullable();
    $table->string('sdo');
    $table->string('medal');
    $table->integer('count')->default(0);
    $table->unsignedBigInteger('forwarded_by');
    $table->enum('status', ['pending','approved','rejected'])->default('pending');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forwarded_results');
    }
};
