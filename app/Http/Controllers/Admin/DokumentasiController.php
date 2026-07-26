<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumentasi;
use App\Models\Jadwal;
use App\Models\Majelis;
use App\Models\Ustadz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DokumentasiController extends Controller
{
    /** Deteksi tipe file dari ekstensi */
    private function detectType(string $ext): string
    {
        $ext = strtolower($ext);
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) return 'image';
        if ($ext === 'pdf')                                                  return 'pdf';
        if (in_array($ext, ['doc', 'docx']))                                 return 'doc';
        if (in_array($ext, ['xls', 'xlsx']))                                 return 'xls';
        if (in_array($ext, ['ppt', 'pptx']))                                 return 'ppt';
        return 'other';
    }

    public function index(Request $request)
    {
        $jadwals  = Jadwal::with(['kitab', 'majelis', 'ustadz.user'])->get();

        // Hanya tampilkan Majelis & Kitab yang punya jadwal (agar filter "cerdas" bisa bekerja)
        $kitabIds   = $jadwals->pluck('kitab_id')->filter()->unique();
        $majelisIds = $jadwals->pluck('majelis_id')->filter()->unique();

        $majeliss = Majelis::whereIn('id', $majelisIds)->orderBy('nama_majelis')->get();
        $kitabs   = \App\Models\Kitab::whereIn('id', $kitabIds)->orderBy('nama_kitab')->get();
        $ustadzs  = Ustadz::join('users', 'users.id', '=', 'ustadzs.user_id')
                        ->orderBy('users.name')
                        ->select('ustadzs.*', 'users.name as nama')
                        ->get();

        $query = Dokumentasi::with(['majelis', 'jadwal.kitab', 'pengampu']);

        if ($request->filled('jadwal_id')) {
            $query->where('jadwal_id', $request->jadwal_id);
        }
        if ($request->filled('majelis_id')) {
            $query->where('majelis_id', $request->majelis_id);
        }
        if ($request->filled('kitab_id')) {
            $query->whereHas('jadwal', function($q) use ($request) {
                $q->where('kitab_id', $request->kitab_id);
            });
        }
        if ($request->filled('pengampu_id')) {
            $query->where('pengampu_id', $request->pengampu_id);
        }
        if ($request->filled('tipe')) {
            $query->where('tipe_file', $request->tipe);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        
        $dokumentasis = $query->latest()->paginate(12)->withQueryString();

        $tahunList = Dokumentasi::selectRaw('YEAR(tanggal) as tahun')
                        ->distinct()
                        ->orderByDesc('tahun')
                        ->pluck('tahun');

        return view('admin.dokumentasi.index', compact('dokumentasis', 'jadwals', 'majeliss', 'kitabs', 'ustadzs', 'tahunList'));
    }

    public function create()
    {
        $jadwals = Jadwal::with(['kitab', 'majelis', 'ustadz.user'])->get();
        return view('admin.dokumentasi.create', compact('jadwals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required|string|max:255',
            'tanggal'     => 'required|date',
            'majelis_id'  => 'nullable|exists:majelis,id',
            'kitab_id'    => 'nullable|exists:kitabs,id',
            'pengampu_id' => 'nullable|exists:ustadzs,id',
            'upload_file' => 'required|file|max:20480',
        ]);

        $file     = $request->file('upload_file');
        $ext      = $file->getClientOriginalExtension();
        $tipe     = $this->detectType($ext);
        $folder   = $tipe === 'image' ? 'dokumentasi/images' : 'dokumentasi/files';
        $path     = $file->store($folder, 'public');
        $namaFile = $file->getClientOriginalName();

        $jadwal = Jadwal::where('majelis_id', $request->majelis_id)
                        ->where('kitab_id', $request->kitab_id)
                        ->where('ustadz_id', $request->pengampu_id)
                        ->first();

        Dokumentasi::create([
            'judul'       => $request->judul,
            'deskripsi'   => $request->deskripsi,
            'tanggal'     => $request->tanggal,
            'majelis_id'  => $request->majelis_id,
            'kitab_id'    => $request->kitab_id,
            'jadwal_id'   => $jadwal ? $jadwal->id : null,
            'upload_file' => $path,
            'tipe_file'   => $tipe,
            'nama_file'   => $namaFile,
            'pengampu_id' => $request->pengampu_id,
        ]);

        return redirect()->route('admin.dokumentasi.index')
                         ->with('success', 'Berkas berhasil diupload.');
    }

    public function edit(Dokumentasi $dokumentasi)
    {
        $jadwals = Jadwal::with(['kitab', 'majelis', 'ustadz.user'])->get();
        $ustadzs = Ustadz::join('users', 'users.id', '=', 'ustadzs.user_id')
                        ->orderBy('users.name')
                        ->select('ustadzs.*', 'users.name as nama')
                        ->get();
        return view('admin.dokumentasi.edit', compact('dokumentasi', 'jadwals', 'ustadzs'));
    }

    public function update(Request $request, Dokumentasi $dokumentasi)
    {
        $request->validate([
            'judul'       => 'required|string|max:255',
            'tanggal'     => 'required|date',
            'majelis_id'  => 'nullable|exists:majelis,id',
            'kitab_id'    => 'nullable|exists:kitabs,id',
            'pengampu_id' => 'nullable|exists:ustadzs,id',
        ]);

        $data = $request->only('judul', 'deskripsi', 'tanggal');
        $data['majelis_id'] = $request->majelis_id;
        $data['kitab_id'] = $request->kitab_id;
        $data['pengampu_id'] = $request->pengampu_id;

        $jadwal = Jadwal::where('majelis_id', $request->majelis_id)
                        ->where('kitab_id', $request->kitab_id)
                        ->where('ustadz_id', $request->pengampu_id)
                        ->first();
        
        $data['jadwal_id'] = $jadwal ? $jadwal->id : null;

        if ($request->hasFile('upload_file')) {
            $request->validate(['upload_file' => 'file|max:20480']);
            if ($dokumentasi->upload_file) {
                Storage::disk('public')->delete($dokumentasi->upload_file);
            }
            $file             = $request->file('upload_file');
            $ext              = $file->getClientOriginalExtension();
            $tipe             = $this->detectType($ext);
            $folder           = $tipe === 'image' ? 'dokumentasi/images' : 'dokumentasi/files';
            $data['upload_file'] = $file->store($folder, 'public');
            $data['tipe_file']   = $tipe;
            $data['nama_file']   = $file->getClientOriginalName();
        }

        $dokumentasi->update($data);

        return redirect()->route('admin.dokumentasi.index')
                         ->with('success', 'Berkas berhasil diperbarui.');
    }

    public function destroy(Dokumentasi $dokumentasi)
    {
        Storage::disk('public')->delete($dokumentasi->upload_file);
        $dokumentasi->delete();
        return back()->with('success', 'Berkas berhasil dihapus.');
    }
}
