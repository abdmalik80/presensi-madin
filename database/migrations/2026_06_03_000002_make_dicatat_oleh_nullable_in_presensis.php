<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat kolom dicatat_oleh nullable agar absen mandiri santri bisa membuat
     * record Presensi tanpa harus mengisi ustadz (pencatat).
     */
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Drop foreign key dulu (SQLite tidak support ALTER, tapi Laravel handles ini)
            $table->unsignedBigInteger('dicatat_oleh')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->unsignedBigInteger('dicatat_oleh')->nullable(false)->change();
        });
    }
};
