<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // e.g., 'School', 'Game'
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('user_id')->nullable(); // who made the change
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('action'); // 'created', 'updated', 'deleted'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
