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
        Schema::table('contacts', function (Blueprint $table) {
            $table->text('ai_answer')->nullable();
            $table->string('ai_category')->nullable();
            $table->string('ai_sentiment')->nullable();
            $table->string('ai_status')->default('pending');
            $table->timestamp('ai_processed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['ai_answer', 'ai_category', 'ai_sentiment', 'ai_status', 'ai_processed_at']);
        });
    }
};
