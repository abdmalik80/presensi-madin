<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('keterangan'); // ringkasan field yang diubah
            $table->boolean('is_read')->default(false); // sudah dibaca admin?
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_logs');
    }
};
