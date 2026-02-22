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
        Schema::table('role_users', function (Blueprint $table) {
            $table->json('menu_visibility')->nullable()->after('need_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_users', function (Blueprint $table) {
            $table->dropColumn('menu_visibility');
        });
    }
};
