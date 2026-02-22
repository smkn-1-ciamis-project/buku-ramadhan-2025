<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->string('kesiswaan_status', 20)->default('pending')->after('catatan_guru');
            $table->string('validated_by')->nullable()->after('kesiswaan_status');
            $table->timestamp('validated_at')->nullable()->after('validated_by');
            $table->text('catatan_kesiswaan')->nullable()->after('validated_at');

            $table->foreign('validated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['kesiswaan_status', 'validated_by', 'validated_at', 'catatan_kesiswaan']);
        });
    }
};
