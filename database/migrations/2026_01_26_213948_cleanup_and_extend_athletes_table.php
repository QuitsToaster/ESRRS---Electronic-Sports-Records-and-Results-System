<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('athletes', function (Blueprint $table) {

            // ❌ REMOVE obsolete column
            if (Schema::hasColumn('athletes', 'name')) {
                $table->dropColumn('name');
            }

            // ➕ ADD missing fields (SAFE: nullable)
            if (!Schema::hasColumn('athletes', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('sex');
            }

            if (!Schema::hasColumn('athletes', 'region')) {
                $table->string('region')->nullable()->after('birthdate');
            }

            if (!Schema::hasColumn('athletes', 'level')) {
                $table->string('level')->nullable()->after('region');
            }

            if (!Schema::hasColumn('athletes', 'event')) {
                $table->string('event')->nullable()->after('level');
            }

            if (!Schema::hasColumn('athletes', 'school_name')) {
                $table->string('school_name')->nullable()->after('event');
            }

            if (!Schema::hasColumn('athletes', 'school_type')) {
                $table->string('school_type')->nullable()->after('school_name');
            }

            if (!Schema::hasColumn('athletes', 'school_address')) {
                $table->string('school_address')->nullable()->after('school_type');
            }

            if (!Schema::hasColumn('athletes', 'school_division')) {
                $table->string('school_division')->nullable()->after('school_address');
            }

            if (!Schema::hasColumn('athletes', 'school_code')) {
                $table->string('school_code')->nullable()->after('school_division');
            }

            if (!Schema::hasColumn('athletes', 'lrn')) {
                $table->string('lrn')->nullable()->unique()->after('school_code');
            }
        });
    }

    public function down(): void
    {
        // ❌ DO NOT DROP DATA ON ROLLBACK
    }
};
