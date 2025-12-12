<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['asphalt_mixing_plant_address', 'concrete_batching_plant_address']);
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('asphalt_mixing_plant_address', 500)->nullable();
            $table->string('concrete_batching_plant_address', 500)->nullable();
        });
    }
};
