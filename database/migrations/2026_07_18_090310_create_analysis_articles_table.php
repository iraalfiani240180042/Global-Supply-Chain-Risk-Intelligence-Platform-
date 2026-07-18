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
        Schema::create('analysis_articles', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->foreignId('country_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->enum('category', [
                'Market Analysis',
                'Export Opportunity',
                'Risk Analysis',
                'Logistics'
            ]);

            $table->enum('risk_level', [
                'Low',
                'Medium',
                'High'
            ]);

            $table->boolean('recommended')->default(true);

            $table->enum('status', [
                'Draft',
                'Published'
            ])->default('Draft');

            $table->text('summary');

            $table->longText('content');

            $table->date('published_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_articles');
    }
};