<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('athletes', function (Blueprint $table) {

            // Personal Info
            $table->string('last_name')->nullable()->after('id');
            $table->string('first_name')->nullable()->after('last_name');
            $table->string('middle_initial')->nullable()->after('first_name');
            $table->enum('sex', ['Male', 'Female'])->nullable()->after('middle_initial');
            $table->date('birthdate')->nullable()->after('sex'); // ✅ FIX

            // Event / Level
            $table->string('region')->nullable()->after('birthdate');
            $table->string('level')->nullable()->after('region');
            $table->string('event')->nullable()->after('level');

            // School Info
            $table->string('school_name')->nullable()->after('event');
            $table->string('school_type')->nullable()->after('school_name');
            $table->string('school_address')->nullable()->after('school_type');
            $table->string('school_division')->nullable()->after('school_address');
            $table->string('school_code')->nullable()->after('school_division');

            // LRN
            $table->string('lrn')->nullable()->unique()->after('school_code');
        });
    }

    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn([
                'last_name','first_name','middle_initial','sex','birthdate',
                'region','level','event',
                'school_name','school_type','school_address',
                'school_division','school_code','lrn'
            ]);
        });
    }
};

