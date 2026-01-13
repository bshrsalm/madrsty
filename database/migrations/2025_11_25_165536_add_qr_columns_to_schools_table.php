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
    Schema::table('schools', function (Blueprint $table) {
        if (!Schema::hasColumn('schools', 'barcode_token')) {
            $table->string('barcode_token')->unique()->nullable();
        }

        if (!Schema::hasColumn('schools', 'barcode_image')) {
            $table->string('barcode_image')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('schools', function (Blueprint $table) {
        if (Schema::hasColumn('schools', 'barcode_token')) {
            $table->dropColumn('barcode_token');
        }

        if (Schema::hasColumn('schools', 'barcode_image')) {
            $table->dropColumn('barcode_image');
        }
    });
}
};