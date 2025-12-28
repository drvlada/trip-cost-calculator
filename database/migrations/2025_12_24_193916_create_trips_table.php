<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            // user
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // route
            $table->string('from');
            $table->string('to');
            $table->integer('distance_km');

            // options
            $table->string('fuel_type');
            $table->boolean('has_vignette')->default(false);

            // costs
            $table->decimal('fuel_cost', 8, 2);
            $table->decimal('toll_cost', 8, 2)->default(0);
            $table->decimal('vignette_cost', 8, 2)->default(0);
            $table->decimal('total_cost', 8, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
