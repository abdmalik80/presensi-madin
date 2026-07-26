<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ustadz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UstadzController extends Controller
{
    public function index(Request $request)
    {
        $query = Ustadz::with('user');
        if ($request->search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->filled('status_aktif')) {
            $query->whereHas('user', fn($q) => $q->where('status_aktif', $request->status_aktif));
        }
        $ustadzs = $query->latest()->paginate(10);
        return view('admin.ustadz.index', compact('ustadzs'));
    }

    public function create()
    {
        return view('admin.ustadz.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'username'     => 'required|string|unique:users',
            'password'     => 'required|string|min:6',
            'jenis_kelamin'=> 'nullable|in:L,P',
            'foto_profil'  => 'nullable|image|max:2048',
        ]);

        $foto = null;
        if ($request->hasFile('foto_profil')) {
            $foto = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        $user = User::create([
            'name'         => $request->name,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'role'         => 'ustadz',
            'jenis_kelamin'=> $request->jenis_kelamin ?: null,
            'no_hp'        => $request->no_hp ?: null,
            'alamat'       => $request->alamat ?: null,
            'foto_profil'  => $foto,
            'status_aktif' => $request->input('status_aktif') == '1',
        ]);

        $user->ustadz()->create();

        return redirect()->route('admin.ustadz.index')->with('success', 'Data ustadz berhasil ditambahkan.');
    }

    public function edit(Ustadz $ustadz)
    {
        return view('admin.ustadz.edit', compact('ustadz'));
    }

    public function update(Request $request, Ustadz $ustadz)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'username'     => 'required|string|unique:users,username,'.$ustadz->user_id,
            'jenis_kelamin'=> 'nullable|in:L,P',
            'foto_profil'  => 'nullable|image|max:2048',
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
            if ($ustadz->user->foto_profil) Storage::disk('public')->delete($ustadz->user->foto_profil);
            $userData['foto_profil'] = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        $ustadz->user->update($userData);

        return redirect()->route('admin.ustadz.index')->with('success', 'Data ustadz berhasil diperbarui.');
    }

    public function destroy(Ustadz $ustadz)
    {
        if ($ustadz->user->foto_profil) Storage::disk('public')->delete($ustadz->user->foto_profil);
        $ustadz->user->delete();
        return redirect()->route('admin.ustadz.index')->with('success', 'Data ustadz berhasil dihapus.');
    }

    public function show(Ustadz $ustadz)
    {
        $ustadz->load(['user', 'jadwals.majelis', 'jadwals.kitab']);
        return view('admin.ustadz.show', compact('ustadz'));
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('admin.ustadz.index')->with('error', 'Tidak ada ustadz yang dipilih.');
        }

        $ustadzs = Ustadz::with('user')->whereIn('id', $ids)->get();
        $count   = $ustadzs->count();

        if ($action === 'delete') {
            foreach ($ustadzs as $ustadz) {
                if ($ustadz->user?->foto_profil) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($ustadz->user->foto_profil);
                }
                $ustadz->user?->delete();
            }
            return redirect()->route('admin.ustadz.index')->with('success', "{$count} data ustadz berhasil dihapus.");
        } elseif ($action === 'active') {
            foreach ($ustadzs as $ustadz) {
                if ($ustadz->user) {
                    $ustadz->user->update(['status_aktif' => true]);
                }
            }
            return redirect()->route('admin.ustadz.index')->with('success', "{$count} ustadz berhasil diaktifkan.");
        } elseif ($action === 'inactive') {
            foreach ($ustadzs as $ustadz) {
                if ($ustadz->user) {
                    $ustadz->user->update(['status_aktif' => false]);
                }
            }
            return redirect()->route('admin.ustadz.index')->with('success', "{$count} ustadz berhasil dinonaktifkan.");
        }

        return redirect()->route('admin.ustadz.index')->with('error', 'Aksi tidak valid.');
    }
}