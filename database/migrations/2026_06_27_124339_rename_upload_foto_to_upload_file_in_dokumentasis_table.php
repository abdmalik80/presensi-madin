<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumentasis', function (Blueprint $table) {
            $table->renameColumn('upload_foto', 'upload_file');
        });
    }

    public function down(): void
    {
        Schema::table('dokumentasis', function (Blueprint $table) {
            $table->renameColumn('upload_file', 'upload_foto');
        });
    }
};
