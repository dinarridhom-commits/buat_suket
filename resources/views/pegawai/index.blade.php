@extends('layouts.app')

@section('title', 'Database Pegawai')
@section('header_title', 'Master Database Pegawai')
@section('header_subtitle', 'Kelola Master Data ' . $totalPegawai . ' Pegawai & Pejabat BWS Kalimantan I Pontianak')

@push('styles')
<style>
    .top-actions-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .filter-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 12px 16px;
        display: flex;
        gap: 10px;
        align-items: center;
        flex: 1;
        flex-wrap: wrap;
        box-shadow: var(--shadow-card);
    }

    .search-input {
        flex: 1;
        min-width: 220px;
        padding: 9px 14px;
        border: 1.5px solid var(--border);
        border-radius: 50px;
        font-size: 13px;
        outline: none;
        background: var(--bg-subtle);
    }

    .search-input:focus {
        background: #ffffff;
        border-color: var(--pu-navy);
    }

    .select-unit {
        padding: 9px 14px;
        border: 1.5px solid var(--border);
        border-radius: 50px;
        font-size: 13px;
        background: var(--bg-subtle);
        outline: none;
        max-width: 260px;
    }

    .btn-filter {
        padding: 9px 18px;
        background: var(--pu-navy);
        color: #fff;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
    }

    .btn-add-pegawai {
        padding: 10px 18px;
        background: var(--pu-navy);
        color: #ffffff;
        border: none;
        border-radius: 50px;
        font-weight: 700;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(19, 36, 61, 0.22);
        transition: all 0.15s ease;
    }

    .btn-add-pegawai:hover {
        background: var(--pu-navy-light);
        transform: translateY(-1px);
    }

    .pegawai-table-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-card);
    }

    .pegawai-info-strip {
        padding: 11px 18px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
        font-size: 12px;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .table-scroll-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .table-pegawai {
        width: 100%;
        min-width: 1480px;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    .table-pegawai thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.45px;
        padding: 11px 12px;
        text-align: left;
        border-bottom: 1.5px solid var(--border);
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .table-pegawai tbody tr.group-row td {
        background: #eef2f8;
        color: var(--pu-navy);
        font-weight: 700;
        font-size: 12px;
        padding: 8px 14px;
        border-top: 1px solid #d7e0ee;
        border-bottom: 1px solid #d7e0ee;
    }

    .group-satker-tag {
        display: inline-block;
        margin-left: 8px;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 1px 8px;
        border-radius: 20px;
    }

    .table-pegawai tbody tr.data-row td {
        padding: 9px 12px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        color: var(--text-main);
    }

    .table-pegawai tbody tr.data-row:hover td {
        background: #fafbfd;
    }

    .badge-unit {
        background: #f1f5f9;
        color: var(--pu-navy);
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
        line-height: 1.35;
    }

    .badge-kelas {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        padding: 2px 7px;
        border-radius: 6px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 11.5px;
        font-family: 'JetBrains Mono', monospace;
    }

    .badge-ket {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        background: var(--pu-gold-light);
        color: #92400e;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .muted-dash {
        color: #9ca3af;
        font-size: 12px;
    }

    .action-btn {
        padding: 5px 9px;
        border-radius: 6px;
        border: 1px solid var(--border);
        background: #ffffff;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }

    .action-btn.edit:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .action-btn.delete {
        color: #dc2626;
        border-color: #fecaca;
        background: #fff5f5;
    }

    .action-btn.delete:hover {
        background: #fee2e2;
        border-color: #f87171;
    }

    /* MODAL */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(2px);
        padding: 16px;
    }

    .modal-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 680px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: modalFadeIn 0.2s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-header {
        padding: 14px 20px;
        background: var(--pu-navy);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14.5px;
        font-weight: 700;
    }

    .modal-body {
        padding: 20px;
        max-height: calc(85vh - 120px);
        overflow-y: auto;
    }

    .modal-footer {
        padding: 12px 20px;
        background: #f8fafc;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .form-group { margin-bottom: 12px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; }
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-sm);
        font-size: 13px;
        outline: none;
        font-family: inherit;
    }
    .form-control:focus { border-color: var(--pu-blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12); }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
    .modal-subhead {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--pu-navy);
        margin: 14px 0 8px;
        padding-top: 10px;
        border-top: 1px dashed var(--border);
    }
</style>
@endpush

@section('content')

    <!-- TOP ACTION BAR & FILTER -->
    <div class="top-actions-bar">
        <form method="GET" action="{{ route('pegawai.index') }}" class="filter-card">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama Pegawai, NIP, Jabatan, Kelas, atau Ket..." class="search-input">
            <select name="unit" class="select-unit">
                <option value="">-- Semua Unit / Satker --</option>
                @foreach($units as $u)
                    <option value="{{ $u }}" {{ request('unit') == $u ? 'selected' : '' }}>{{ $u }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter">Cari</button>
            @if(request('q') || request('unit'))
                <a href="{{ route('pegawai.index') }}" style="font-size: 12.5px; color: var(--text-muted); text-decoration: none; padding: 0 4px;">Reset</a>
            @endif
        </form>

        <button type="button" class="btn-add-pegawai" onclick="openAddModal()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pegawai
        </button>
        <button type="button" class="btn-add-pegawai" style="background: #1d4ed8;" onclick="openImportModal()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Impor CSV
        </button>
        <a href="{{ route('pegawai.export.excel', request()->only(['q', 'unit'])) }}" class="btn-add-pegawai" style="background: #047857; text-decoration:none;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Ekspor Excel
        </a>
    </div>

    <!-- PEGAWAI TABLE -->
    <div class="pegawai-table-card">
        <div class="pegawai-info-strip">
            <div>
                Kolom <strong>Jabatan di Surat</strong>, <strong>Rangkap di Surat</strong>, dan <strong>Nama di Surat</strong> (opsional) otomatis dipakai pada generator surat bila penulisan di surat berbeda dari SK (misal tanpa gelar atau jabatan disingkat).
            </div>
            <div style="font-weight: 700; color: var(--pu-navy); white-space: nowrap;">
                Menampilkan {{ $pegawais->count() }} dari {{ $totalPegawai }} pegawai
            </div>
        </div>

        <div class="table-scroll-wrap">
            <table class="table-pegawai">
                <thead>
                    <tr>
                        <th style="width: 44px; text-align:center;">No</th>
                        <th style="min-width: 190px;">Unit</th>
                        <th style="min-width: 210px;">Nama Lengkap</th>
                        <th style="min-width: 155px;">NIP</th>
                        <th style="min-width: 150px;">Pangkat / Gol.</th>
                        <th style="width: 66px; text-align:center;">Kelas</th>
                        <th style="min-width: 220px;">Jabatan Utama</th>
                        <th style="min-width: 190px;">Jabatan Rangkap</th>
                        <th style="min-width: 170px;">Jabatan di Surat</th>
                        <th style="min-width: 160px;">Rangkap di Surat</th>
                        <th style="min-width: 155px;">Nama di Surat</th>
                        <th style="min-width: 110px;">Ket.</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $lastGroup = null;
                        $unitCounts = $pegawais->groupBy(fn($x) => ($x->unit ?: 'Balai Wilayah Sungai Kalimantan I Pontianak') . '|' . ($x->satker ?? ''))->map->count();
                    @endphp
                    @forelse($pegawais as $index => $p)
                        @php
                            $unitName = $p->unit ?: 'Balai Wilayah Sungai Kalimantan I Pontianak';
                            $groupKey = $unitName . '|' . ($p->satker ?? '');
                        @endphp
                        @if($groupKey !== $lastGroup)
                            @php $lastGroup = $groupKey; @endphp
                            <tr class="group-row">
                                <td colspan="13">
                                    <span>{{ $unitName }}</span>
                                    @if($p->satker && $p->satker !== $unitName)
                                        <span class="group-satker-tag">{{ $p->satker }}</span>
                                    @endif
                                    <span style="float:right; font-size:11px; font-weight:600; color:#64748b;">{{ $unitCounts[$groupKey] ?? 0 }} pegawai</span>
                                </td>
                            </tr>
                        @endif
                        <tr class="data-row">
                            <td style="text-align:center; color: var(--text-subtle); font-variant-numeric: tabular-nums;">
                                {{ $index + 1 }}
                            </td>
                            <td>
                                <span class="badge-unit">{{ $unitName }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main);">{{ $p->nama }}</div>
                            </td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 11.5px; color: var(--text-muted); white-space: nowrap;">
                                {{ $p->nip ?: '-' }}
                            </td>
                            <td style="font-size: 12px;">{{ $p->pangkat ?: '-' }}</td>
                            <td style="text-align: center;">
                                @if($p->kelas)
                                    <span class="badge-kelas">{{ $p->kelas }}</span>
                                @else
                                    <span class="muted-dash">-</span>
                                @endif
                            </td>
                            <td style="font-weight: 500;">{{ $p->jabatan ?: '-' }}</td>
                            <td>
                                @if($p->rangkap)
                                    <span style="color: #92400e; font-weight: 500;">{{ $p->rangkap }}</span>
                                @else
                                    <span class="muted-dash">-</span>
                                @endif
                            </td>
                            <td>{!! $p->jabatan_surat ? e($p->jabatan_surat) : '<span class="muted-dash">-</span>' !!}</td>
                            <td>{!! $p->rangkap_surat ? e($p->rangkap_surat) : '<span class="muted-dash">-</span>' !!}</td>
                            <td>{!! $p->nama_surat ? e($p->nama_surat) : '<span class="muted-dash">-</span>' !!}</td>
                            <td>
                                @if($p->ket)
                                    <span class="badge-ket">{{ $p->ket }}</span>
                                @else
                                    <span class="muted-dash">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 5px;">
                                    <button type="button" class="action-btn edit"
                                        data-id="{{ $p->id }}"
                                        data-unit="{{ $p->unit }}"
                                        data-satker="{{ $p->satker }}"
                                        data-nama="{{ $p->nama }}"
                                        data-nip="{{ $p->nip }}"
                                        data-pangkat="{{ $p->pangkat }}"
                                        data-kelas="{{ $p->kelas }}"
                                        data-jabatan="{{ $p->jabatan }}"
                                        data-rangkap="{{ $p->rangkap }}"
                                        data-jabatan-surat="{{ $p->jabatan_surat }}"
                                        data-rangkap-surat="{{ $p->rangkap_surat }}"
                                        data-nama-surat="{{ $p->nama_surat }}"
                                        data-ket="{{ $p->ket }}"
                                        onclick="openEditModal(this)">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('pegawai.destroy', $p->id) }}" onsubmit="return confirm('Hapus data pegawai {{ addslashes($p->nama) }} dari database?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Hapus">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" style="text-align: center; padding: 36px; color: var(--text-muted);">
                                Tidak ada data pegawai yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH PEGAWAI -->
    <div class="modal-backdrop" id="modalAdd">
        <div class="modal-card">
            <div class="modal-header">
                <span>Tambah Data Pegawai Baru</span>
                <span style="cursor:pointer;" onclick="closeModal('modalAdd')">&times;</span>
            </div>
            <form method="POST" action="{{ route('pegawai.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" name="unit" class="form-control" value="Balai Wilayah Sungai Kalimantan I Pontianak">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Satuan Kerja (Satker)</label>
                            <input type="text" name="satker" class="form-control" placeholder="Opsional">
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Donni Saputra, S.T, M.Si., M.T." required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" placeholder="18 Digit NIP">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Pangkat / Gol.</label>
                            <input type="text" name="pangkat" class="form-control" placeholder="Pembina ( IV / a )">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" placeholder="Contoh: 10">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ket.</label>
                            <input type="text" name="ket" class="form-control" placeholder="Contoh: Tugas Belajar">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan Utama *</label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Kepala Sub Bagian Umum & Tata Usaha" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan Rangkap</label>
                        <input type="text" name="rangkap" class="form-control" placeholder="Contoh: Pgs. PPK Ketatalaksanaan">
                    </div>

                    <div class="modal-subhead">Penulisan Khusus di Surat (Opsional)</div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Jabatan di Surat</label>
                            <input type="text" name="jabatan_surat" class="form-control" placeholder="Kosongkan jika ikut Jabatan Utama">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rangkap di Surat</label>
                            <input type="text" name="rangkap_surat" class="form-control" placeholder="Kosongkan jika ikut Jabatan Rangkap">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama di Surat</label>
                        <input type="text" name="nama_surat" class="form-control" placeholder="Kosongkan jika ikut Nama Lengkap">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-filter" style="background:#e2e8f0; color:#334155;" onclick="closeModal('modalAdd')">Batal</button>
                    <button type="submit" class="btn-filter" style="background:var(--pu-blue);">Simpan Pegawai</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PEGAWAI -->
    <div class="modal-backdrop" id="modalEdit">
        <div class="modal-card">
            <div class="modal-header">
                <span>Edit Data Pegawai</span>
                <span style="cursor:pointer;" onclick="closeModal('modalEdit')">&times;</span>
            </div>
            <form method="POST" id="formEditPegawai">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" name="unit" id="editUnit" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Satuan Kerja (Satker)</label>
                            <input type="text" name="satker" id="editSatker" class="form-control">
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama" id="editNama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" id="editNip" class="form-control">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Pangkat / Gol.</label>
                            <input type="text" name="pangkat" id="editPangkat" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" id="editKelas" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ket.</label>
                            <input type="text" name="ket" id="editKet" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan Utama *</label>
                        <input type="text" name="jabatan" id="editJabatan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan Rangkap</label>
                        <input type="text" name="rangkap" id="editRangkap" class="form-control">
                    </div>

                    <div class="modal-subhead">Penulisan Khusus di Surat (Opsional)</div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Jabatan di Surat</label>
                            <input type="text" name="jabatan_surat" id="editJabatanSurat" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rangkap di Surat</label>
                            <input type="text" name="rangkap_surat" id="editRangkapSurat" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama di Surat</label>
                        <input type="text" name="nama_surat" id="editNamaSurat" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-filter" style="background:#e2e8f0; color:#334155;" onclick="closeModal('modalEdit')">Batal</button>
                    <button type="submit" class="btn-filter" style="background:var(--pu-blue);">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL IMPOR CSV PEGAWAI -->
    <div class="modal-backdrop" id="modalImport">
        <div class="modal-card">
            <div class="modal-header">
                <span>Impor Massal Data Pegawai (CSV)</span>
                <span style="cursor:pointer;" onclick="closeModal('modalImport')">&times;</span>
            </div>
            <form method="POST" action="{{ route('pegawai.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Pilih File CSV (.csv / .txt)</label>
                        <input type="file" name="file" accept=".csv,.txt" class="form-control" required>
                    </div>
                    <div style="font-size: 12px; color: #475569; background: #f8fafc; border: 1px solid var(--border); padding: 10px 12px; border-radius: 8px; line-height: 1.55;">
                        <strong>Format Kolom (baris pertama sebagai header):</strong><br>
                        <code>unit, satker, nama, nip, pangkat, kelas, jabatan, rangkap, jabatan_surat, rangkap_surat, nama_surat, ket</code><br>
                        Pemisah koma (<code>,</code>), titik koma (<code>;</code>), atau Tab didukung otomatis. Jika NIP sudah terdaftar, data pegawai akan diperbarui otomatis.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-filter" style="background:#e2e8f0; color:#334155;" onclick="closeModal('modalImport')">Batal</button>
                    <button type="submit" class="btn-filter" style="background:var(--pu-blue);">Unggah &amp; Proses</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('modalAdd').style.display = 'flex';
    }

    function openImportModal() {
        document.getElementById('modalImport').style.display = 'flex';
    }

    function openEditModal(btn) {
        const id = btn.getAttribute('data-id');
        const form = document.getElementById('formEditPegawai');
        form.action = '/pegawai/' + id + '/update';

        document.getElementById('editUnit').value = btn.getAttribute('data-unit') || '';
        document.getElementById('editSatker').value = btn.getAttribute('data-satker') || '';
        document.getElementById('editNama').value = btn.getAttribute('data-nama') || '';
        document.getElementById('editNip').value = btn.getAttribute('data-nip') || '';
        document.getElementById('editPangkat').value = btn.getAttribute('data-pangkat') || '';
        document.getElementById('editKelas').value = btn.getAttribute('data-kelas') || '';
        document.getElementById('editJabatan').value = btn.getAttribute('data-jabatan') || '';
        document.getElementById('editRangkap').value = btn.getAttribute('data-rangkap') || '';
        document.getElementById('editJabatanSurat').value = btn.getAttribute('data-jabatan-surat') || '';
        document.getElementById('editRangkapSurat').value = btn.getAttribute('data-rangkap-surat') || '';
        document.getElementById('editNamaSurat').value = btn.getAttribute('data-nama-surat') || '';
        document.getElementById('editKet').value = btn.getAttribute('data-ket') || '';

        document.getElementById('modalEdit').style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.style.display = 'none';
        }
    });
</script>
@endpush
