<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    DB::statement("
        ALTER TABLE events 
        MODIFY status ENUM(
            'Pending',
            'Ongoing',
            'Finished',
            'Finished and Awarded'
        ) NOT NULL DEFAULT 'Pending'
    ");
}

public function down()
{
    DB::statement("
        ALTER TABLE events 
        MODIFY status ENUM(
            'Pending',
            'Ongoing',
            'Finished'
        ) NOT NULL DEFAULT 'Pending'
    ");
}
};
