<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_rating_scores', function (Blueprint $table) {
            $table->dropForeign(['school_rating_id']);
            $table->foreign('school_rating_id')
                  ->references('id')
                  ->on('school_ratings_inspectors')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('school_rating_scores', function (Blueprint $table) {
            $table->dropForeign(['school_rating_id']);
            $table->foreign('school_rating_id')
                  ->references('id')
                  ->on('school_ratings')
                  ->onDelete('cascade');
        });
    }
};