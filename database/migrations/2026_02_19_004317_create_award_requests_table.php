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
        Schema::create('award_requests', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('event_id');
    $table->string('award_type'); // all | individual

    $table->string('division');   // Elementary / Secondary / Paragames
    $table->string('category')->nullable();
    $table->string('sdo');

    $table->json('payload'); // RAW results data
    $table->string('attachment')->nullable();

    $table->enum('status', ['pending','approved','rejected'])->default('pending');

    $table->unsignedBigInteger('submitted_by');
    $table->unsignedBigInteger('validated_by')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('award_requests');
    }
};
