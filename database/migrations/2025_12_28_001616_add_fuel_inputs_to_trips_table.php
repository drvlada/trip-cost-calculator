<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // Saved inputs for rebuilding results from history
            $table->decimal('consumption_l_per_100km', 5, 2)->nullable()->after('fuel_type');
            $table->string('fuel_country_iso2', 2)->nullable()->after('consumption_l_per_100km');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['consumption_l_per_100km', 'fuel_country_iso2']);
        });
    }
};
