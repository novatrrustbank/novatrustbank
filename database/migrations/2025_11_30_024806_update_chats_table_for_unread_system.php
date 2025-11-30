<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            if (!Schema::hasColumn('chats', 'sender_id')) {
                $table->unsignedBigInteger('sender_id')->nullable();
            }

            if (!Schema::hasColumn('chats', 'receiver_id')) {
                $table->unsignedBigInteger('receiver_id')->nullable();
            }

            if (!Schema::hasColumn('chats', 'is_read')) {
                $table->boolean('is_read')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            if (Schema::hasColumn('chats', 'sender_id')) {
                $table->dropColumn('sender_id');
            }

            if (Schema::hasColumn('chats', 'receiver_id')) {
                $table->dropColumn('receiver_id');
            }

            if (Schema::hasColumn('chats', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
};
