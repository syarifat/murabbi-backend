<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengampu_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas_rombels')->cascadeOnDelete();
            $table->string('jadwal_halaqah')->nullable()->default('07:30 - 09:00 WIB');
            $table->timestamps();
            $table->unique(['tahun_ajaran_id', 'guru_id', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengampu_kelas');
    }
};
