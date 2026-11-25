@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('header_title', 'Manajemen Akun Pengguna')
@section('header_subtitle', 'Kelola akun operator dan administrator Portal Tata Usaha BWS Kalimantan I')

@push('styles')
<style>
    .user-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .btn-primary-pill {
        padding: 10px 20px;
        background: var(--pu-navy);
        color: #ffffff;
        border: none;
        border-radius: 50px;
        font-weight: 700;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(19, 36, 61, 0.18);
    }
    .btn-primary-pill:hover { background: var(--pu-navy-light); }
    .btn-icon-sm {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid var(--border);
        background: #ffffff;
        color: var(--text-main);
    }
    .btn-icon-sm.edit:hover { background: #eff6ff; border-color: #93c5fd; color: #1d4ed8; }
    .btn-icon-sm.delete { background: #fff5f5; border-color: #fecaca; color: #dc2626; }
    .btn-icon-sm.delete:hover { background: #fee2e2; }
    .badge-self {
        background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;
        padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-left: 6px;
    }
    .modal-backdrop {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55);
        display: none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(2px);
    }
    .modal-card {
        background: #ffffff; border-radius: var(--radius-lg); width: 100%; max-width: 460px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;
    }
    .modal-header {
        padding: 14px 20px; background: var(--pu-navy); color: #ffffff;
        display: flex; align-items: center; justify-content: space-between; font-size: 14.5px; font-weight: 700;
    }
    .modal-body { padding: 20px; }
    .modal-footer {
        padding: 12px 20px; background: #f8fafc; border-top: 1px solid var(--border);
        display: flex; justify-content: flex-end; gap: 10px;
    }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 5px; }
    .form-control {
        width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: var(--radius-sm);
        font-size: 13.5px; font-family: inherit; outline: none;
    }
    .form-control:focus { border-color: var(--pu-blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12); }
</style>
@endpush

@section('content')

<div class="user-toolbar">
    <div style="font-size: 13px; color: var(--text-muted);">
        Total <strong>{{ $users->count() }}</strong> akun terdaftar dengan akses sistem.
    </div>
    <button type="button" class="btn-primary-pill" onclick="openUserModal()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Pengguna
    </button>
</div>

<div class="panel-card">
    <div class="panel-head">
        <h3>Daftar Akun Pengguna Sistem</h3>
    </div>
    <div class="panel-body" style="padding: 0;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Lengkap</th>
                    <th>Alamat Email</th>
                    <th>Terdaftar Sejak</th>
                    <th style="text-align: center; width: 200px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $idx => $u)
                    <tr>
                        <td style="color: var(--text-subtle);">{{ $idx + 1 }}</td>
                        <td style="font-weight: 600;">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                                <span class="badge-self">Akun Aktif</span>
                            @endif
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-size: 12.5px;">{{ $u->email }}</td>
                        <td>{{ $u->created_at ? $u->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 6px;">
                                <button type="button" class="btn-icon-sm edit"
                                        data-id="{{ $u->id }}"
                                        data-name="{{ $u->name }}"
                                        data-email="{{ $u->email }}"
                                        onclick="openEditUserModal(this)">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit / Sandi
                                </button>
                                @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $u->id) }}"
                                          onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon-sm delete" title="Hapus Akun">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH / EDIT PENGGUNA -->
<div class="modal-backdrop" id="userModal">
    <div class="modal-card">
        <div class="modal-header">
            <span id="userModalTitle">Tambah Pengguna Baru</span>
            <button type="button" onclick="closeUserModal()" style="background:none; border:none; color:#fff; font-size:18px; cursor:pointer;">&times;</button>
        </div>
        <form id="userForm" method="POST" action="{{ route('users.store') }}">
            @csrf
            <input type="hidden" name="_method" id="userMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="uName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <input type="email" name="email" id="uEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" id="uPassLabel">Kata Sandi (min. 6 karakter)</label>
                    <input type="password" name="password" id="uPass" class="form-control">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="uPassConf" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeUserModal()" class="btn-icon-sm">Batal</button>
                <button type="submit" class="btn-primary-pill" style="padding:8px 18px;">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const modal = document.getElementById('userModal');
    const form  = document.getElementById('userForm');

    function openUserModal() {
        document.getElementById('userModalTitle').innerText = 'Tambah Pengguna Baru';
        form.action = "{{ route('users.store') }}";
        document.getElementById('userMethod').value = 'POST';
        document.getElementById('uName').value = '';
        document.getElementById('uEmail').value = '';
        document.getElementById('uPass').value = '';
        document.getElementById('uPassConf').value = '';
        document.getElementById('uPass').required = true;
        document.getElementById('uPassConf').required = true;
        document.getElementById('uPassLabel').innerText = 'Kata Sandi (min. 6 karakter)';
        modal.style.display = 'flex';
    }

    function openEditUserModal(btn) {
        document.getElementById('userModalTitle').innerText = 'Ubah Data & Kata Sandi Pengguna';
        form.action = "{{ url('pengguna') }}/" + btn.getAttribute('data-id');
        document.getElementById('userMethod').value = 'PUT';
        document.getElementById('uName').value = btn.getAttribute('data-name');
        document.getElementById('uEmail').value = btn.getAttribute('data-email');
        document.getElementById('uPass').value = '';
        document.getElementById('uPassConf').value = '';
        document.getElementById('uPass').required = false;
        document.getElementById('uPassConf').required = false;
        document.getElementById('uPassLabel').innerText = 'Kata Sandi Baru (kosongkan jika tidak diubah)';
        modal.style.display = 'flex';
    }

    function closeUserModal() {
        modal.style.display = 'none';
    }
</script>
@endpush
