<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Majelis;
use Illuminate\Http\Request;

class MajelisController extends Controller
{
    public function index(Request $request)
    {
        $query = Majelis::withCount('santris');
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_majelis', 'like', '%'.$request->search.'%')
                  ->orWhere('koordinator', 'like', '%'.$request->search.'%');
            });
        }
        $majelis = $query->latest()->paginate(10);
        return view('admin.majelis.index', compact('majelis'));
    }

    public function create() { return view('admin.majelis.create'); }

    public function store(Request $request)
    {
        $request->validate(['nama_majelis' => 'required|string|max:255']);
        Majelis::create($request->only('nama_majelis', 'tempat', 'koordinator', 'keterangan'));
        return redirect()->route('admin.majelis.index')->with('success', 'Data majelis berhasil ditambahkan.');
    }

    public function edit(Majelis $majeli) { return view('admin.majelis.edit', ['majelis' => $majeli]); }

    public function update(Request $request, Majelis $majeli)
    {
        $request->validate(['nama_majelis' => 'required|string|max:255']);
        $majeli->update($request->only('nama_majelis', 'tempat', 'koordinator', 'keterangan'));
        return redirect()->route('admin.majelis.index')->with('success', 'Data majelis berhasil diperbarui.');
    }

    public function destroy(Majelis $majeli)
    {
        $majeli->delete();
        return redirect()->route('admin.majelis.index')->with('success', 'Data majelis berhasil dihapus.');
    }

    public function show(Majelis $majeli)
    {
        $majeli->load(['santris.user']);
        return view('admin.majelis.show', ['majelis' => $majeli]);
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('admin.majelis.index')->with('error', 'Tidak ada majelis yang dipilih.');
        }

        $majelis = \App\Models\Majelis::whereIn('id', $ids)->get();
        $count   = $majelis->count();

        if ($action === 'delete') {
            foreach ($majelis as $m) {
                $m->delete();
            }
            return redirect()->route('admin.majelis.index')->with('success', "{$count} data majelis berhasil dihapus.");
        }

        return redirect()->route('admin.majelis.index')->with('error', 'Aksi tidak valid.');
    }
}