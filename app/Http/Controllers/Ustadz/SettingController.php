<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('ustadz.setting.index');
    }

    public function updateMode(Request $request)
    {
        $request->validate([
            'mode_presensi' => 'required|in:by_ustadz,by_santri',
        ]);

        $ustadz = auth()->user()->ustadz
            ?? auth()->user()->ustadz()->firstOrCreate(['user_id' => auth()->id()]);

        $ustadz->update(['mode_presensi' => $request->mode_presensi]);

        return back()->with('success', 'Mode presensi untuk majelis Anda berhasil diubah ke: '
            . ($request->mode_presensi === 'by_ustadz' ? 'Input oleh Ustadz' : 'Absen Mandiri Santri'));
    }
}
