<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            // Add is_online if missing
            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(0);
            }

            // Add is_typing if missing
            if (!Schema::hasColumn('users', 'is_typing')) {
                $table->boolean('is_typing')->default(0);
            }

            // Add last_active if missing
            if (!Schema::hasColumn('users', 'last_active')) {
                $table->timestamp('last_active')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'is_online')) {
                $table->dropColumn('is_online');
            }

            if (Schema::hasColumn('users', 'is_typing')) {
                $table->dropColumn('is_typing');
            }

            if (Schema::hasColumn('users', 'last_active')) {
                $table->dropColumn('last_active');
            }
        });
    }
};
