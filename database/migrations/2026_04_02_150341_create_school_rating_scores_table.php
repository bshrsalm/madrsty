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
        Schema::create('school_rating_scores', function (Blueprint $table) {
     $table->id();
    $table->foreignId('school_rating_id')->constrained('school_ratings')->onDelete('cascade');
    $table->foreignId('rating_criteria_id')->constrained('rating_criteria')->onDelete('cascade');
    $table->unsignedTinyInteger('score'); 
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_rating_scores');
    }
};
