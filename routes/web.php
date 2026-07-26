<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Ustadz;
use App\Http\Controllers\Santri;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/santri', [ProfileController::class, 'updateSantri'])->name('profile.update-santri');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mark notifications as read (simpan timestamp di database)
    Route::post('/notifications/mark-read', function () {
        if(auth()->check()) {
            auth()->user()->update(['notif_read_at' => now()]);
        }
        return response()->json(['ok' => true]);
    })->name('notifications.mark-read');

    // Tindakan per notifikasi (baca / hapus)
    Route::post('/notifications/action', [\App\Http\Controllers\NotifikasiController::class, 'action'])->name('notifications.action');

    // Halaman Semua Notifikasi
    Route::get('/notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index'])->name('notifikasi.index');
});

/* ============================================================
   ADMIN ROUTES
   ============================================================ */
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Data Santri
    Route::get('santri/template', [Admin\SantriController::class, 'template'])->name('santri.template');
    Route::post('santri/import', [Admin\SantriController::class, 'import'])->name('santri.import');
    Route::post('santri/bulk-action', [Admin\SantriController::class, 'bulkAction'])->name('santri.bulk-action');
    Route::resource('santri', Admin\SantriController::class);
    // Data Ustadz
    Route::post('ustadz/bulk-action', [Admin\UstadzController::class, 'bulkAction'])->name('ustadz.bulk-action');
    Route::resource('ustadz', Admin\UstadzController::class);
    // Data Kitab
    Route::post('kitab/bulk-action', [Admin\KitabController::class, 'bulkAction'])->name('kitab.bulk-action');
    Route::resource('kitab', Admin\KitabController::class);
    // Data Majelis
    Route::post('majelis/bulk-action', [Admin\MajelisController::class, 'bulkAction'])->name('majelis.bulk-action');
    Route::resource('majelis', Admin\MajelisController::class);
    // Data Jadwal
    Route::post('jadwal/bulk-action', [Admin\JadwalController::class, 'bulkAction'])->name('jadwal.bulk-action');
    Route::resource('jadwal', Admin\JadwalController::class);
    // Presensi
    Route::post('presensi/toggle', [Admin\PresensiController::class, 'toggleStatus'])->name('presensi.toggle');
    Route::get('presensi-santris', [Admin\PresensiController::class, 'getSantris'])->name('presensi.santris');
    Route::resource('presensi', Admin\PresensiController::class);
    // Validasi Izin
    Route::resource('izin', Admin\IzinController::class);
    Route::patch('izin/{izin}/validasi', [Admin\IzinController::class, 'validasi'])->name('izin.validasi');
    // Laporan
    Route::get('laporan', [Admin\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/cetak', [Admin\LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('laporan/pdf', [Admin\LaporanController::class, 'pdf'])->name('laporan.pdf');
    // Dokumentasi Pembelajaran
    Route::resource('dokumentasi', Admin\DokumentasiController::class)->except(['show']);
    // Manajemen Pengguna
    Route::post('pengguna/bulk-action', [Admin\PenggunaController::class, 'bulkAction'])->name('pengguna.bulk-action');
    Route::resource('pengguna', Admin\PenggunaController::class);

});

/* ============================================================
   USTADZ ROUTES
   ============================================================ */
Route::middleware(['auth', 'role:ustadz'])->prefix('ustadz')->name('ustadz.')->group(function () {

    
    // Toggle Buka/Tutup Akses Presensi
    Route::post('presensi/toggle', [Ustadz\PresensiController::class, 'toggleStatus'])->name('presensi.toggle');

    Route::get('jadwal', [Ustadz\JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('jadwal/{jadwal}', [Ustadz\JadwalController::class, 'show'])->name('jadwal.show');
    Route::resource('presensi', Ustadz\PresensiController::class);
    Route::get('presensi-santris', [Ustadz\PresensiController::class, 'getSantris'])->name('presensi.santris');
    Route::resource('izin', Ustadz\IzinController::class);
    Route::patch('izin/{izin}/validasi', [Ustadz\IzinController::class, 'validasi'])->name('izin.validasi');
    Route::resource('dokumentasi', Ustadz\DokumentasiController::class);
    // Rekap & laporan presensi kelas
    Route::get('laporan', [Ustadz\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/cetak', [Ustadz\LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('laporan/pdf', [Ustadz\LaporanController::class, 'pdf'])->name('laporan.pdf');
});

/* ============================================================
   SANTRI ROUTES
   ============================================================ */
Route::middleware(['auth', 'role:santri'])->prefix('santri')->name('santri.')->group(function () {
    // Riwayat presensi
    Route::get('presensi', [Santri\PresensiController::class, 'index'])->name('presensi.index');
    // Presensi semua santri (satu majelis)
    Route::get('presensi-semua', [Santri\PresensiController::class, 'indexSemua'])->name('presensi.semua');
    // Izin
    Route::resource('izin', Santri\IzinController::class)->only(['index', 'create', 'store']);
    // Dokumentasi kelas
    Route::get('dokumentasi', [Santri\DokumentasiController::class, 'index'])->name('dokumentasi.index');
    // Jadwal kelas
    Route::get('jadwal', [Santri\JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('jadwal/{jadwal}', [Santri\JadwalController::class, 'show'])->name('jadwal.show');
    Route::post('jadwal/{jadwal}/mark-berkas-read', [Santri\JadwalController::class, 'markBerkasRead'])->name('jadwal.mark-berkas-read');
    // Absen mandiri (hanya aktif mode by_santri)
    Route::get('absen-mandiri', [Santri\AbsenMandiriController::class, 'index'])->name('absen-mandiri.index');
    Route::post('absen-mandiri', [Santri\AbsenMandiriController::class, 'store'])->name('absen-mandiri.store');
});

require __DIR__.'/auth.php';
