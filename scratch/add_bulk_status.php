<?php

// 1. Update Route
$routeFile = 'd:\PRESENSI MADIN\routes\web.php';
$routeContent = file_get_contents($routeFile);
$routeContent = str_replace(
    "Route::post('santri/bulk-delete', [Admin\SantriController::class, 'bulkDelete'])->name('santri.bulk-delete');",
    "Route::post('santri/bulk-action', [Admin\SantriController::class, 'bulkAction'])->name('santri.bulk-action');",
    $routeContent
);
file_put_contents($routeFile, $routeContent);

// 2. Update Controller
$controllerFile = 'd:\PRESENSI MADIN\app\Http\Controllers\Admin\SantriController.php';
$controllerContent = file_get_contents($controllerFile);

$oldBulkDelete = <<<'EOD'
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.santri.index')->with('error', 'Tidak ada santri yang dipilih.');
        }

        $santris = Santri::with('user')->whereIn('id', $ids)->get();
        $count   = $santris->count();

        foreach ($santris as $santri) {
            if ($santri->user?->foto_profil) {
                Storage::disk('public')->delete($santri->user->foto_profil);
            }
            $santri->user?->delete(); // cascade akan hapus santri juga
        }

        return redirect()->route('admin.santri.index')
            ->with('success', "{$count} data santri berhasil dihapus.");
    }
EOD;

$newBulkAction = <<<'EOD'
    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('admin.santri.index')->with('error', 'Tidak ada santri yang dipilih.');
        }

        $santris = Santri::with('user')->whereIn('id', $ids)->get();
        $count   = $santris->count();

        if ($action === 'delete') {
            foreach ($santris as $santri) {
                if ($santri->user?->foto_profil) {
                    Storage::disk('public')->delete($santri->user->foto_profil);
                }
                $santri->user?->delete();
            }
            return redirect()->route('admin.santri.index')->with('success', "{$count} data santri berhasil dihapus.");
        } elseif ($action === 'active') {
            foreach ($santris as $santri) {
                if ($santri->user) {
                    $santri->user->update(['status_aktif' => true]);
                }
            }
            return redirect()->route('admin.santri.index')->with('success', "{$count} santri berhasil diaktifkan.");
        } elseif ($action === 'inactive') {
            foreach ($santris as $santri) {
                if ($santri->user) {
                    $santri->user->update(['status_aktif' => false]);
                }
            }
            return redirect()->route('admin.santri.index')->with('success', "{$count} santri berhasil dinonaktifkan.");
        }

        return redirect()->route('admin.santri.index')->with('error', 'Aksi tidak valid.');
    }
EOD;

if(strpos($controllerContent, 'bulkDelete') !== false) {
    $controllerContent = str_replace($oldBulkDelete, $newBulkAction, $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
}

// 3. Update UI
$viewFile = 'd:\PRESENSI MADIN\resources\views\admin\santri\index.blade.php';
$viewContent = file_get_contents($viewFile);

// Find the old bulk delete form
$oldFormPattern = '/<form method="POST" action="\{\{ route\(\'admin\.santri\.bulk-delete\'\) \}\}".*?<\/form>/s';

$newForm = <<<'EOD'
            <form method="POST" action="{{ route('admin.santri.bulk-action') }}" id="bulkActionForm">
                @csrf
                <input type="hidden" name="action" id="bulkActionType" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="if(confirm('Yakin set aktif ' + selectedIds.length + ' santri?')) { document.getElementById('bulkActionType').value='active'; document.getElementById('bulkActionForm').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700 transition-colors shadow-sm">
                        <i class="fas fa-check-circle"></i> Set Aktif
                    </button>
                    <button type="button" @click="if(confirm('Yakin set non-aktif ' + selectedIds.length + ' santri?')) { document.getElementById('bulkActionType').value='inactive'; document.getElementById('bulkActionForm').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-500 text-white text-xs font-semibold hover:bg-orange-600 transition-colors shadow-sm">
                        <i class="fas fa-times-circle"></i> Set Non-Aktif
                    </button>
                    <div class="hidden sm:block w-px h-5 bg-red-200 mx-1"></div>
                    <button type="button" @click="if(confirm('Yakin hapus ' + selectedIds.length + ' santri? Tindakan ini tidak dapat dibatalkan!')) { document.getElementById('bulkActionType').value='delete'; document.getElementById('bulkActionForm').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>
                </div>
            </form>
EOD;

$viewContent = preg_replace($oldFormPattern, $newForm, $viewContent);
file_put_contents($viewFile, $viewContent);

echo "Done.";
