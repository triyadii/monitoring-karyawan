<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisKegiatan;
use Illuminate\Http\Request;

class JenisKegiatanController extends Controller
{
    public function index()
    {
        return response()->json(JenisKegiatan::orderBy('nama_jenis_kegiatan', 'asc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis_kegiatan' => 'required|string|max:255',
        ]);

        $jenisKegiatan = JenisKegiatan::create($validated);

        return response()->json($jenisKegiatan, 201);
    }

    public function show($id)
    {
        $jenisKegiatan = JenisKegiatan::findOrFail($id);

        return response()->json($jenisKegiatan);
    }

    public function update(Request $request, $id)
    {
        $jenisKegiatan = JenisKegiatan::findOrFail($id);

        $validated = $request->validate([
            'nama_jenis_kegiatan' => 'required|string|max:255',
        ]);

        $jenisKegiatan->update($validated);

        return response()->json($jenisKegiatan);
    }

    public function destroy($id)
    {
        $jenisKegiatan = JenisKegiatan::findOrFail($id);
        $jenisKegiatan->delete();

        return response()->json(['message' => 'Jenis Kegiatan deleted successfully']);
    }
}
