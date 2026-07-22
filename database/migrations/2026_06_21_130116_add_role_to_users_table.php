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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'loket','tubing_mini','tubing_dewasa', 'kolam', 'kuliner'])
                  ->default('loket')
                  ->after('email');
            $table->boolean('aktif')
                  ->default(true)
                  ->after('role');
            $table->foreignId('created_by')
                  ->nullable()
                  ->after('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'aktif', 'created_by']);
        });
    }
};
