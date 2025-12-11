<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Borrowing; // PENTING: Kita panggil Model Borrowing
use App\Models\Item;      // PENTING: Kita panggil Model Item (untuk cek stok)
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    // Fungsi untuk memproses data dari Form Peminjaman
    public function store(Request $request)
    {
        // 1. Validasi Input (Biar aman)
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_pinjam',
            'jumlah_pinjam' => 'required|integer|min:1',
            'keterangan' => 'required|string',
        ]);

        // Cek Stok Barang dulu (Opsional tapi bagus)
        $barang = Item::findOrFail($request->item_id);
        if ($barang->stok < $request->jumlah_pinjam) {
            return redirect()->back()->with('error', 'Stok barang tidak cukup!');
        }

        // ==========================================
        // 2. LOGIKA PREPROCESSING (INTI TUGAS)
        // ==========================================
        
        // Ambil input mentah dari user
        $raw_keterangan = $request->input('keterangan');

        // a. Case Folding (Ubah ke huruf kecil semua)
        $clean = strtolower($raw_keterangan);
        
        // b. Cleaning (Hapus simbol aneh, sisakan huruf, angka, spasi)
        $clean = preg_replace('/[^a-z0-9 ]/', '', $clean);
        
        // c. Trim (Hapus spasi berlebih di awal/akhir)
        $clean = trim($clean);

        // ==========================================
        // 3. SIMPAN KE DATABASE
        // ==========================================
        Borrowing::create([
            'user_id' => Auth::id(), // Ambil ID user yang sedang login
            'item_id' => $request->item_id,
            'tgl_pinjam' => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'jumlah_pinjam' => $request->jumlah_pinjam,
            
            // Simpan Dua Versi (Asli & Bersih) untuk laporan tugas
            'keterangan_asli' => $raw_keterangan, 
            'keterangan_clean' => $clean,
            
            'status' => 'pending', // Default status
        ]);

        // Kurangi Stok Barang
        $barang->decrement('stok', $request->jumlah_pinjam);

        return redirect()->back()->with('success', 'Peminjaman berhasil diajukan! Data sudah dipreprocessing.');
    }
    
    // Fungsi untuk melihat halaman hasil preprocessing (Tabel Perbandingan)
    public function history()
    {
        // Ambil data peminjaman milik user yang login saja
        $peminjaman = Borrowing::where('user_id', Auth::id())->get();
        
        // Kirim data ke view (nanti kita buat viewnya)
        return view('peminjaman.history', compact('peminjaman'));
    }
}