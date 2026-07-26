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
        Schema::table('dokumentasis', function (Blueprint $table) {
            $table->foreignId('majelis_id')->nullable()->change();
            $table->foreignId('pengampu_id')->nullable()->change();
            $table->foreignId('kitab_id')->nullable()->after('majelis_id')->constrained('kitabs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumentasis', function (Blueprint $table) {
            $table->dropForeign(['kitab_id']);
            $table->dropColumn('kitab_id');
            $table->foreignId('majelis_id')->nullable(false)->change();
            $table->foreignId('pengampu_id')->nullable(false)->change();
        });
    }
};
