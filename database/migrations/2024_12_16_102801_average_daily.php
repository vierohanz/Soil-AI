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
            $table->decimal('avg_suhu', 8, 2)->nullable(false);
            $table->decimal('avg_air_humidity', 8, 2)->nullable(false);
            $table->decimal('avg_soil_humidity', 8, 2)->nullable(false);
            $table->decimal('avg_light', 8, 2)->nullable(false);
            $table->unsignedBigInteger('collect_id');
            $table->timestamps();

            $table->foreign('collect_id')->references('id')->on('collect_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
