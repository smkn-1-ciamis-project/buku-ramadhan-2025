<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending')->after('data');
            $table->string('verified_by')->nullable()->after('status');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('catatan_guru')->nullable()->after('verified_at');

            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['status', 'verified_by', 'verified_at', 'catatan_guru']);
        });
    }
};
