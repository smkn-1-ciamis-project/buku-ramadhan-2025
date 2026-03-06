<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('target');
            $table->string('status')->default('sent')->after('failed_count'); // sent, scheduled, cancelled
        });
    }

    public function down(): void
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->dropColumn(['scheduled_at', 'status']);
        });
    }
};
