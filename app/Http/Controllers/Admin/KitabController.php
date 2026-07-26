<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kitab;
use Illuminate\Http\Request;

class KitabController extends Controller
{
    public function index(Request $request)
    {
        $query = Kitab::query();
        if ($request->search) {
            $query->where('nama_kitab', 'like', '%'.$request->search.'%');
        }
        if ($request->bidang_ilmu) {
            $query->where('bidang_ilmu', $request->bidang_ilmu);
        }
        $kitabs = $query->latest()->paginate(10);
        $bidangIlmuList = Kitab::select('bidang_ilmu')->whereNotNull('bidang_ilmu')->distinct()->pluck('bidang_ilmu');
        return view('admin.kitab.index', compact('kitabs', 'bidangIlmuList'));
    }

    public function create() { return view('admin.kitab.create'); }

    public function store(Request $request)
    {
        $request->validate(['nama_kitab' => 'required|string|max:255']);
        Kitab::create($request->only('nama_kitab', 'bidang_ilmu', 'pengarang'));
        return redirect()->route('admin.kitab.index')->with('success', 'Data kitab berhasil ditambahkan.');
    }

    public function edit(Kitab $kitab) { return view('admin.kitab.edit', compact('kitab')); }

    public function update(Request $request, Kitab $kitab)
    {
        $request->validate(['nama_kitab' => 'required|string|max:255']);
        $kitab->update($request->only('nama_kitab', 'bidang_ilmu', 'pengarang'));
        return redirect()->route('admin.kitab.index')->with('success', 'Data kitab berhasil diperbarui.');
    }

    public function destroy(Kitab $kitab)
    {
        $kitab->delete();
        return redirect()->route('admin.kitab.index')->with('success', 'Data kitab berhasil dihapus.');
    }

    public function show(Kitab $kitab)
    {
        $kitab->load(['jadwals.majelis', 'jadwals.ustadz.user']);
        return view('admin.kitab.show', compact('kitab'));
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('admin.kitab.index')->with('error', 'Tidak ada kitab yang dipilih.');
        }

        $kitabs = \App\Models\Kitab::whereIn('id', $ids)->get();
        $count  = $kitabs->count();

        if ($action === 'delete') {
            foreach ($kitabs as $k) {
                $k->delete();
            }
            return redirect()->route('admin.kitab.index')->with('success', "{$count} data kitab berhasil dihapus.");
        }

        return redirect()->route('admin.kitab.index')->with('error', 'Aksi tidak valid.');
    }
}