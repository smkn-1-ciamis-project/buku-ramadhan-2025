<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Add indexes for cursor-based pagination, search, sort & query performance.
   */
  public function up(): void
  {
    // ─── users ───
    Schema::table('users', function (Blueprint $table) {
      // Cursor pagination by name (role view page), search & sort in Filament tables
      $table->index('name', 'idx_users_name');

      // FK lookup: users belonging to a role
      $table->index('role_user_id', 'idx_users_role_user_id');

      // FK lookup: users belonging to a kelas
      $table->index('kelas_id', 'idx_users_kelas_id');

      // Search by NISN (siswa login, search)
      $table->index('nisn', 'idx_users_nisn');

      // Filter by jenis_kelamin
      $table->index('jenis_kelamin', 'idx_users_jenis_kelamin');

      // Sort by created_at (terdaftar column)
      $table->index('created_at', 'idx_users_created_at');
    });

    // ─── form_submissions ───
    Schema::table('form_submissions', function (Blueprint $table) {
      // Sort/filter by status (pending/verified/rejected)
      $table->index('status', 'idx_fs_status');

      // Sort by verified_at (validasi tables)
      $table->index('verified_at', 'idx_fs_verified_at');

      // Kesiswaan status filter + sort
      $table->index('kesiswaan_status', 'idx_fs_kesiswaan_status');

      // Compound: kesiswaan_status + verified_at (validasi table ordering)
      $table->index(['kesiswaan_status', 'verified_at'], 'idx_fs_kesiswaan_verified');

      // Sort by created_at
      $table->index('created_at', 'idx_fs_created_at');

      // Sort by hari_ke (formulir listing)
      $table->index('hari_ke', 'idx_fs_hari_ke');

      // Compound: user_id + status (guru verification queries)
      $table->index(['user_id', 'status'], 'idx_fs_user_status');
    });

    // ─── prayer_checkins ───
    Schema::table('prayer_checkins', function (Blueprint $table) {
      // Cursor pagination by created_at
      $table->index('created_at', 'idx_pc_created_at');

      // Filter by shalat type
      $table->index('shalat', 'idx_pc_shalat');

      // Filter by status
      $table->index('status', 'idx_pc_status');
    });

    // ─── activity_logs ───
    Schema::table('activity_logs', function (Blueprint $table) {
      // Filter by panel
      $table->index('panel', 'idx_al_panel');

      // Filter by role
      $table->index('role', 'idx_al_role');

      // Compound: user_id + created_at (user activity timeline)
      $table->index(['user_id', 'created_at'], 'idx_al_user_created');
    });

    // ─── kelas ───
    Schema::table('kelas', function (Blueprint $table) {
      // Sort by nama (dashboard, export, dropdowns)
      $table->index('nama', 'idx_kelas_nama');

      // FK lookup: wali_id
      $table->index('wali_id', 'idx_kelas_wali_id');
    });

    // ─── form_settings ───
    Schema::table('form_settings', function (Blueprint $table) {
      // Filter by is_active
      $table->index('is_active', 'idx_fsettings_is_active');
    });

    // ─── role_users ───
    Schema::table('role_users', function (Blueprint $table) {
      // Sort by created_at
      $table->index('created_at', 'idx_ru_created_at');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropIndex('idx_users_name');
      $table->dropIndex('idx_users_role_user_id');
      $table->dropIndex('idx_users_kelas_id');
      $table->dropIndex('idx_users_nisn');
      $table->dropIndex('idx_users_jenis_kelamin');
      $table->dropIndex('idx_users_created_at');
    });

    Schema::table('form_submissions', function (Blueprint $table) {
      $table->dropIndex('idx_fs_status');
      $table->dropIndex('idx_fs_verified_at');
      $table->dropIndex('idx_fs_kesiswaan_status');
      $table->dropIndex('idx_fs_kesiswaan_verified');
      $table->dropIndex('idx_fs_created_at');
      $table->dropIndex('idx_fs_hari_ke');
      $table->dropIndex('idx_fs_user_status');
    });

    Schema::table('prayer_checkins', function (Blueprint $table) {
      $table->dropIndex('idx_pc_created_at');
      $table->dropIndex('idx_pc_shalat');
      $table->dropIndex('idx_pc_status');
    });

    Schema::table('activity_logs', function (Blueprint $table) {
      $table->dropIndex('idx_al_panel');
      $table->dropIndex('idx_al_role');
      $table->dropIndex('idx_al_user_created');
    });

    Schema::table('kelas', function (Blueprint $table) {
      $table->dropIndex('idx_kelas_nama');
      $table->dropIndex('idx_kelas_wali_id');
    });

    Schema::table('form_settings', function (Blueprint $table) {
      $table->dropIndex('idx_fsettings_is_active');
    });

    Schema::table('role_users', function (Blueprint $table) {
      $table->dropIndex('idx_ru_created_at');
    });
  }
};
