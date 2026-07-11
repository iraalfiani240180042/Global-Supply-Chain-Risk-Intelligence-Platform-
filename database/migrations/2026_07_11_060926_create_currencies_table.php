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
    Schema::create('currencies', function (Blueprint $table) {

        $table->id();

        $table->foreignId('country_id')
              ->constrained()
              ->cascadeOnDelete();

       $table->foreignId('currency_master_id')
      ->constrained('currencies_master')
      ->cascadeOnDelete();

        $table->decimal('exchange_rate',15,4);

        $table->timestamp('updated_at_api')->nullable();

        $table->timestamps();

    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
