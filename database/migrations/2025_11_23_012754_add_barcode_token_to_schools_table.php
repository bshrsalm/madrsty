<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('barcode_token')->unique()->nullable();
           
        });
    }

    public function down(): void {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('barcode_token');
        });
    }
};
