<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
     Schema::create('schools', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('google_map')->nullable();
    $table->text('address')->nullable();
    $table->string('phone')->nullable();
    $table->decimal('registration_fee', 10, 2)->default(0);
    $table->decimal('tuition', 10, 2)->nullable();
    $table->text('description')->nullable();
    $table->string('facebook')->nullable();
    $table->string('instagram')->nullable();
    $table->string('link_web')->nullable();
  
    $table->timestamps();
});
    }

    public function down()
    {
        Schema::dropIfExists('schools');
    }
};
