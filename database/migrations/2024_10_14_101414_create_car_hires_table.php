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
        Schema::create('car_hires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('make_id');
            $table->integer('model_id');
            $table->integer('category_id');
            $table->integer('year');
            $table->string('transmission');
            $table->string('location');
            $table->integer('price');
            $table->integer('frequency')->default(0);  // 0 for daily, 1 for hourly
            $table->text('description')->nullable();
            $table->integer('available')->default(1); //1 for available 0 for not available
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_hires');
    }
};
