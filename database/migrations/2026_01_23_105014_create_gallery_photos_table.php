<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('photos'); // store multiple photo file paths as JSON
            $table->unsignedBigInteger('uploaded_by'); // admin ID
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_photos');
    }
};
