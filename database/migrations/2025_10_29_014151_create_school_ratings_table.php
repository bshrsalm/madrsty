<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('education_level');
            $table->unsignedTinyInteger('support');
            $table->unsignedTinyInteger('teachers');
            $table->unsignedTinyInteger('follow_up');
            $table->unsignedTinyInteger('trips');
            $table->timestamps();

            $table->unique(['user_id', 'school_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_ratings');
    }
};
