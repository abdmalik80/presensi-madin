<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Dokumentasi;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumentasiController extends Controller
{
    /** Deteksi tipe file dari ekstensi */
    private function detectType(string $ext): string
    {
        $ext = strtolower($ext);
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']))  return 'image';
        if ($ext === 'pdf')                                                   return 'pdf';
        if (in_array($ext, ['doc', 'docx']))                                  return 'doc';
        if (in_array($ext, ['xls', 'xlsx']))                                  return 'xls';
        if (in_array($ext, ['ppt', 'pptx']))                                  return 'ppt';
        return 'other';
    }

    public function index(Request $request)
    {
        $ustadz  = auth()->user()->ustadz;
        $jadwals = Jadwal::where('ustadz_id', $ustadz->id)->with(['kitab','majelis'])->get();

        $query = Dokumentasi::with(['majelis', 'jadwal.kitab', 'kitab'])
            ->where(function($q) use ($ustadz) {
                $q->where('pengampu_id', $ustadz->id)
                  ->orWhereNull('pengampu_id');
            });

        // Filter by majelis
        if ($request->filled('majelis_id')) {
            $query->where('majelis_id', $request->majelis_id);
        }
        // Filter by kitab
        if ($request->filled('kitab_id')) {
            $query->whereHas('jadwal', function($q) use ($request) {
                $q->where('kitab_id', $request->kitab_id);
            });
        }
        // Filter by tipe
        if ($request->filled('tipe')) {
            $query->where('tipe_file', $request->tipe);
        }
        // Filter by bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $dokumentasis = $query->latest()->paginate(12)->withQueryString();

        // Daftar tahun untuk dropdown
        $tahunList = Dokumentasi::where(function($q) use ($ustadz) {
                            $q->where('pengampu_id', $ustadz->id)
                              ->orWhereNull('pengampu_id');
                        })
                        ->selectRaw('YEAR(tanggal) as tahun')
                        ->distinct()
                        ->orderByDesc('tahun')
                        ->pluck('tahun');

        $majelisOptions = [];
        $kitabOptions = [];
        $majelisKitabMap = [];
        $kitabMajelisMap = [];
        foreach($jadwals as $j) {
            if($j->majelis) $majelisOptions[$j->majelis_id] = $j->majelis->nama_majelis;
            if($j->kitab) $kitabOptions[$j->kitab_id] = $j->kitab->nama_kitab;
            
            if ($j->majelis_id && $j->kitab_id) {
                $majelisKitabMap[$j->majelis_id][] = $j->kitab_id;
                $kitabMajelisMap[$j->kitab_id][] = $j->majelis_id;
            }
        }

        foreach($majelisKitabMap as $mId => $kIds) {
            $majelisKitabMap[$mId] = array_values(array_unique($kIds));
        }
        foreach($kitabMajelisMap as $kId => $mIds) {
            $kitabMajelisMap[$kId] = array_values(array_unique($mIds));
        }

        return view('ustadz.dokumentasi.index', compact('dokumentasis', 'jadwals', 'tahunList', 'majelisOptions', 'kitabOptions', 'majelisKitabMap', 'kitabMajelisMap'));
    }

    public function create(Request $request)
    {
        $ustadz  = auth()->user()->ustadz;
        $jadwals = Jadwal::where('ustadz_id', $ustadz->id)->with(['kitab','majelis'])->get();
        $selectedJadwal = $request->jadwal_id;

        $majelisOptions = [];
        $kitabOptions = [];
        $majelisKitabMap = [];
        $kitabMajelisMap = [];
        foreach($jadwals as $j) {
            if($j->majelis) $majelisOptions[$j->majelis_id] = $j->majelis->nama_majelis;
            if($j->kitab) $kitabOptions[$j->kitab_id] = $j->kitab->nama_kitab;
            
            if ($j->majelis_id && $j->kitab_id) {
                $majelisKitabMap[$j->majelis_id][] = $j->kitab_id;
                $kitabMajelisMap[$j->kitab_id][] = $j->majelis_id;
            }
        }

        foreach($majelisKitabMap as $mId => $kIds) {
            $majelisKitabMap[$mId] = array_values(array_unique($kIds));
        }
        foreach($kitabMajelisMap as $kId => $mIds) {
            $kitabMajelisMap[$kId] = array_values(array_unique($mIds));
        }

        return view('ustadz.dokumentasi.create', compact('jadwals', 'selectedJadwal', 'majelisOptions', 'kitabOptions', 'majelisKitabMap', 'kitabMajelisMap'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'      => 'required|string|max:255',
            'tanggal'    => 'required|date',
            'majelis_id' => 'required|exists:majelis,id',
            'kitab_id'   => 'required|exists:kitabs,id',
            'upload_file'=> 'required|file|max:20480', // max 20MB
        ]);

        $ustadz   = auth()->user()->ustadz;
        
        $jadwal = Jadwal::where('ustadz_id', $ustadz->id)
                        ->where('majelis_id', $request->majelis_id)
                        ->where('kitab_id', $request->kitab_id)
                        ->first();

        if (!$jadwal) {
            return back()->withErrors(['error' => 'Kombinasi Majelis dan Kitab tidak valid untuk jadwal Anda.'])->withInput();
        }

        $file     = $request->file('upload_file');
        $ext      = $file->getClientOriginalExtension();
        $tipe     = $this->detectType($ext);
        $folder   = $tipe === 'image' ? 'dokumentasi/images' : 'dokumentasi/files';
        $path     = $file->store($folder, 'public');
        $namaFile = $file->getClientOriginalName();

        Dokumentasi::create([
            'judul'       => $request->judul,
            'deskripsi'   => $request->deskripsi,
            'tanggal'     => $request->tanggal,
            'majelis_id'  => $jadwal->majelis_id,
            'jadwal_id'   => $jadwal->id,
            'upload_file' => $path,
            'tipe_file'   => $tipe,
            'nama_file'   => $namaFile,
            'pengampu_id' => $ustadz->id,
        ]);

        // Redirect ke detail jadwal jika dari sana (walaupun input jadwal_id ganti majelis_id/kitab_id, kita cek dari HTTP Referer jika perlu. Tapi aman aja balik ke index)
        return redirect()->route('ustadz.dokumentasi.index')
                         ->with('success', 'Berkas berhasil diupload.');
    }

    public function edit(Dokumentasi $dokumentasi)
    {
        $ustadz  = auth()->user()->ustadz;
        $jadwals = Jadwal::where('ustadz_id', $ustadz->id)->with(['kitab','majelis'])->get();
        
        $majelisOptions = [];
        $kitabOptions = [];
        $majelisKitabMap = [];
        $kitabMajelisMap = [];
        foreach($jadwals as $j) {
            if($j->majelis) $majelisOptions[$j->majelis_id] = $j->majelis->nama_majelis;
            if($j->kitab) $kitabOptions[$j->kitab_id] = $j->kitab->nama_kitab;
            
            if ($j->majelis_id && $j->kitab_id) {
                $majelisKitabMap[$j->majelis_id][] = $j->kitab_id;
                $kitabMajelisMap[$j->kitab_id][] = $j->majelis_id;
            }
        }

        foreach($majelisKitabMap as $mId => $kIds) {
            $majelisKitabMap[$mId] = array_values(array_unique($kIds));
        }
        foreach($kitabMajelisMap as $kId => $mIds) {
            $kitabMajelisMap[$kId] = array_values(array_unique($mIds));
        }

        return view('ustadz.dokumentasi.edit', compact('dokumentasi', 'jadwals', 'majelisOptions', 'kitabOptions', 'majelisKitabMap', 'kitabMajelisMap'));
    }

    public function update(Request $request, Dokumentasi $dokumentasi)
    {
        $request->validate([
            'judul'      => 'required|string|max:255',
            'tanggal'    => 'required|date',
            'majelis_id' => 'required|exists:majelis,id',
            'kitab_id'   => 'required|exists:kitabs,id',
        ]);

        $ustadz = auth()->user()->ustadz;
        $jadwal = Jadwal::where('ustadz_id', $ustadz->id)
                        ->where('majelis_id', $request->majelis_id)
                        ->where('kitab_id', $request->kitab_id)
                        ->first();

        if (!$jadwal) {
            return back()->withErrors(['error' => 'Kombinasi Majelis dan Kitab tidak valid untuk jadwal Anda.'])->withInput();
        }

        $data = $request->only('judul', 'deskripsi', 'tanggal');
        $data['majelis_id'] = $jadwal->majelis_id;
        $data['jadwal_id']  = $jadwal->id;

        if ($request->hasFile('upload_file')) {
            Storage::disk('public')->delete($dokumentasi->upload_file);
            $file               = $request->file('upload_file');
            $ext                = $file->getClientOriginalExtension();
            $tipe               = $this->detectType($ext);
            $folder             = $tipe === 'image' ? 'dokumentasi/images' : 'dokumentasi/files';
            $data['upload_file'] = $file->store($folder, 'public');
            $data['tipe_file']   = $tipe;
            $data['nama_file']   = $file->getClientOriginalName();
        }

        $dokumentasi->update($data);

        return redirect()->route('ustadz.dokumentasi.index')->with('success', 'Berkas diperbarui.');
    }

    public function destroy(Dokumentasi $dokumentasi)
    {
        Storage::disk('public')->delete($dokumentasi->upload_file);
        $dokumentasi->delete();
        return back()->with('success', 'Berkas dihapus.');
    }

    public function show(Dokumentasi $dokumentasi)
    {
        return view('ustadz.dokumentasi.show', compact('dokumentasi'));
    }
}
