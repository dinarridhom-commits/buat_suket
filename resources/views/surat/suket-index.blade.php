@extends('layouts.app')

@section('title', 'Arsip Suket Lupa Absen')
@section('header_title', 'Arsip Surat Keterangan Lupa Absen')
@section('header_subtitle', 'Rekap Suket Lupa Absen Masuk / Pulang Pegawai BWS Kalimantan I')

@push('styles')
<style>
    .badge-jenis-masuk      { background:#eff6ff; color:#1d4ed8; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; }
    .badge-jenis-pulang     { background:#fffbeb; color:#b45309; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; }
    .badge-jenis-terlambat  { background:#fef2f2; color:#b91c1c; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; }
    .badge-jenis-pulangawal { background:#f5f3ff; color:#6d28d9; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; }
</style>
@endpush

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; gap:14px; margin-bottom:18px; flex-wrap:wrap;">
    <form method="GET" action="{{ route('surat.suket.index') }}"
          style="display:flex; align-items:center; gap:10px; flex:1; flex-wrap:wrap; background:var(--bg-surface); padding:12px 16px; border:1px solid var(--border); border-radius:var(--radius-lg); box-shadow:var(--shadow-card);">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor suket, nama pegawai, NIP, atau atasan..."
               style="flex:1; min-width:220px; padding:8px 14px; border:1.5px solid var(--border); border-radius:50px; font-size:13px; background:var(--bg-subtle); outline:none;">
        <select name="jenis_absen" style="padding:8px 14px; border:1.5px solid var(--border); border-radius:50px; font-size:13px; background:var(--bg-subtle); outline:none;">
            <option value="">Semua Jenis Izin</option>
            <option value="masuk" {{ request('jenis_absen') == 'masuk' ? 'selected' : '' }}>Lupa Absen Masuk</option>
            <option value="pulang" {{ request('jenis_absen') == 'pulang' ? 'selected' : '' }}>Lupa Absen Pulang</option>
            <option value="terlambat" {{ request('jenis_absen') == 'terlambat' ? 'selected' : '' }}>Terlambat Masuk</option>
            <option value="pulangawal" {{ request('jenis_absen') == 'pulangawal' ? 'selected' : '' }}>Pulang Sebelum Waktunya</option>
        </select>
        <input type="month" name="bulan" value="{{ request('bulan') }}" title="Filter Bulan Kejadian"
               style="padding:7px 12px; border:1.5px solid var(--border); border-radius:50px; font-size:13px; background:var(--bg-subtle); outline:none;">
        <button type="submit" style="padding:8px 18px; background:var(--pu-navy); color:#fff; border:none; border-radius:50px; font-weight:600; font-size:13px; cursor:pointer;">🔍 Filter</button>
        @if(request()->hasAny(['q', 'jenis_absen', 'bulan']) && (request('q') || request('jenis_absen') || request('bulan')))
            <a href="{{ route('surat.suket.index') }}" style="font-size:12.5px; color:var(--text-muted); text-decoration:none; padding:0 4px;">Reset</a>
        @endif
    </form>
    <a href="{{ route('surat.suket.create') }}" class="btn-quick-create" style="padding:10px 20px;">+ Buat Suket Baru</a>
</div>

<div class="panel-card">
    <div class="panel-head">
        <h3>Daftar Suket Lupa Absen ({{ $sukets->total() }})</h3>
    </div>
    <div class="panel-body" style="padding: 0;">
        @if($sukets->count() > 0)
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Nomor Suket</th>
                        <th>Jenis Keterangan</th>
                        <th>Nama Pegawai</th>
                        <th>NIP</th>
                        <th>Tanggal Kejadian</th>
                        <th>Waktu</th>
                        <th>Atasan Penyetuju</th>
                        <th style="text-align:center; width:200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $jenisLabel = [
                            'masuk' => 'Lupa Absen Masuk',
                            'pulang' => 'Lupa Absen Pulang',
                            'terlambat' => 'Terlambat Masuk',
                            'pulangawal' => 'Pulang Awal',
                        ];
                    @endphp
                    @foreach($sukets as $sk)
                        <tr>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 600;">
                                {{ $sk->nomor_surat }}
                            </td>
                            <td>
                                <span class="badge-jenis-{{ $sk->jenis_absen }}">
                                    {{ $jenisLabel[$sk->jenis_absen] ?? 'Lupa Absen ' . ucfirst($sk->jenis_absen) }}
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
                                    <a href="{{ route('surat.suket.edit', $sk->id) }}"
                                       style="padding:5px 9px; border-radius:4px; border:1px solid #fde68a; background:#fffbeb; color:#b45309; font-size:11.5px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                        ✏️ Edit
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
                <p style="font-weight: 600;">{{ request()->hasAny(['q', 'jenis_absen', 'bulan']) ? 'Tidak ada arsip yang sesuai dengan filter.' : 'Belum ada arsip Suket Lupa Absen di database.' }}</p>
                <a href="{{ route('surat.suket.create') }}" class="btn-quick-create" style="margin-top: 14px;">+ Buat Suket Lupa Absen</a>
            </div>
        @endif
    </div>
</div>
@endsection
