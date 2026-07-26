<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\User;
use App\Models\Majelis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $query = Santri::with(['user', 'majelis']);
        if ($request->search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->majelis_id) {
            $query->where('majelis_id', $request->majelis_id);
        }
        $allIds  = (clone $query)->pluck('id')->toArray(); // semua ID hasil filter (tanpa paginasi)
        $santris = $query->orderBy('id', 'asc')->paginate(10);
        $majelisList = Majelis::all();

        $nisList = $santris->pluck('nis')->filter()->toArray();
        $duplicateNis = [];
        if (!empty($nisList)) {
            $duplicateNis = Santri::select('nis')
                ->whereIn('nis', $nisList)
                ->groupBy('nis')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('nis')
                ->toArray();
        }

        return view('admin.santri.index', compact('santris', 'majelisList', 'duplicateNis', 'allIds'));
    }

    public function create()
    {
        $majelis = Majelis::all();
        return view('admin.santri.create', compact('majelis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|unique:users',
            'password'   => 'required|string|min:6',
            'nis'        => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'majelis_id' => 'nullable|exists:majelis,id',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        $foto = null;
        if ($request->hasFile('foto_profil')) {
            $foto = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        $user = User::create([
            'name'        => $request->name,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'role'        => 'santri',
            'jenis_kelamin' => $request->jenis_kelamin ?: null,
            'no_hp'       => $request->no_hp ?: null,
            'alamat'      => $request->alamat ?: null,
            'foto_profil' => $foto,
            'status_aktif' => $request->input('status_aktif') == '1',
        ]);

        $user->santri()->create([
            'nis'            => $request->nis,
            'tempat_lahir'   => $request->tempat_lahir,
            'tanggal_lahir'  => $request->tanggal_lahir,
            'nama_wali'      => $request->nama_wali,
            'no_hp_wali'     => $request->no_hp_wali,
            'angkatan'       => $request->angkatan,
            'majelis_id'     => $request->majelis_id,
        ]);

        return redirect()->route('admin.santri.index')->with('success', 'Data santri berhasil ditambahkan.');
    }

    public function show(Santri $santri)
    {
        $santri->load(['user', 'majelis', 'detailPresensis.presensi.jadwal']);
        return view('admin.santri.show', compact('santri'));
    }

    public function edit(Santri $santri)
    {
        $majelis = Majelis::all();
        return view('admin.santri.edit', compact('santri', 'majelis'));
    }

    public function update(Request $request, Santri $santri)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|unique:users,username,'.$santri->user_id,
            'nis'        => 'nullable|string',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        $userData = [
            'name'         => $request->name,
            'username'     => $request->username,
            'jenis_kelamin'=> $request->jenis_kelamin ?: null,
            'no_hp'        => $request->no_hp ?: null,
            'alamat'       => $request->alamat ?: null,
            'status_aktif' => $request->has('_status_sent') ? $request->has('status_aktif') : true,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_profil')) {
            if ($santri->user->foto_profil) Storage::disk('public')->delete($santri->user->foto_profil);
            $userData['foto_profil'] = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        $santri->user->update($userData);
        $santri->update([
            'nis'           => $request->nis,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nama_wali'     => $request->nama_wali,
            'no_hp_wali'    => $request->no_hp_wali,
            'angkatan'      => $request->angkatan,
            'majelis_id'    => $request->majelis_id,
        ]);

        return redirect()->route('admin.santri.index')->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy(Santri $santri)
    {
        if ($santri->user->foto_profil) Storage::disk('public')->delete($santri->user->foto_profil);
        $santri->user->delete();
        return redirect()->route('admin.santri.index')->with('success', 'Data santri berhasil dihapus.');
    }

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

    public function template()
    {
        $data = [
            ['nama', 'username', 'password', 'jenis_kelamin', 'no_hp', 'alamat', 'nis', 'tempat_lahir', 'tanggal_lahir', 'nama_wali', 'no_hp_wali', 'angkatan', 'nama_majelis'],
            ['Santri Baru', 'santri.baru', 'password123', 'L', '08123456789', 'Jl. Mawar 1', '101010', 'Jakarta', '2010-01-01', 'Bapak Santri', '0811111111', '2023', 'Majlis 1']
        ];

        return response()->streamDownload(function() use ($data) {
            $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
            $xlsx->saveAs('php://output');
        }, 'template_import_santri.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        \DB::beginTransaction();
        try {
            $rows = [];
            if (in_array($extension, ['csv', 'txt'])) {
                $handle = fopen($file->getPathname(), "r");
                $header = true;
                while ($csvLine = fgetcsv($handle, 1000, ",")) {
                    if ($header) {
                        $header = false;
                        continue;
                    }
                    $rows[] = $csvLine;
                }
                fclose($handle);
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                if ($xlsx = \Shuchkin\SimpleXLSX::parse($file->getPathname())) {
                    $header = true;
                    foreach ($xlsx->rows() as $row) {
                        if ($header) {
                            $header = false;
                            continue;
                        }
                        $rows[] = $row;
                    }
                } else {
                    throw new \Exception(\Shuchkin\SimpleXLSX::parseError());
                }
            }

            foreach ($rows as $rowLine) {
                // Ensure row has at least 13 columns by padding it with nulls
                $rowLine = array_pad($rowLine, 13, null);

                $nama = trim($rowLine[0] ?? '');
                $username = trim($rowLine[1] ?? '');
                $password = trim($rowLine[2] ?? '');

                // Skip if required fields are missing
                if (empty($nama) || empty($username) || empty($password)) {
                    continue;
                }

                // Ensure username is unique
                if (User::where('username', $username)->exists()) {
                    continue;
                }

                $user = User::create([
                    'name'          => $nama,
                    'username'      => $username,
                    'password'      => Hash::make($password),
                    'role'          => 'santri',
                    'jenis_kelamin' => in_array(strtoupper(trim($rowLine[3] ?? '')), ['L','P']) ? strtoupper(trim($rowLine[3])) : null,
                    'no_hp'         => trim($rowLine[4] ?? '') ?: null,
                    'alamat'        => trim($rowLine[5] ?? '') ?: null,
                    'status_aktif'  => true,
                ]);

                $majelisName = trim($rowLine[12] ?? '');
                $majelisId = null;
                if (!empty($majelisName)) {
                    $majelis = \App\Models\Majelis::where('nama_majelis', $majelisName)->first();
                    if ($majelis) {
                        $majelisId = $majelis->id;
                    }
                }

                $user->santri()->create([
                    'nis'           => trim($rowLine[6] ?? '') ?: null,
                    'tempat_lahir'  => trim($rowLine[7] ?? '') ?: null,
                    'tanggal_lahir' => trim($rowLine[8] ?? '') ?: null,
                    'nama_wali'     => trim($rowLine[9] ?? '') ?: null,
                    'no_hp_wali'    => trim($rowLine[10] ?? '') ?: null,
                    'angkatan'      => trim($rowLine[11] ?? '') ?: null,
                    'majelis_id'    => $majelisId,
                ]);
            }
            \DB::commit();
            return redirect()->route('admin.santri.index')->with('success', 'Import data santri berhasil.');
        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Gagal mengimport data. Error: ' . $e->getMessage()]);
        }
    }
}
