<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lapak_umkm', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pedagang');
            $table->string('nama_usaha');
            $table->string('jenis_usaha');
            $table->string('no_telepon')->nullable();
            $table->integer('tarif_bulanan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->date('tanggal_mulai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lapak_umkm');
    }
};