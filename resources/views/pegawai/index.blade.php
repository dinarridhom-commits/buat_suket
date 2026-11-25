@extends('layouts.app')

@section('title', 'Database Pegawai')
@section('header_title', 'Master Database Pegawai')
@section('header_subtitle', 'Kelola Master Data 148 Pegawai & Pejabat BWS Kalimantan I Pontianak')

@push('styles')
<style>
    .top-actions-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .filter-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 14px 18px;
        display: flex;
        gap: 12px;
        align-items: center;
        flex: 1;
        flex-wrap: wrap;
        box-shadow: var(--shadow-card);
    }

    .search-input {
        flex: 1;
        min-width: 240px;
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
        padding: 10px 20px;
        background: var(--pu-navy);
        color: #ffffff;
        border: none;
        border-radius: 50px;
        font-weight: 700;
        font-size: 13px;
        display: flex;
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

    .badge-unit {
        background: #f1f5f9;
        color: var(--pu-navy);
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }

    .action-btn {
        padding: 5px 9px;
        border-radius: 4px;
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

    .pagination-wrap {
        padding: 14px 20px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
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
    }

    .modal-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 580px;
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

    .form-group {
        margin-bottom: 12px;
    }

    .form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 4px;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-sm);
        font-size: 13px;
        outline: none;
    }

    .form-control:focus {
        border-color: var(--pu-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
</style>
@endpush

@section('content')

    <!-- TOP ACTION BAR & FILTER -->
    <div class="top-actions-bar">
        <form method="GET" action="{{ route('pegawai.index') }}" class="filter-card">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama Pegawai, NIP, atau Jabatan..." class="search-input">
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
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Nama Lengkap & Gelar</th>
                    <th>NIP</th>
                    <th>Pangkat / Gol</th>
                    <th>Jabatan Struktural / Definitif</th>
                    <th>Unit Kerja</th>
                    <th style="width: 140px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawais as $index => $p)
                    <tr>
                        <td style="color: var(--text-subtle); font-variant-numeric: tabular-nums;">
                            {{ $pegawais->firstItem() + $index }}
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $p->nama }}</div>
                            @if($p->rangkap)
                                <div style="font-size: 11.5px; color: var(--pu-gold-dark); font-weight: 500;">
                                    [Rangkap] {{ $p->rangkap }}
                                </div>
                            @endif
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-muted);">
                            {{ $p->nip ?? '-' }}
                        </td>
                        <td style="font-size: 12px;">{{ $p->pangkat ?? '-' }}</td>
                        <td style="font-size: 12.5px; font-weight: 500;">{{ $p->jabatan ?? '-' }}</td>
                        <td>
                            <span class="badge-unit">{{ $p->unit ?? 'Balai Wilayah Sungai Kalimantan I' }}</span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 6px;">
                                <button type="button" class="action-btn edit" 
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->nama }}"
                                    data-nip="{{ $p->nip }}"
                                    data-pangkat="{{ $p->pangkat }}"
                                    data-jabatan="{{ $p->jabatan }}"
                                    data-unit="{{ $p->unit }}"
                                    data-rangkap="{{ $p->rangkap }}"
                                    onclick="openEditModal(this)">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('pegawai.destroy', $p->id) }}" onsubmit="return confirm('Hapus data pegawai {{ $p->nama }} dari database?')">
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
                        <td colspan="7" style="text-align: center; padding: 36px; color: var(--text-muted);">
                            Tidak ada data pegawai yang sesuai dengan pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrap">
            {{ $pegawais->links() }}
        </div>
    </div>

    <!-- MODAL TAMBAH PEGAWAI -->
    <div class="modal-backdrop" id="modalAdd">
        <div class="modal-card">
            <div class="modal-header">
                <span>➕ Tambah Data Pegawai Baru</span>
                <span style="cursor:pointer;" onclick="closeModal('modalAdd')">✕</span>
            </div>
            <form method="POST" action="{{ route('pegawai.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap & Gelar *</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Donni Saputra, S.T, M.Si., M.T." required>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" placeholder="18 Digit NIP">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pangkat / Golongan</label>
                            <input type="text" name="pangkat" class="form-control" placeholder="Contoh: Pembina ( IV / a )">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan Definitif / Struktural *</label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Kepala Sub Bagian Umum dan Tata Usaha" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan Rangkap (Bila Ada)</label>
                        <input type="text" name="rangkap" class="form-control" placeholder="Contoh: Plt. Kepala Satker BWS Kalimantan I">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit Kerja / Satuan Kerja</label>
                        <input type="text" name="unit" class="form-control" value="Balai Wilayah Sungai Kalimantan I Pontianak">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-filter" style="background:#e2e8f0; color:#334155;" onclick="closeModal('modalAdd')">Batal</button>
                    <button type="submit" class="btn-filter" style="background:var(--pu-blue);">💾 Simpan Pegawai</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PEGAWAI -->
    <div class="modal-backdrop" id="modalEdit">
        <div class="modal-card">
            <div class="modal-header">
                <span>✏️ Edit Data Pegawai</span>
                <span style="cursor:pointer;" onclick="closeModal('modalEdit')">✕</span>
            </div>
            <form method="POST" id="formEditPegawai">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap & Gelar *</label>
                        <input type="text" name="nama" id="editNama" class="form-control" required>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" id="editNip" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pangkat / Golongan</label>
                            <input type="text" name="pangkat" id="editPangkat" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan Definitif / Struktural *</label>
                        <input type="text" name="jabatan" id="editJabatan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan Rangkap (Bila Ada)</label>
                        <input type="text" name="rangkap" id="editRangkap" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit Kerja / Satuan Kerja</label>
                        <input type="text" name="unit" id="editUnit" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-filter" style="background:#e2e8f0; color:#334155;" onclick="closeModal('modalEdit')">Batal</button>
                    <button type="submit" class="btn-filter" style="background:var(--pu-blue);">💾 Perbarui Data</button>
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
                        <code>nama, nip, pangkat, jabatan, rangkap, unit</code><br>
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

        document.getElementById('editNama').value = btn.getAttribute('data-nama') || '';
        document.getElementById('editNip').value = btn.getAttribute('data-nip') || '';
        document.getElementById('editPangkat').value = btn.getAttribute('data-pangkat') || '';
        document.getElementById('editJabatan').value = btn.getAttribute('data-jabatan') || '';
        document.getElementById('editRangkap').value = btn.getAttribute('data-rangkap') || '';
        document.getElementById('editUnit').value = btn.getAttribute('data-unit') || '';

        document.getElementById('modalEdit').style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Close on backdrop click
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.style.display = 'none';
        }
    });
</script>
@endpush
