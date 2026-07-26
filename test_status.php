<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Cek kolom status_aktif di tabel users
$cols = Illuminate\Support\Facades\Schema::getColumnListing("users");
echo "Columns: " . implode(", ", $cols) . "\n";

// Cek nilai status_aktif santri pertama
$santri = App\Models\Santri::with("user")->first();
if ($santri && $santri->user) {
    echo "User: " . $santri->user->name . "\n";
    echo "Status aktif: " . var_export($santri->user->status_aktif, true) . "\n";
}

