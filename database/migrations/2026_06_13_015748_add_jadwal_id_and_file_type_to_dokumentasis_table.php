<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumentasis', function (Blueprint $table) {
            // Hubungkan ke jadwal (nullable agar data lama tetap valid)
            $table->foreignId('jadwal_id')->nullable()->after('pengampu_id')
                  ->constrained('jadwals')->onDelete('set null');

            // Tipe file: image, pdf, doc, other
            $table->string('tipe_file')->default('image')->after('upload_foto');

            // Nama asli file (untuk tampilan)
            $table->string('nama_file')->nullable()->after('tipe_file');

            // Jadikan upload_foto nullable (field ini kita pakai sebagai file_path untuk semua tipe)
            $table->string('upload_foto')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('dokumentasis', function (Blueprint $table) {
            $table->dropForeign(['jadwal_id']);
            $table->dropColumn(['jadwal_id', 'tipe_file', 'nama_file']);
            $table->string('upload_foto')->nullable(false)->change();
        });
    }
};
