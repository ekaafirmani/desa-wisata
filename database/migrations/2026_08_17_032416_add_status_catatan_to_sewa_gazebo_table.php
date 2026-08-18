<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sewa_gazebo', function (Blueprint $table) {
            $table->string('catatan')->nullable()->after('jumlah');
            $table->string('status')->default('disewa')->after('total_bayar');
            $table->timestamp('waktu_kembali')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('sewa_gazebo', function (Blueprint $table) {
            $table->dropColumn(['catatan', 'status', 'waktu_kembali']);
        });
    }
};