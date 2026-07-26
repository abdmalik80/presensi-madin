<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
 use App\Models\ProfileLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user   = $request->user();
        $santri = null;
        $ustadz = null;

        if ($user->role === 'santri') {
            $santri = $user->santri?->load('majelis');
        } elseif ($user->role === 'ustadz') {
            $ustadz = $user->ustadz;
        }

        return view('profile.edit', compact('user', 'santri', 'ustadz'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Upload foto profil
        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('foto-profil', 'public');
            $user->foto_profil = $path;
        }

        $user->save();

        // Update data no_hp & alamat di tabel users (dari form profil)
        $user->update(array_filter([
            'no_hp'  => $request->no_hp,
            'alamat' => $request->alamat,
        ], fn($v) => $v !== null));

        // Jika santri: update nama_wali, no_hp_wali, tempat_lahir, tanggal_lahir
        if ($user->role === 'santri' && $user->santri) {
            $user->santri->update(array_filter([
                'nama_wali'    => $request->nama_wali,
                'no_hp_wali'   => $request->no_hp_wali,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir'=> $request->tanggal_lahir,
            ], fn($v) => $v !== null));
        }

        // Catat log perubahan profil untuk notifikasi admin
        $changedFields = [];
        if ($request->has('name'))  $changedFields[] = 'Nama';
        if ($request->has('email')) $changedFields[] = 'Email';
        if ($request->filled('no_hp'))  $changedFields[] = 'No. HP';
        if ($request->filled('alamat')) $changedFields[] = 'Alamat';
        if ($request->hasFile('foto_profil')) $changedFields[] = 'Foto Profil';
        if (!empty($changedFields)) {
            \App\Models\ProfileLog::create([
                'user_id'    => $user->id,
                'keterangan' => implode(', ', $changedFields),
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    public function updateSantri(Request $request): RedirectResponse
    {
        $user   = $request->user();
        $santri = $user->santri;

        if (!$santri) {
            return back()->with('error', 'Data santri tidak ditemukan.');
        }

        $request->validate([
            'nama_wali'    => 'nullable|string|max:255',
            'no_hp_wali'   => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir'=> 'nullable|date',
        ]);

        $santri->update([
            'nama_wali'    => $request->nama_wali,
            'no_hp_wali'   => $request->no_hp_wali,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir'=> $request->tanggal_lahir,
        ]);

        // Catat log perubahan data santri untuk notifikasi admin
        $changedFields = [];
        if ($request->filled('nama_wali'))     $changedFields[] = 'Nama Wali';
        if ($request->filled('no_hp_wali'))    $changedFields[] = 'No. HP Wali';
        if ($request->filled('tempat_lahir'))  $changedFields[] = 'Tempat Lahir';
        if ($request->filled('tanggal_lahir')) $changedFields[] = 'Tanggal Lahir';
        if (!empty($changedFields)) {
            \App\Models\ProfileLog::create([
                'user_id'    => $user->id,
                'keterangan' => implode(', ', $changedFields),
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Hapus foto profil jika ada
        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
