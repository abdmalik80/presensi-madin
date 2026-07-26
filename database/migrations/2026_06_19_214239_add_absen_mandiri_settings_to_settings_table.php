<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['key' => 'absen_toleransi_sebelum', 'value' => '15'],  // menit sebelum jam mulai
            ['key' => 'absen_toleransi_sesudah', 'value' => '30'],  // menit setelah jam mulai
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->updateOrInsert(
                ['key' => $row['key']],
                ['value' => $row['value'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'absen_toleransi_sebelum',
            'absen_toleransi_sesudah',
        ])->delete();
    }
};
