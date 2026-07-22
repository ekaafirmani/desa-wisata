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
        Schema::create('paket_tubing', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket');
            $table->enum('jenis', ['mini', 'dewasa']);
            $table->text('fasilitas');
            $table->integer('harga');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_tubing');
    }
};
