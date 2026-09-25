<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_umkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapak_id')
                  ->constrained('lapak_umkm')
                  ->onDelete('restrict');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('jumlah_bayar');
            $table->date('tanggal_bayar');
            $table->enum('status', ['lunas', 'belum_lunas'])->default('lunas');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_umkm');
    }
};