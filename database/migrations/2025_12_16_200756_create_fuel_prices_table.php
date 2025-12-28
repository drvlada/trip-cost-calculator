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
        Schema::create('fuel_prices', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->foreignId('fuel_type_id')
                ->constrained('fuel_types')
                ->cascadeOnDelete();

            // Price data
            $table->decimal('price_per_liter', 8, 3);
            $table->string('currency', 3)->default('EUR');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_prices');
    }
};
