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
        Schema::create('artificial_intellegence', function (Blueprint $table) {
            $table->id();
            $table->string('message', 30)->nullable();
            $table->unsignedBigInteger('average_id');
            $table->timestamps();

            $table->foreign('average_id')->references('id')->on('average_daily');
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
