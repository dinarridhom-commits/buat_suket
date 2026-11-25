@extends('layouts.app')

@section('title', 'Arsip Suket Lupa Absen')
@section('header_title', 'Arsip Surat Keterangan Lupa Absen')
@section('header_subtitle', 'Rekap Suket Lupa Absen Masuk / Pulang Pegawai BWS Kalimantan I')

@push('styles')
<style>
    .badge-jenis-masuk  { background:#eff6ff; color:#1d4ed8; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; }
    .badge-jenis-pulang { background:#fffbeb; color:#b45309; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; }
</style>
@endpush

@section('content')

<div class="panel-card">
    <div class="panel-head">
        <h3>Daftar Suket Lupa Absen</h3>
        <a href="{{ route('surat.suket.create') }}" class="btn-quick-create">+ Buat Suket Baru</a>
    </div>
    <div class="panel-body" style="padding: 0;">
        @if($sukets->count() > 0)
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Nomor Suket</th>
                        <th>Jenis Absen</th>
                        <th>Nama Pegawai</th>
                        <th>NIP</th>
                        <th>Tanggal Kejadian</th>
                        <th>Waktu</th>
                        <th>Atasan Penyetuju</th>
                        <th style="text-align:center; width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sukets as $sk)
                        <tr>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 600;">
                                {{ $sk->nomor_surat }}
                            </td>
                            <td>
                                <span class="badge-jenis-{{ $sk->jenis_absen }}">
                                    Lupa Absen {{ ucfirst($sk->jenis_absen) }}
                                </span>
                            </td>
                            <td style="font-weight: 600;">{{ $sk->nama_pegawai }}</td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px;">{{ $sk->nip_pegawai ?? '-' }}</td>
                            <td>{{ $sk->tanggal_kejadian->format('d/m/Y') }}</td>
                            <td>{{ $sk->waktu_kejadian }} WIB</td>
                            <td>{{ $sk->atasan_nama }}</td>
                            <td style="text-align:center;">
                                <div style="display:inline-flex; gap:6px; align-items:center;">
                                    <a href="{{ route('surat.suket.show', $sk->id) }}"
                                       style="padding:5px 9px; border-radius:4px; border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; font-size:11.5px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                        👁️ Lihat
                                    </a>
                                    <form method="POST" action="{{ route('surat.suket.destroy', $sk->id) }}"
                                          onsubmit="return confirm('Hapus arsip suket {{ addslashes($sk->nama_pegawai) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                style="padding:5px 9px; border-radius:4px; border:1px solid #fecaca; background:#fff5f5; color:#dc2626; font-size:11.5px; font-weight:600; cursor:pointer;">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding: 16px 20px;">
                {{ $sukets->links() }}
            </div>
        @else
            <div class="empty-state">
                <span>📝</span>
                <p style="font-weight: 600;">Belum ada arsip Suket Lupa Absen di database.</p>
                <a href="{{ route('surat.suket.create') }}" class="btn-quick-create" style="margin-top: 14px;">+ Buat Suket Lupa Absen</a>
            </div>
        @endif
    </div>
</div>
@endsection
