<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\QrCodeItem;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function index()
    {
        $riwayatQr = QrCodeItem::latest()->take(15)->get();
        return view('qrcode.index', compact('riwayatQr'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'  => 'required|string|max:255',
            'konten' => 'required|string',
        ], [
            'judul.required'  => 'Judul QR Code wajib diisi.',
            'konten.required' => 'Isi teks atau tautan QR Code tidak boleh kosong.',
        ]);

        QrCodeItem::create([
            'judul'            => $request->judul,
            'konten'           => $request->konten,
            'warna_qr'         => $request->warna_qr ?? '#213366',
            'warna_bg'         => $request->warna_bg ?? '#ffffff',
            'use_logo_pu'      => $request->boolean('use_logo_pu', true),
            'ukuran_logo'      => $request->ukuran_logo ?? 22,
            'error_correction' => $request->error_correction ?? 'H',
        ]);
        ActivityLog::record('create', 'QR Code', "Menyimpan QR Code: {$request->judul}");

        return redirect()->route('qrcode.index')->with('success', 'QR Code berhasil disimpan ke daftar riwayat.');
    }

    public function destroy($id)
    {
        $qr = QrCodeItem::findOrFail($id);
        $judul = $qr->judul;
        $qr->delete();
        ActivityLog::record('delete', 'QR Code', "Menghapus riwayat QR Code: {$judul}");

        return redirect()->route('qrcode.index')->with('success', 'Riwayat QR Code berhasil dihapus.');
    }
}
