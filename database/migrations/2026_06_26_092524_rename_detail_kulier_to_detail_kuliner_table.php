<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('detail_kulier', 'detail_kuliner');
    }

    public function down(): void
    {
        Schema::rename('detail_kuliner', 'detail_kulier');
    }
};