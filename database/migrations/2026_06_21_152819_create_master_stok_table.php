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
        Schema::create('master_stok', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['pelampung', 'pakan_ikan']);
            $table->integer('total_stok');
            $table->integer('tersedia');
            $table->integer('harga_satuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_stok');
    }
};
