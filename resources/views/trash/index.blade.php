@extends('layouts.app')

@section('title', 'Tempat Sampah')
@section('header_title', 'Tempat Sampah (Soft Delete)')
@section('header_subtitle', 'Data yang dihapus sementara tersimpan di sini — dapat dipulihkan kembali atau dihapus permanen')

@push('styles')
<style>
    .trash-tabs {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }
    .trash-tab-group {
        display: inline-flex;
        gap: 8px;
        flex-wrap: wrap;
        background: var(--bg-surface);
        padding: 6px;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow-card);
    }
    .trash-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.15s;
    }
    .trash-tab-btn:hover {
        background: var(--bg-subtle);
        color: var(--text-main);
    }
    .trash-tab-btn.active {
        background: var(--pu-navy);
        color: #ffffff;
    }
    .trash-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 20px;
        padding: 0 6px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        background: rgba(15, 23, 42, 0.08);
        color: var(--text-main);
    }
    .trash-tab-btn.active .trash-tab-count {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }
    .btn-empty-trash {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 15px;
        border-radius: 10px;
        border: 1px solid #fecaca;
        background: #fff5f5;
        color: #dc2626;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s;
    }
    .btn-empty-trash:hover {
        background: #fee2e2;
    }
    .btn-row-restore {
        padding: 5px 10px;
        border-radius: 5px;
        border: 1px solid #a7f3d0;
        background: #ecfdf5;
        color: #047857;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }
    .btn-row-restore:hover {
        background: #d1fae5;
    }
    .btn-row-force {
        padding: 5px 10px;
        border-radius: 5px;
        border: 1px solid #fecaca;
        background: #fff5f5;
        color: #dc2626;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }
    .btn-row-force:hover {
        background: #fee2e2;
    }
</style>
@endpush

@section('content')

@php
    $tabTitles = [
        'undangan' => 'Arsip Surat Undangan Terhapus',
        'suket'    => 'Arsip Suket Absen Terhapus',
        'pegawai'  => 'Data Pegawai Terhapus',
    ];
@endphp

<div class="trash-tabs">
    <div class="trash-tab-group">
        <a href="{{ route('trash.index', ['tab' => 'undangan']) }}" class="trash-tab-btn {{ $tab === 'undangan' ? 'active' : '' }}">
            <span>📁 Surat Undangan</span>
            <span class="trash-tab-count">{{ $counts['undangan'] }}</span>
        </a>
        <a href="{{ route('trash.index', ['tab' => 'suket']) }}" class="trash-tab-btn {{ $tab === 'suket' ? 'active' : '' }}">
            <span>🗂️ Suket Absen</span>
            <span class="trash-tab-count">{{ $counts['suket'] }}</span>
        </a>
        <a href="{{ route('trash.index', ['tab' => 'pegawai']) }}" class="trash-tab-btn {{ $tab === 'pegawai' ? 'active' : '' }}">
            <span>👥 Database Pegawai</span>
            <span class="trash-tab-count">{{ $counts['pegawai'] }}</span>
        </a>
    </div>

    @if($items->total() > 0)
        <form method="POST" action="{{ route('trash.empty', $tab) }}"
              onsubmit="return confirm('Hapus PERMANEN seluruh {{ $items->total() }} item di tab {{ $tabTitles[$tab] }}? Tindakan ini tidak bisa dibatalkan.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-empty-trash">
                🗑️ Kosongkan Tab Ini ({{ $items->total() }})
            </button>
        </form>
    @endif
</div>

<div class="panel-card">
    <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h3>{{ $tabTitles[$tab] }} ({{ $items->total() }})</h3>
        <form method="POST" action="{{ route('trash.bulk', $tab) }}" id="bulkFormTrash" class="bulk-bar">
            @csrf
            <div id="bulkInputsTrash"></div>
            <span class="bulk-count-pill" id="bulkCountTrash">0 dipilih</span>
            <button type="submit" name="action" value="restore" class="btn-bulk-restore"
                    data-confirm="Pulihkan {count} data terpilih kembali ke daftar aktif?">
                Pulihkan Terpilih
            </button>
            <button type="submit" name="action" value="force_delete" class="btn-bulk-delete"
                    data-confirm="PERINGATAN: Hapus PERMANEN {count} data terpilih? Data yang dihapus permanen tidak bisa dikembalikan.">
                Hapus Permanen
            </button>
            <button type="button" class="btn-bulk-cancel" id="bulkCancelTrash">Batal</button>
        </form>
    </div>

    <div class="panel-body" style="padding: 0;">
        @if($items->count() > 0)
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width:38px; text-align:center;">
                            <input type="checkbox" class="chk-input" id="selectAllTrash" title="Pilih semua di halaman ini">
                        </th>
                        @if($tab === 'undangan')
                            <th>Nomor Surat</th>
                            <th>Hal / Acara</th>
                            <th>Tanggal Surat</th>
                            <th>Undangan</th>
                        @elseif($tab === 'suket')
                            <th>Nomor Suket</th>
                            <th>Nama Pegawai</th>
                            <th>Jenis</th>
                            <th>Tanggal Kejadian</th>
                        @else
                            <th>Nama Lengkap</th>
                            <th>NIP</th>
                            <th>Jabatan Utama</th>
                            <th>Unit</th>
                        @endif
                        <th>Dihapus Pada</th>
                        <th style="text-align:center; width:215px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $row)
                        <tr>
                            <td style="text-align:center;">
                                <input type="checkbox" class="chk-input row-chk-trash" value="{{ $row->id }}" aria-label="Pilih item {{ $row->id }}">
                            </td>
                            @if($tab === 'undangan')
                                <td style="font-family:'JetBrains Mono', monospace; font-size:12px; font-weight:600;">{{ $row->nomor_surat }}</td>
                                <td style="font-weight:600;">{{ $row->hal }}</td>
                                <td>{{ $row->tanggal_surat?->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $row->penerimas->count() }} orang/instansi</td>
                            @elseif($tab === 'suket')
                                <td style="font-family:'JetBrains Mono', monospace; font-size:12px; font-weight:600;">{{ $row->nomor_surat }}</td>
                                <td style="font-weight:600;">{{ $row->nama_pegawai }}</td>
                                <td>{{ $row->jenis_absen === 'pulang' ? 'Lupa Absen Pulang' : 'Lupa Absen Masuk' }}</td>
                                <td>{{ $row->tanggal_kejadian?->format('d/m/Y') ?? '-' }} ({{ $row->waktu_kejadian }})</td>
                            @else
                                <td style="font-weight:600;">{{ $row->nama }}</td>
                                <td style="font-family:'JetBrains Mono', monospace; font-size:12px;">{{ $row->nip ?: '-' }}</td>
                                <td>{{ $row->jabatan ?: '-' }}</td>
                                <td>{{ $row->unit ?: '-' }}</td>
                            @endif
                            <td style="font-size:12px; color:var(--text-muted);">
                                {{ $row->deleted_at?->format('d/m/Y H:i') }}
                            </td>
                            <td style="text-align:center;">
                                <div style="display:inline-flex; gap:6px; align-items:center;">
                                    <form method="POST" action="{{ route('trash.restore', [$tab, $row->id]) }}">
                                        @csrf
                                        <button type="submit" class="btn-row-restore">Pulihkan</button>
                                    </form>
                                    <form method="POST" action="{{ route('trash.force-delete', [$tab, $row->id]) }}"
                                          onsubmit="return confirm('Hapus PERMANEN data ini? Data tidak dapat dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-row-force">Hapus Permanen</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @include('partials.pagination-bar', ['paginator' => $items, 'label' => 'data terhapus'])
        @else
            <div class="empty-state">
                <span class="empty-icon">✨</span>
                <p style="font-weight:600;">Tidak ada data terhapus di kategori ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.initBulkSelect) {
            window.initBulkSelect({
                selectAllId: 'selectAllTrash',
                rowSelector: '.row-chk-trash',
                barId: 'bulkFormTrash',
                countId: 'bulkCountTrash',
                formId: 'bulkFormTrash',
                inputsId: 'bulkInputsTrash',
                cancelId: 'bulkCancelTrash',
                entityLabel: 'data terhapus'
            });
        }
    });
</script>
@endpush
