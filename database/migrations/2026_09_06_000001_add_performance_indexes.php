<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('nip');
        });

        Schema::table('santris', function (Blueprint $table) {
            $table->index(['tahun_ajaran_id', 'kelas_id']);
            $table->index(['tahun_ajaran_id', 'wali_id']);
        });

        Schema::table('setorans', function (Blueprint $table) {
            $table->index(['tahun_ajaran_id', 'guru_id', 'waktu_setor']);
            $table->index(['tahun_ajaran_id', 'santri_id', 'waktu_setor']);
            $table->index(['tahun_ajaran_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['nip']);
        });

        Schema::table('santris', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran_id', 'kelas_id']);
            $table->dropIndex(['tahun_ajaran_id', 'wali_id']);
        });

        Schema::table('setorans', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran_id', 'guru_id', 'waktu_setor']);
            $table->dropIndex(['tahun_ajaran_id', 'santri_id', 'waktu_setor']);
            $table->dropIndex(['tahun_ajaran_id', 'status']);
        });
    }
};
