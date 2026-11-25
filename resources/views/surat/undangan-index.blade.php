@extends('layouts.app')

@section('title', 'Arsip Surat Undangan')
@section('header_title', 'Buku Agenda & Arsip Surat Undangan')
@section('header_subtitle', 'Daftar Surat Undangan Keluar BWS Kalimantan I Pontianak')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; gap:14px; margin-bottom:18px; flex-wrap:wrap;">
    <form method="GET" action="{{ route('surat.undangan.index') }}"
          style="display:flex; align-items:center; gap:10px; flex:1; flex-wrap:wrap; background:var(--bg-surface); padding:12px 16px; border:1px solid var(--border); border-radius:var(--radius-lg); box-shadow:var(--shadow-card);">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor surat, hal, tempat, atau penandatangan..."
               style="flex:1; min-width:220px; padding:8px 14px; border:1.5px solid var(--border); border-radius:50px; font-size:13px; background:var(--bg-subtle); outline:none;">
        <select name="status" style="padding:8px 14px; border:1.5px solid var(--border); border-radius:50px; font-size:13px; background:var(--bg-subtle); outline:none;">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="final" {{ request('status') == 'final' ? 'selected' : '' }}>Final</option>
        </select>
        <input type="month" name="bulan" value="{{ request('bulan') }}" title="Filter Bulan Surat"
               style="padding:7px 12px; border:1.5px solid var(--border); border-radius:50px; font-size:13px; background:var(--bg-subtle); outline:none;">
        <button type="submit" style="padding:8px 18px; background:var(--pu-navy); color:#fff; border:none; border-radius:50px; font-weight:600; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            Filter
        </button>
        @if(request()->hasAny(['q', 'status', 'bulan']) && (request('q') || request('status') || request('bulan')))
            <a href="{{ route('surat.undangan.index') }}" style="font-size:12.5px; color:var(--text-muted); text-decoration:none; padding:0 4px;">Reset</a>
        @endif
    </form>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a href="{{ route('surat.undangan.export.excel', request()->only(['q', 'status', 'bulan'])) }}"
           style="padding:10px 18px; background:#047857; color:#fff; border-radius:50px; font-weight:700; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Ekspor Excel
        </a>
        <a href="{{ route('surat.undangan.create') }}" class="btn-quick-create" style="padding:10px 20px;">+ Buat Surat Undangan</a>
    </div>
</div>

<div class="panel-card">
    <div class="panel-head">
        <h3>Daftar Surat Undangan Terbit ({{ $surats->total() }})</h3>
    </div>
    <div class="panel-body" style="padding: 0;">
        @if($surats->count() > 0)
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Hal / Acara</th>
                        <th>Tanggal Surat</th>
                        <th>Tempat</th>
                        <th>Penandatangan</th>
                        <th>Jumlah Undangan</th>
                        <th>Status</th>
                        <th style="text-align:center; width:275px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surats as $s)
                        <tr>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 600;">
                                {{ $s->nomor_surat }}
                            </td>
                            <td style="font-weight: 600;">{{ $s->hal }}</td>
                            <td>{{ $s->tanggal_surat->format('d/m/Y') }}</td>
                            <td>{{ $s->tempat }}</td>
                            <td>{{ $s->penandatangan_nama }}</td>
                            <td>{{ $s->penerimas->count() }} orang/instansi</td>
                            <td>
                                <span class="badge-status badge-{{ $s->status == 'final' ? 'final' : 'draft' }}">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:inline-flex; gap:5px; align-items:center;">
                                    <a href="{{ route('surat.undangan.show', $s->id) }}"
                                       style="padding:5px 9px; border-radius:4px; border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; font-size:11.5px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Lihat
                                    </a>
                                    <a href="{{ route('surat.undangan.edit', $s->id) }}"
                                       style="padding:5px 9px; border-radius:4px; border:1px solid #fde68a; background:#fffbeb; color:#b45309; font-size:11.5px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit
                                    </a>
                                    <a href="{{ route('surat.undangan.duplicate', $s->id) }}" title="Duplikat menjadi surat baru"
                                       style="padding:5px 9px; border-radius:4px; border:1px solid #cbd5e1; background:#f8fafc; color:#334155; font-size:11.5px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        Duplikat
                                    </a>
                                    <form method="POST" action="{{ route('surat.undangan.destroy', $s->id) }}"
                                          onsubmit="return confirm('Hapus arsip surat {{ addslashes($s->nomor_surat) }}?')">
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
            <div style="padding: 16px 20px;">
                {{ $surats->links() }}
            </div>
        @else
            <div class="empty-state">
                <span>📭</span>
                <p style="font-weight: 600;">{{ request()->hasAny(['q', 'status', 'bulan']) ? 'Tidak ada arsip yang sesuai dengan filter.' : 'Belum ada arsip Surat Undangan di database.' }}</p>
                <a href="{{ route('surat.undangan.create') }}" class="btn-quick-create" style="margin-top: 14px;">+ Buat Surat Undangan Sekarang</a>
            </div>
        @endif
    </div>
</div>
@endsection
