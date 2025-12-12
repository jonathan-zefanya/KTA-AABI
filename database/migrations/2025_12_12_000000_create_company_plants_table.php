<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['AMP', 'CBP'])->comment('AMP=Asphalt Mixing Plant, CBP=Concrete Batching Plant');
            $table->string('address', 500);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_plants');
    }
};
