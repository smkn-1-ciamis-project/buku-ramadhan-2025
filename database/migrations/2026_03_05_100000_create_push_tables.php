<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->text('endpoint')->unique();
            $table->string('p256dh_key');
            $table->string('auth_token');
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        Schema::create('push_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('body');
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->string('target')->default('all'); // all, siswa, guru, kesiswaan
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->uuid('sent_by')->nullable();
            $table->timestamps();

            $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('push_subscriptions');
    }
};
