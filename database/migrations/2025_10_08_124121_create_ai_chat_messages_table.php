<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained('chat_sessions')->onDelete('cascade');
            $table->enum('sender_type', ['user', 'ai']);
            $table->text('message');
            $table->string('context')->nullable();
            $table->json('metadata')->nullable(); // Untuk menyimpan data tambahan seperti reasoning, confidence, dll
            $table->timestamps();
            
            $table->index(['chat_session_id', 'created_at']);
            $table->index('sender_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
    }
};