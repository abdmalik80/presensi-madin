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
        if (Schema::hasColumn('kitabs', 'kategori')) {
            Schema::table('kitabs', function (Blueprint $table) {
                $table->renameColumn('kategori', 'bidang_ilmu');
            });
        }

        Schema::table('kitabs', function (Blueprint $table) {
            if (!Schema::hasColumn('kitabs', 'pengarang')) {
                $table->string('pengarang')->nullable()->after('bidang_ilmu');
            }
            if (Schema::hasColumn('kitabs', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kitabs', function (Blueprint $table) {
            $table->renameColumn('bidang_ilmu', 'kategori');
            $table->dropColumn('pengarang');
            $table->text('keterangan')->nullable();
        });
    }
};
