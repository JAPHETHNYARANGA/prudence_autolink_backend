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
        Schema::table('cars', function (Blueprint $table) {
            // Add foreign key for make_id
            $table->foreignId('make_id')
                  ->constrained('makes')
                  ->onDelete('cascade');

            // Add foreign key for model_id
            $table->foreignId('model_id')
                  ->constrained('car_models')
                  ->onDelete('cascade');

            // Add foreign key for category_id (nullable)
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['make_id']);
            $table->dropColumn('make_id');

            $table->dropForeign(['model_id']);
            $table->dropColumn('model_id');

            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
