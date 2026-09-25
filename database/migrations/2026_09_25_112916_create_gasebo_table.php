<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gasebo', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gasebo');
            $table->enum('jenis', ['kecil', 'besar']);
            $table->integer('harga_per_jam')->default(0);
            $table->integer('durasi_minimal')->default(1); // dalam jam (kecil=1, besar=3)
            $table->enum('status', ['tersedia', 'disewa'])->default('tersedia');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gasebo');
    }
};