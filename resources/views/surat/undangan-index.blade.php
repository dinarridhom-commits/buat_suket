@extends('layouts.app')

@section('title', 'Arsip Surat Undangan')
@section('header_title', 'Buku Agenda & Arsip Surat Undangan')
@section('header_subtitle', 'Daftar Surat Undangan Keluar BWS Kalimantan I Pontianak')

@section('content')

<div class="panel-card">
    <div class="panel-head">
        <h3>Daftar Surat Undangan Terbit</h3>
        <a href="{{ route('surat.undangan.create') }}" class="btn-quick-create">+ Buat Surat Undangan</a>
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
                        <th style="text-align:center; width:160px;">Aksi</th>
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
                                <div style="display:inline-flex; gap:6px; align-items:center;">
                                    <a href="{{ route('surat.undangan.show', $s->id) }}"
                                       style="padding:5px 9px; border-radius:4px; border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; font-size:11.5px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                        👁️ Lihat
                                    </a>
                                    <form method="POST" action="{{ route('surat.undangan.destroy', $s->id) }}"
                                          onsubmit="return confirm('Hapus arsip surat {{ addslashes($s->nomor_surat) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                style="padding:5px 9px; border-radius:4px; border:1px solid #fecaca; background:#fff5f5; color:#dc2626; font-size:11.5px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
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
                {{ $surats->links() }}
            </div>
        @else
            <div class="empty-state">
                <span>📭</span>
                <p style="font-weight: 600;">Belum ada arsip Surat Undangan di database.</p>
                <a href="{{ route('surat.undangan.create') }}" class="btn-quick-create" style="margin-top: 14px;">+ Buat Surat Undangan Sekarang</a>
            </div>
        @endif
    </div>
</div>
@endsection
