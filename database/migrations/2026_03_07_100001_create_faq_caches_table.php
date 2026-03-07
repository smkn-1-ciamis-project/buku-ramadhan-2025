<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faq_caches', function (Blueprint $table) {
            $table->id();
            $table->string('religion', 20);
            $table->string('question_hash', 32);
            $table->string('question', 500);
            $table->text('answer');
            $table->unsignedInteger('hit_count')->default(0);
            $table->timestamps();

            $table->index(['religion', 'question_hash']);
            $table->index(['religion', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_caches');
    }
};
