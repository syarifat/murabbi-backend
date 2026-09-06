<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setorans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('surah_id')->constrained('surahs')->cascadeOnDelete();
            $table->integer('ayat_mulai')->default(1);
            $table->integer('ayat_selesai')->default(20);
            $table->enum('status', ['lancar', 'kurang', 'mengulang'])->default('lancar');
            $table->integer('nilai')->nullable()->default(85);
            $table->text('catatan')->nullable();
            $table->timestamp('waktu_setor')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setorans');
    }
};
