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
        Schema::create('market_analysis', function (Blueprint $table) {
            $table->id();
            $table->string('analysis_type'); // 'profit_analysis', 'market_trend', 'product_performance', 'customer_behavior'
            $table->json('data'); // Raw data yang dianalisis
            $table->json('insights'); // Insight dari AI
            $table->json('recommendations'); // Rekomendasi dari AI
            $table->boolean('ai_generated')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->date('analysis_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_analysis');
    }
};
