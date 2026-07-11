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
    Schema::create('ports', function (Blueprint $table) {

        $table->id();

        $table->foreignId('country_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->foreignId('status_id')
              ->constrained('port_statuses')
              ->cascadeOnDelete();

        $table->string('name');

        $table->string('city');

        $table->decimal('latitude',10,7);

        $table->decimal('longitude',10,7);

        $table->timestamps();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
