<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor')->unique();
            $table->string('nama_latin');
            $table->string('nama_arab')->nullable();
            $table->integer('jumlah_ayat');
            $table->string('tempat_turun')->default('Makkiyyah');
            $table->integer('juz')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};
