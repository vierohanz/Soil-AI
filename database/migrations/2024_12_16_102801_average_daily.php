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
        Schema::create('average_daily', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('avg_temperature', 8, 2)->nullable()->default(0);
            $table->decimal('avg_air_humidity', 8, 2)->nullable()->default(0);
            $table->decimal('avg_soil_humidity', 8, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('average_daily');
    }
};
