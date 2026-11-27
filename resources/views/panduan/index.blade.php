@extends('layouts.app')

@section('title', 'Panduan Penggunaan')
@section('header_title', 'Panduan Penggunaan')
@section('header_subtitle', 'Tata cara pembuatan Surat Undangan, Suket Lupa Absen, pengaturan kop, dan pengelolaan database pegawai')

@push('styles')
<style>
    .guide-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 28px 32px;
        box-shadow: var(--shadow-sm);
    }

    .guide-section + .guide-section {
        margin-top: 26px;
        padding-top: 22px;
        border-top: 1px solid var(--border);
    }

    .guide-heading {
        font-size: 15px;
        font-weight: 700;
        color: #164e82;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .guide-heading .ui-icon {
        width: 17px;
        height: 17px;
        color: #164e82;
        flex-shrink: 0;
    }

    .guide-list,
    .guide-ol {
        margin: 0;
        padding-left: 22px;
        color: #374151;
        font-size: 13.75px;
        line-height: 1.7;
    }

    .guide-list li + li,
    .guide-ol li + li {
        margin-top: 6px;
    }

    .guide-kbd {
        display: inline-block;
        padding: 1px 6px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 12px;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 5px;
        color: #1f2937;
    }

    .guide-actions {
        margin-top: 26px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .guide-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border);
        background: #ffffff;
        color: var(--text-main);
        transition: all 0.16s ease;
    }

    .guide-btn:hover {
        border-color: var(--border-hover);
        background: var(--bg-subtle);
    }

    .guide-btn-primary {
        background: var(--pu-navy);
        border-color: var(--pu-navy);
        color: #ffffff;
    }

    .guide-btn-primary:hover {
        background: var(--pu-navy-light);
        border-color: var(--pu-navy-light);
    }

    @media (max-width: 640px) {
        .guide-card {
            padding: 20px 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="guide-card">

    <div class="guide-section">
        <h2 class="guide-heading">
            <x-icon name="book-open" />
            <span>Cara pakai</span>
        </h2>
        <ol class="guide-ol">
            <li>Pilih jenis dokumen di menu samping: <strong>Buat Surat Undangan</strong> atau <strong>Suket Lupa Absen</strong>. Isian setiap surat tersimpan terpisah di sistem.</li>
            <li>Kolom pegawai: ketik sebagian nama atau jabatan di kotak cari lalu pilih — isian NIP, pangkat/golongan, dan jabatan terisi otomatis dan tetap bisa diedit sebelum disimpan.</li>
            <li>Setelah disimpan, klik <strong>Unduh Word (.docx)</strong> atau <strong>Cetak / PDF</strong>. Surat undangan berisi halaman Konsep, Paraf, Surat Asli, dan Lampiran (bila Yth. menggunakan opsi <em>Daftar Terlampir</em>).</li>
            <li>Surat dapat disimpan sebagai <strong>Draft</strong> maupun <strong>Final</strong>, dan seluruh arsip tersimpan otomatis di database server pada menu <strong>Arsip Surat Undangan</strong> dan <strong>Arsip Suket Absen</strong>.</li>
        </ol>
    </div>

    <div class="guide-section">
        <h2 class="guide-heading">
            <x-icon name="file-text" />
            <span>Kop surat</span>
        </h2>
        <ul class="guide-list">
            <li>Semua kop memakai format seragam: logo PU + 4 baris teks Arial — baris 1 (18 pt), baris 2 (12 pt), baris 3 nama unit/satker (8 pt mengecil otomatis sampai 6 pt), dan baris 4 alamat (7,5 pt).</li>
            <li>Pilihan kop pada Surat Undangan: <strong>Kop Balai</strong>, <strong>Kop Satker O&amp;P SDA</strong>, <strong>Kop Satker SNVT PJPA</strong>, <strong>Kop Satker SNVT PJSA</strong>, dan <strong>Kop Satker BWS</strong>. Teks baris nama instansi dan alamat dapat diubah per surat lewat bagian <em>&ldquo;Ubah teks kop&rdquo;</em>.</li>
            <li>Pengaturan bawaan kop dan penandatangan instansi dapat disesuaikan secara global melalui menu <strong>Pengaturan Instansi</strong>.</li>
            <li>Suket Lupa Absen selalu memakai <strong>Kop Balai</strong>.</li>
        </ul>
    </div>

    <div class="guide-section">
        <h2 class="guide-heading">
            <x-icon name="database" />
            <span>Database</span>
        </h2>
        <ul class="guide-list">
            <li>Database awal memuat data pegawai BWS Kalimantan I. Pegawai yang memiliki <strong>Jabatan Rangkap</strong> muncul dua kali di kotak cari: jabatan utama dan jabatan rangkap (ditandai <code>[rangkap]</code>).</li>
            <li>Kolom opsional <em>Nama di Surat</em>, <em>Jabatan di Surat</em>, dan <em>Rangkap di Surat</em> otomatis dipakai pada generator surat bila penulisan di surat berbeda dari SK.</li>
            <li>Data tersimpan terpusat di database server sehingga bisa diakses dari perangkat mana saja setelah login, serta mendukung <strong>Impor CSV</strong> dan <strong>Ekspor Excel</strong> pada menu <strong>Database Pegawai</strong>.</li>
            <li>Data surat atau pegawai yang dihapus masuk terlebih dahulu ke menu <strong>Riwayat Penghapusan</strong> dan dapat dipulihkan kembali atau dihapus permanen.</li>
        </ul>
    </div>

    <div class="guide-section">
        <h2 class="guide-heading">
            <x-icon name="info" />
            <span>Catatan</span>
        </h2>
        <ul class="guide-list">
            <li>Kop pada surat asli/paraf hanya muncul di halaman pertama tiap bagian. Halaman lampiran tampil tanpa kop.</li>
            <li>Gunakan kata sandi minimal 8 karakter (kombinasi huruf dan angka) yang dapat diperbarui melalui menu <strong>Profil &amp; Kata Sandi</strong>.</li>
        </ul>
    </div>

    <div class="guide-actions">
        <a href="{{ route('surat.undangan.create') }}" class="guide-btn guide-btn-primary">
            <x-icon name="mail" class="ui-icon-sm" />
            <span>Buat Surat Undangan</span>
        </a>
        <a href="{{ route('surat.suket.create') }}" class="guide-btn">
            <x-icon name="file-text" class="ui-icon-sm" />
            <span>Buat Suket Lupa Absen</span>
        </a>
        <a href="{{ route('pegawai.index') }}" class="guide-btn">
            <x-icon name="users" class="ui-icon-sm" />
            <span>Database Pegawai</span>
        </a>
    </div>

</div>
@endsection
