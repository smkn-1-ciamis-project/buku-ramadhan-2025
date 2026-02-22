<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_checkins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->date('tanggal');
            $table->string('shalat', 20); // subuh, dzuhur, ashar, maghrib, isya, tarawih, rowatib, tahajud, dhuha
            $table->string('tipe', 10);   // wajib, sunnah
            $table->string('status', 10); // jamaah, munfarid, ya, tidak
            $table->timestamp('waktu_checkin')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'tanggal', 'shalat']);
            $table->index(['user_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_checkins');
    }
};
