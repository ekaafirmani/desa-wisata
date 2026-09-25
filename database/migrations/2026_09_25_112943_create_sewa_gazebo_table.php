<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sewa_gasebo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->foreignId('gasebo_id')
                  ->constrained('gasebo')
                  ->onDelete('restrict');
            $table->string('nama_penyewa')->nullable();
            $table->integer('durasi_jam');
            $table->integer('harga_per_jam');
            $table->integer('total_bayar');
            $table->timestamp('waktu_mulai');
            $table->timestamp('waktu_selesai')->nullable();
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewa_gasebo');
    }
};