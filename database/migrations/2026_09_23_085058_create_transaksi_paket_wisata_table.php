<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_paket_wisata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->foreignId('paket_id')
                  ->constrained('paket_wisata')
                  ->onDelete('restrict');
            $table->integer('jumlah_orang');
            $table->integer('harga_per_orang');
            $table->integer('total_bayar');
            $table->string('nama_pemesan')->nullable();
            $table->string('no_telepon')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_paket_wisata');
    }
};