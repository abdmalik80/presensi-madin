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
        Schema::table('ustadzs', function (Blueprint $table) {
            $table->enum('mode_presensi', ['by_ustadz', 'by_santri'])->default('by_ustadz')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ustadzs', function (Blueprint $table) {
            $table->dropColumn('mode_presensi');
        });
    }
};
