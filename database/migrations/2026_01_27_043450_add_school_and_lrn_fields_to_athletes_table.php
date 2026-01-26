<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('athletes', function (Blueprint $table) {
        $table->string('region')->nullable()->after('birthdate');
        $table->enum('school_type', ['Public', 'Private'])->nullable()->after('region');
        $table->string('school_address')->nullable()->after('school_type');
        $table->string('school_division')->nullable()->after('school_address');
        $table->string('school_code')->nullable()->after('school_division');
        $table->string('lrn', 20)->nullable()->unique()->after('school_code');
    });
}

public function down()
{
    Schema::table('athletes', function (Blueprint $table) {
        $table->dropColumn([
            'region',
            'school_type',
            'school_address',
            'school_division',
            'school_code',
            'lrn'
        ]);
    });
}

};
