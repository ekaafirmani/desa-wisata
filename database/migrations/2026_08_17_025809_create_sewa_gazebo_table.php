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
    Schema::create('sewa_gazebo', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('titik_jual');           // loket, admin, dst
        $table->enum('jenis_gazebo', ['besar', 'kecil']);
        $table->integer('jumlah');
        $table->integer('harga_satuan');
        $table->integer('total_bayar');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sewa_gazebo');
    }
};
