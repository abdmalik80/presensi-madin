<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $pengguna = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pengguna.index', compact('pengguna'));
    }

    public function create()
    {
        return view('admin.pengguna.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'username'      => 'required|string|max:100|unique:users',
            'email'         => 'nullable|email|unique:users',
            'password'      => 'required|string|min:6|confirmed',
            'role'          => 'required|in:admin,ustadz,santri',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
            'foto_profil'   => 'nullable|image|max:2048',
            'status_aktif'  => 'nullable|boolean',
        ]);

        $foto = null;
        if ($request->hasFile('foto_profil')) {
            $foto = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        $user = User::create([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
            'foto_profil'   => $foto,
            'status_aktif'  => $request->input('status_aktif') == '1',
        ]);

        // Otomatis buat record profile sesuai role
        if ($request->role === 'ustadz') {
            $user->ustadz()->create();
        } elseif ($request->role === 'santri') {
            $user->santri()->create();
        }

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $pengguna)
    {
        return view('admin.pengguna.show', compact('pengguna'));
    }

    public function edit(User $pengguna)
    {
        return view('admin.pengguna.edit', compact('pengguna'));
    }

    public function update(Request $request, User $pengguna)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'username'      => ['required', 'string', 'max:100', Rule::unique('users')->ignore($pengguna->id)],
            'email'         => ['nullable', 'email', Rule::unique('users')->ignore($pengguna->id)],
            'password'      => 'nullable|string|min:6|confirmed',
            'role'          => 'required|in:admin,ustadz,santri',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
            'foto_profil'   => 'nullable|image|max:2048',
            'status_aktif'  => 'nullable|boolean',
        ]);

        $data = [
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'role'          => $request->role,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
            'status_aktif'  => $request->has('_status_sent') ? $request->has('status_aktif') : true,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_profil')) {
            if ($pengguna->foto_profil) {
                Storage::disk('public')->delete($pengguna->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        $pengguna->update($data);

        // Jika role diubah, pastikan record profile tersedia
        $pengguna->refresh();
        if ($pengguna->role === 'ustadz' && !$pengguna->ustadz) {
            $pengguna->ustadz()->create();
        } elseif ($pengguna->role === 'santri' && !$pengguna->santri) {
            $pengguna->santri()->create();
        }

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
        }

        if ($pengguna->foto_profil) {
            Storage::disk('public')->delete($pengguna->foto_profil);
        }

        $pengguna->delete();

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }


    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('admin.pengguna.index')->with('error', 'Tidak ada pengguna yang dipilih.');
        }

        $users = User::whereIn('id', $ids)->get();
        $count   = $users->count();

        if ($action === 'delete') {
            foreach ($users as $user) {
                if ($user->foto_profil) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto_profil);
                }
                $user->delete();
            }
            return redirect()->route('admin.pengguna.index')->with('success', "{$count} data pengguna berhasil dihapus.");
        } elseif ($action === 'active') {
            foreach ($users as $user) {
                $user->update(['status_aktif' => true]);
            }
            return redirect()->route('admin.pengguna.index')->with('success', "{$count} pengguna berhasil diaktifkan.");
        } elseif ($action === 'inactive') {
            foreach ($users as $user) {
                $user->update(['status_aktif' => false]);
            }
            return redirect()->route('admin.pengguna.index')->with('success', "{$count} pengguna berhasil dinonaktifkan.");
        }

        return redirect()->route('admin.pengguna.index')->with('error', 'Aksi tidak valid.');
    }
}