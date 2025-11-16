<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add receiver_id ONLY if not exists
        if (!Schema::hasColumn('messages', 'receiver_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->unsignedBigInteger('receiver_id')->nullable()->after('sender_id');
            });
        }

        // 2. Copy user_id → receiver_id
        if (Schema::hasColumn('messages', 'user_id') && Schema::hasColumn('messages', 'receiver_id')) {
            DB::table('messages')->update([
                'receiver_id' => DB::raw('user_id')
            ]);
        }

        // 3. SAFELY DROP EXISTING INDEX / FK BEFORE ADDING NEW ONE
        Schema::table('messages', function (Blueprint $table) {

            // Drop FK if exists
            try {
                $table->dropForeign('messages_receiver_id_foreign');
            } catch (\Exception $e) {}

            // Drop index if exists (MySQL auto-creates it)
            try {
                $table->dropIndex('messages_receiver_id_foreign');
            } catch (\Exception $e) {}

            // Now add FK cleanly
            try {
                $table->foreign('receiver_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            } catch (\Exception $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {

            try {
                $table->dropForeign(['receiver_id']);
            } catch (\Exception $e) {}

            if (Schema::hasColumn('messages', 'receiver_id')) {
                try {
                    $table->dropColumn('receiver_id');
                } catch (\Exception $e) {}
            }
        });
    }
};
