<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with('kategori');

        // Fitur Pencarian (Judul / Penulis)
        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function ($k) use ($q) {
                $k->where('judul', 'like', "%{$q}%")
                  ->orWhere('penulis', 'like', "%{$q}%");
            });
        }

        // Filter Kategori
        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter Tahun
        if ($request->has('tahun')) {
            $query->where('tahun_terbit', $request->tahun);
        }

        $buku = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data'   => $buku
        ], 200);
    }

    public function store(StoreBukuRequest $request)
    {
        $buku = Buku::create($request->validated());

        return response()->json([
            'message' => 'Buku berhasil ditambahkan.',
            'data'    => $buku
        ], 201);
    }

    public function show($id)
    {
        $buku = Buku::with('kategori')->find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $buku], 200);
    }

    public function update(UpdateBukuRequest $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan.'], 404);
        }

        $buku->update($request->validated());

        return response()->json([
            'message' => 'Buku berhasil diperbarui.',
            'data'    => $buku
        ], 200);
    }

    public function destroy($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan.'], 404);
        }

        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus.'], 200);
    }

    // Rekap Agregasi 1: Jumlah buku per kategori
    public function bukuPerKategori()
    {
        $rekap = Kategori::withCount('bukus')->get();

        return response()->json(['data' => $rekap], 200);
    }

    // Rekap Agregasi 2: Rekap daftar peminjaman per buku
    public function peminjamanPerBuku()
    {
        $rekap = Buku::select('id', 'judul')->get();

        return response()->json(['data' => $rekap], 200);
    }
}