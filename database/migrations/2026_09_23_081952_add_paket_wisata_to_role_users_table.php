<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ← tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'loket', 'tubing_mini', 'tubing_dewasa', 'kolam', 'kuliner', 'paket_wisata') NOT NULL DEFAULT 'loket'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'loket', 'tubing_mini', 'tubing_dewasa', 'kolam', 'kuliner') NOT NULL DEFAULT 'loket'");
    }
};