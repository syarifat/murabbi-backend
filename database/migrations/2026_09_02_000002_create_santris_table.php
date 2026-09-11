<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->string('nis')->unique();
            $table->string('nama_lengkap');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas_rombels')->nullOnDelete();
            $table->foreignId('wali_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('target_juz')->nullable()->default(null);
            $table->integer('progress_pct')->default(0);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};
