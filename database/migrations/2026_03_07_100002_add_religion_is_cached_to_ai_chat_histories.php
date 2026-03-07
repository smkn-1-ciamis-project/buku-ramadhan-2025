<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_chat_histories', function (Blueprint $table) {
            $table->string('religion', 20)->nullable()->after('content');
            $table->boolean('is_cached')->default(false)->after('religion');
        });
    }

    public function down(): void
    {
        Schema::table('ai_chat_histories', function (Blueprint $table) {
            $table->dropColumn(['religion', 'is_cached']);
        });
    }
};
