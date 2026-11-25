@extends('layouts.app')

@section('title', 'Log Aktivitas Sistem')
@section('header_title', 'Log Aktivitas & Audit Trail')
@section('header_subtitle', 'Rekam jejak seluruh aktivitas pembuatan, perubahan, dan penghapusan data kedinasan')

@push('styles')
<style>
    .log-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .log-filter-form {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        background: var(--bg-surface);
        padding: 10px 16px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
    }
    .log-select {
        padding: 8px 14px;
        border: 1.5px solid var(--border);
        border-radius: 50px;
        font-size: 13px;
        font-family: inherit;
        background: var(--bg-subtle);
        outline: none;
    }
    .btn-pill {
        padding: 8px 18px;
        border-radius: 50px;
        border: none;
        background: var(--pu-navy);
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-pill-gold {
        background: var(--pu-gold);
        color: var(--pu-navy);
        font-weight: 700;
    }
    .badge-act {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .act-create { background: #dcfce7; color: #166534; }
    .act-update { background: #eff6ff; color: #1d4ed8; }
    .act-delete { background: #fee2e2; color: #b91c1c; }
    .act-login  { background: #fef3c7; color: #92400e; }
    .act-backup { background: #f3e8ff; color: #6d28d9; }
</style>
@endpush

@section('content')

    <div class="log-toolbar">
        <form method="GET" action="{{ route('logs.index') }}" class="log-filter-form">
            <select name="module" class="log-select">
                <option value="">Semua Modul</option>
                @foreach($modules as $m)
                    <option value="{{ $m }}" {{ request('module') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
            <select name="action" class="log-select">
                <option value="">Semua Aksi</option>
                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Buat (Create)</option>
                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Ubah (Update)</option>
                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Hapus (Delete)</option>
                <option value="login"  {{ request('action') == 'login'  ? 'selected' : '' }}>Login</option>
                <option value="backup" {{ request('action') == 'backup' ? 'selected' : '' }}>Backup</option>
            </select>
            <button type="submit" class="btn-pill">🔍 Filter</button>
            @if(request()->hasAny(['module', 'action']))
                <a href="{{ route('logs.index') }}" style="font-size:12.5px; color:var(--text-muted); text-decoration:none; padding:0 6px;">Reset</a>
            @endif
        </form>

        <a href="{{ route('backup.download') }}" class="btn-pill btn-pill-gold">
            <span>💾</span> Unduh Backup Database
        </a>
    </div>

    <div class="panel-card">
        <div class="panel-head">
            <h3>📋 Riwayat Aktivitas Operator</h3>
            <span style="font-size:12px; color:var(--text-muted);">Total: {{ $logs->total() }} catatan</span>
        </div>
        <div class="panel-body" style="padding:0; overflow-x:auto;">
            @if($logs->count() > 0)
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="width:160px;">Waktu (WIB)</th>
                            <th style="width:140px;">Operator</th>
                            <th style="width:100px;">Aksi</th>
                            <th style="width:160px;">Modul</th>
                            <th>Keterangan Aktivitas</th>
                            <th style="width:120px;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td style="font-family:'JetBrains Mono',monospace; font-size:12px; color:var(--text-muted);">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td style="font-weight:700; color:var(--pu-navy);">{{ $log->user_name }}</td>
                                <td>
                                    <span class="badge-act act-{{ $log->action }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td style="font-weight:600;">{{ $log->module }}</td>
                                <td>{{ $log->description }}</td>
                                <td style="font-family:'JetBrains Mono',monospace; font-size:11.5px; color:var(--text-muted);">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="padding:16px 20px;">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="empty-state">
                    <span class="empty-icon">📜</span>
                    <p>Belum ada catatan aktivitas yang terekam.</p>
                </div>
            @endif
        </div>
    </div>

@endsection
