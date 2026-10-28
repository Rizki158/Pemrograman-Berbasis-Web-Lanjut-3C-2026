<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BukuController;

// Resource API Buku
Route::apiResource('buku', BukuController::class);

// Route Rekap Agregasi
Route::get('/rekap/buku-per-kategori', [BukuController::class, 'bukuPerKategori']);
Route::get('/rekap/peminjaman-per-buku', [BukuController::class, 'peminjamanPerBuku']);