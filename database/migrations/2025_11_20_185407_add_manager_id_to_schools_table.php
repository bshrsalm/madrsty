<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('schools', function (Blueprint $table) {
        $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
    });
}

public function down()
{
    Schema::table('schools', function (Blueprint $table) {
        $table->dropForeign(['manager_id']);
        $table->dropColumn('manager_id');
    });
}

};
