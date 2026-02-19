<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secretary_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_id')->nullable()->constrained('sport_types')->onDelete('cascade');
            $table->string('division');
            $table->string('category')->nullable();
            $table->string('sdo'); // Secretary's SDO
            $table->string('medal');
            $table->integer('count')->default(0);
            $table->foreignId('awarded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secretary_awards');
    }
};
