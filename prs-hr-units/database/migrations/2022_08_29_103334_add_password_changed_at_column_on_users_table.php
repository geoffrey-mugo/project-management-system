<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('password_changed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_changed_at');
        });
    }
};
