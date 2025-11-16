<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // Who sent the message
            $table->unsignedBigInteger('sender_id');

            // Who received the message
            $table->unsignedBigInteger('receiver_id');

            // Message content
            $table->text('content')->nullable();

            // File attachment (optional)
            $table->string('file_path')->nullable();

            // Read status
            $table->boolean('is_read')->default(false);

            $table->timestamps();

            // Foreign keys
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
