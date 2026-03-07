<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faq_caches', function (Blueprint $table) {
            $table->text('question')->change();
            $table->longText('answer')->change();
            $table->dropIndex(['religion', 'updated_at']);
            $table->index('religion');
        });

        Schema::table('ai_chat_histories', function (Blueprint $table) {
            $table->index(['user_id', 'religion']);
        });
    }

    public function down(): void
    {
        Schema::table('faq_caches', function (Blueprint $table) {
            $table->string('question', 500)->change();
            $table->text('answer')->change();
            $table->dropIndex(['religion']);
            $table->index(['religion', 'updated_at']);
        });

        Schema::table('ai_chat_histories', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'religion']);
        });
    }
};
