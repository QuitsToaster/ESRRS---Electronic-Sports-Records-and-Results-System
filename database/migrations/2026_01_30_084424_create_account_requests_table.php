<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('account_requests', function (Blueprint $table) {
        $table->id();
        $table->string('email')->unique();
        $table->enum('type', ['forgot_password', 'new_account']);
        $table->text('message')->nullable();
        $table->boolean('processed')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_requests');
    }
};
