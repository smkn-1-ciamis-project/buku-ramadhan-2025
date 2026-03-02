<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('app_settings', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('group', 50)->index();       // 'api', 'ramadhan', 'location'
      $table->string('key', 100)->unique();        // unique setting key
      $table->text('value')->nullable();            // setting value
      $table->string('type', 20)->default('string'); // string, integer, boolean, date, json
      $table->string('label')->nullable();          // human-readable label
      $table->text('description')->nullable();      // help text
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('app_settings');
  }
};
