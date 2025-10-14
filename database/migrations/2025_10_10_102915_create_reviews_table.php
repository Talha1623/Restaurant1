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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->integer('rating'); // 1-5 stars
            $table->text('description');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            // Index for faster queries
            $table->index('restaurant_id');
            $table->index('customer_id');
            
            // Unique constraint: One customer can review one restaurant only once
            $table->unique(['customer_id', 'restaurant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
