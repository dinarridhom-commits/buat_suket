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
            <option value="">Semua Jenis</option>
            <option value="masuk" {{ request('jenis_absen') == 'masuk' ? 'selected' : '' }}>Lupa Absen Masuk</option>
            <option value="pulang" {{ request('jenis_absen') == 'pulang' ? 'selected' : '' }}>Lupa Absen Pulang</option>
        </select>
        <input type="month" name="bulan" value="{{ request('bulan') }}" title="Filter Bulan Kejadian"
               style="padding:7px 12px; border:1.5px solid var(--border); border-radius:50px; font-size:13px; background:var(--bg-subtle); outline:none;">
        <select name="per_page" onchange="this.form.submit()" title="Jumlah data per halaman"
                style="padding:8px 14px; border:1.5px solid var(--border); border-radius:50px; font-size:13px; background:var(--bg-subtle); outline:none;">
            @foreach([5, 10, 15, 25, 50, 100] as $opt)
                <option value="{{ $opt }}" {{ ($perPage ?? 10) == $opt ? 'selected' : '' }}>{{ $opt }} / hal</option>
            @endforeach
        </select>
        <button type="submit" style="padding:8px 18px; background:var(--pu-navy); color:#fff; border:none; border-radius:50px; font-weight:600; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            Filter
        </button>
        @if(request()->hasAny(['q', 'jenis_absen', 'bulan', 'per_page']) && (request('q') || request('jenis_absen') || request('bulan') || request('per_page')))
            <a href="{{ route('surat.suket.index') }}" style="font-size:12.5px; color:var(--text-muted); text-decoration:none; padding:0 4px;">Reset</a>
        @endif
    </form>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a href="{{ route('surat.suket.export.excel', request()->only(['q', 'jenis_absen', 'bulan'])) }}"
           style="padding:10px 18px; background:#047857; color:#fff; border-radius:50px; font-weight:700; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Ekspor Excel
        </a>
        <a href="{{ route('surat.suket.create') }}" class="btn-quick-create" style="padding:10px 20px;">+ Buat Suket Baru</a>
    </div>
</div>

<div class="panel-card">
    <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h3>Daftar Suket Lupa Absen ({{ $sukets->total() }})</h3>
        <form method="POST" action="{{ route('surat.suket.destroy.bulk') }}" id="bulkFormSuket" class="bulk-bar">
            @csrf
            @method('DELETE')
            <div id="bulkInputsSuket"></div>
            <span class="bulk-count-pill" id="bulkCountSuket">0 dipilih</span>
            <button type="submit" class="btn-bulk-delete">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                Hapus Terpilih
            </button>
            <button type="button" class="btn-bulk-cancel" id="bulkCancelSuket">Batal</button>
        </form>
    </div>
    <div class="panel-body" style="padding: 0;">
        @if($sukets->count() > 0)
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width:38px; text-align:center;">
                            <input type="checkbox" class="chk-input" id="selectAllSuket" title="Pilih semua di halaman ini">
                        </th>
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
                            <td style="text-align:center;">
                                <input type="checkbox" class="chk-input row-chk-suket" value="{{ $sk->id }}" aria-label="Pilih {{ $sk->nomor_surat }}">
                            </td>
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
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Lihat
                                    </a>
                                    <a href="{{ route('surat.suket.edit', $sk->id) }}"
                                       style="padding:5px 9px; border-radius:4px; border:1px solid #fde68a; background:#fffbeb; color:#b45309; font-size:11.5px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('surat.suket.destroy', $sk->id) }}"
                                          onsubmit="return confirm('Hapus arsip suket {{ addslashes($sk->nama_pegawai) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                style="padding:5px 8px; border-radius:4px; border:1px solid #fecaca; background:#fff5f5; color:#dc2626; font-size:11.5px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center;">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @include('partials.pagination-bar', ['paginator' => $sukets, 'label' => 'suket'])
        @else
            <div class="empty-state">
                <span class="empty-icon"><x-icon name="file-text" class="ui-icon-lg" /></span>
                <p style="font-weight: 600;">{{ request()->hasAny(['q', 'jenis_absen', 'bulan']) ? 'Tidak ada arsip yang sesuai dengan filter.' : 'Belum ada arsip Suket Lupa Absen di database.' }}</p>
                <a href="{{ route('surat.suket.create') }}" class="btn-quick-create" style="margin-top: 14px;">+ Buat Suket Lupa Absen</a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.initBulkSelect) {
            window.initBulkSelect({
                selectAllId: 'selectAllSuket',
                rowSelector: '.row-chk-suket',
                barId: 'bulkFormSuket',
                countId: 'bulkCountSuket',
                formId: 'bulkFormSuket',
                inputsId: 'bulkInputsSuket',
                cancelId: 'bulkCancelSuket',
                entityLabel: 'arsip suket lupa absen'
            });
        }
    });
</script>
@endpush
