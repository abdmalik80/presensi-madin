<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ustadz = App\Models\Ustadz::find(1);
$jadwalIds = $ustadz->jadwals()->pluck('id');
$majelisIds = $ustadz->jadwals()->pluck('majelis_id');

$baseQuery = App\Models\Izin::where(function ($q) use ($jadwalIds, $majelisIds) {
    $q->whereIn('jadwal_id', $jadwalIds)
          ->orWhere(function ($q2) use ($majelisIds) {
              $q2->whereNull('jadwal_id')
                ->whereHas('santri', fn($sq) => $sq->whereIn('majelis_id', $majelisIds));
          });
});

$izins = $baseQuery->get();
echo json_encode(['count' => $izins->count(), 'izins' => $izins]);
