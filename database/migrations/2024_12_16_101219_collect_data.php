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
        Schema::create('collect_data', function (Blueprint $table) {
            $table->id();
            $table->decimal('temperature', 8, 2)->nullable(false)->default(0.0);
            $table->decimal('air_humidity', 8, 2)->nullable(false)->default(0.0);
            $table->decimal('soil_humidity', 8, 2)->nullable(false)->default(0.0);
            $table->decimal('light', 8, 2)->nullable(false)->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collect_data');
    }
};
