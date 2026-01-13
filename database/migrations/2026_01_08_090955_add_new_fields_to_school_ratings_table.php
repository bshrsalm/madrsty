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
    Schema::table('school_ratings', function (Blueprint $table) {
        $table->unsignedTinyInteger('parent_communication');
        $table->unsignedTinyInteger('exams');
        $table->unsignedTinyInteger('enrichment_curriculum');
        $table->unsignedTinyInteger('school_management');
        $table->unsignedTinyInteger('school_environment');
    });
}

public function down(): void
{
    Schema::table('school_ratings', function (Blueprint $table) {
        $table->dropColumn([
            'parent_communication',
            'exams',
            'enrichment_curriculum',
            'school_management',
            'school_environment'
        ]);
    });
}

};
