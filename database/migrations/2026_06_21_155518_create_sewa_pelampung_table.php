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
        Schema::create('sewa_pelampung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->integer('jumlah');
            $table->integer('harga_satuan');
            $table->integer('total_bayar');
            $table->timestamp('waktu_kembali')->nullable();
            $table->enum('status', ['dipinjam', 'kembali'])
                  ->default('dipinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sewa_pelampung');
    }
};
