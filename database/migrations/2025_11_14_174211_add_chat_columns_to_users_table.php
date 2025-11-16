<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Track online/offline status
            $table->boolean('is_online')->default(false);

            // Track last seen time
            $table->timestamp('last_active')->nullable();

            // For typing indicator
            $table->boolean('is_typing')->default(false);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_online', 'last_active', 'is_typing']);
        });
    }
};
