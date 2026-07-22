<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_kulier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')
                  ->constrained('transaksi_kuliner')
                  ->onDelete('cascade');
            $table->foreignId('menu_id')
                  ->constrained('menu_kuliner')
                  ->onDelete('restrict');
            $table->integer('jumlah');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kulier');
    }
};
