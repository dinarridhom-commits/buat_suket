@extends('layouts.app')

@section('title', 'QR Code Generator PU')
@section('header_title', 'QR Code Generator Resmi PU')
@section('header_subtitle', 'Hasilkan QR Code Bersemayam Logo Kementerian Pekerjaan Umum — Siap Cetak Resolusi Tinggi')

@push('styles')
<style>
    .qr-workspace {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 24px;
        align-items: start;
    }

    .config-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .config-head {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
        font-size: 13.5px;
        font-weight: 700;
        color: var(--pu-navy);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .config-body {
        padding: 20px;
    }

    .field-wrap {
        margin-bottom: 18px;
    }

    .field-wrap:last-child {
        margin-bottom: 0;
    }

    .field-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }

    .field-input, .field-textarea, .field-select {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 13.5px;
        background: #ffffff;
        outline: none;
        transition: all 0.15s ease;
    }

    .field-input:focus, .field-textarea:focus, .field-select:focus {
        border-color: var(--pu-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .logo-option-card {
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px;
        margin-bottom: 18px;
    }

    .check-pu {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13.5px;
    }

    .check-pu input[type=checkbox] {
        width: 18px;
        height: 18px;
        accent-color: var(--pu-navy);
        cursor: pointer;
    }

    .logo-preview-thumb {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        object-fit: contain;
    }

    .slider-box {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid var(--border);
    }

    .slider-box.off {
        opacity: 0.45;
        pointer-events: none;
    }

    .slider-header {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 6px;
    }

    .slider-input {
        width: 100%;
        accent-color: var(--pu-navy);
        cursor: pointer;
    }

    /* PREVIEW CARD */
    .preview-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
    }

    .preview-canvas-box {
        padding: 30px;
        background: repeating-conic-gradient(#f1f5f9 0 25%, #ffffff 0 50%) 0 0/16px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid var(--border);
    }

    #cv {
        width: 250px;
        height: 250px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        border-radius: var(--radius-sm);
        background: #ffffff;
    }

    .meta-info-box {
        padding: 14px 20px;
        font-size: 12px;
        color: var(--text-muted);
        text-align: center;
        background: #fafbfc;
        border-bottom: 1px solid var(--border);
        min-height: 42px;
    }

    .meta-info-box.err {
        color: #dc2626;
        font-weight: 600;
    }

    .action-btns-grid {
        padding: 18px 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        border: 1px solid var(--border);
        background: #ffffff;
        color: var(--text-main);
    }

    .btn-action:hover {
        background: #f1f5f9;
    }

    .btn-primary-action {
        grid-column: 1 / -1;
        background: var(--pu-navy);
        color: #ffffff;
        border-color: var(--pu-navy);
    }

    .btn-primary-action:hover {
        background: var(--pu-navy-light);
    }

    @media (max-width: 900px) {
        .qr-workspace { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="qr-workspace">
    <!-- LEFT PANE: CONFIGURATION -->
    <div class="config-card">
        <div class="config-head">
            <span>⚙️</span> Konfigurasi Konten & Tampilan QR
        </div>
        <div class="config-body">
            <div class="field-wrap">
                <label class="field-label" for="text">Teks / URL Tujuan</label>
                <textarea id="text" class="field-textarea" rows="3" placeholder="Contoh: https://pu.go.id atau link Google Drive materi rapat">https://pu.go.id</textarea>
                <div style="font-size: 11.5px; color: var(--text-subtle); margin-top: 4px;">
                    Dapat berupa URL dokumen rapat, tautan absensi online, atau pesan teks bebas.
                </div>
            </div>

            <!-- LOGO PU TOGGLE -->
            <div class="logo-option-card">
                <label class="check-pu" for="useLogo">
                    <input type="checkbox" id="useLogo" checked>
                    <img id="chkLogo" class="logo-preview-thumb" alt="Logo PU">
                    <span>Gunakan Logo Resmi Kementerian PU di Tengah</span>
                </label>
                <div class="slider-box" id="logoOpts">
                    <div class="slider-header">
                        <span>Ukuran Logo di Tengah</span>
                        <span id="logoSizeVal" style="font-weight: 700; color: var(--pu-navy);">22%</span>
                    </div>
                    <input type="range" id="logoSize" class="slider-input" min="12" max="28" value="22">
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">
                        Saat logo aktif, sistem otomatis mengunci koreksi error ke level H (30%) agar QR tetap terbaca optimal.
                    </div>
                </div>
            </div>

            <!-- COLOR SCHEME -->
            <div class="grid-2 field-wrap">
                <div>
                    <label class="field-label" for="fg">Warna QR (Foreground)</label>
                    <input type="color" id="fg" class="field-input" value="#213366" style="height: 42px; padding: 3px; cursor: pointer;">
                </div>
                <div>
                    <label class="field-label" for="bg">Warna Latar (Background)</label>
                    <input type="color" id="bg" class="field-input" value="#ffffff" style="height: 42px; padding: 3px; cursor: pointer;">
                </div>
            </div>

            <!-- TECHNICAL PARAMETERS -->
            <div class="grid-2 field-wrap">
                <div>
                    <label class="field-label" for="ecc">Error Correction</label>
                    <select id="ecc" class="field-select">
                        <option value="L">L – 7% (Data Maksimal)</option>
                        <option value="M">M – 15% (Standar)</option>
                        <option value="Q">Q – 25% (Aman)</option>
                        <option value="H" selected>H – 30% (Rekomendasi Logo)</option>
                    </select>
                </div>
                <div>
                    <div class="slider-header">
                        <label class="field-label" for="margin" style="margin-bottom:0;">Margin Tepi</label>
                        <span id="marginVal" style="font-size:12px; font-weight:700; color:var(--pu-navy);">4 modul</span>
                    </div>
                    <input type="range" id="margin" class="slider-input" min="0" max="8" value="4" style="margin-top:8px;">
                </div>
            </div>

            <!-- HIDDEN FIELDS FOR FORM PERSISTENCE -->
            <input type="hidden" id="size" value="1024">
        </div>
    </div>

    <!-- RIGHT PANE: REAL CANVAS PREVIEW -->
    <div class="preview-card">
        <div class="config-head">
            <span>👁️</span> Pratinjau QR Code Asli
        </div>

        <div class="preview-canvas-box">
            <canvas id="cv" width="1024" height="1024"></canvas>
        </div>

        <div class="meta-info-box" id="meta">
            Menghitung modul QR...
        </div>

        <div class="action-btns-grid">
            <button type="button" class="btn-action btn-primary-action" id="dlPng">
                💾 Unduh PNG Resolusi Tinggi (1024px)
            </button>
            <button type="button" class="btn-action" id="copyBtn">
                📋 Salin ke Clipboard
            </button>
            <button type="button" class="btn-action" style="background:#047857; color:#ffffff; border-color:#047857;" onclick="openSaveModal()">
                ⭐ Simpan ke Riwayat
            </button>
        </div>
    </div>
</div>

<!-- MODAL SIMPAN KE RIWAYAT -->
<div class="modal-backdrop" id="modalSaveQr" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); align-items:center; justify-content:center; z-index:1000; backdrop-filter:blur(2px);">
    <div style="background:#ffffff; border-radius:12px; width:100%; max-width:480px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.25); overflow:hidden;">
        <div style="padding:14px 20px; background:var(--pu-navy); color:#ffffff; display:flex; align-items:center; justify-content:space-between; font-weight:700; font-size:14px;">
            <span>⭐ Simpan QR Code ke Riwayat</span>
            <span style="cursor:pointer;" onclick="closeSaveModal()">✕</span>
        </div>
        <form method="POST" action="{{ route('qrcode.store') }}">
            @csrf
            <div style="padding:20px;">
                <div class="field-wrap">
                    <label class="field-label" for="inJudul">Judul / Keterangan QR Code *</label>
                    <input type="text" name="judul" id="inJudul" class="field-input" placeholder="Contoh: QR Rapat Evaluasi Triwulan / Absensi" required>
                </div>
                <div class="field-wrap">
                    <label class="field-label">Isi Konten / URL</label>
                    <input type="text" name="konten" id="inKontenSave" class="field-input" readonly style="background:#f1f5f9; color:#475569;">
                </div>

                <!-- Hidden parameters -->
                <input type="hidden" name="warna_qr" id="inWarnaQr">
                <input type="hidden" name="warna_bg" id="inWarnaBg">
                <input type="hidden" name="use_logo_pu" id="inUseLogo">
                <input type="hidden" name="ukuran_logo" id="inUkuranLogo">
                <input type="hidden" name="error_correction" id="inEcc">
            </div>
            <div style="padding:12px 20px; background:#f8fafc; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-action" onclick="closeSaveModal()">Batal</button>
                <button type="submit" class="btn-action" style="background:var(--pu-blue); color:#ffffff; border-color:var(--pu-blue);">💾 Simpan Sekarang</button>
            </div>
        </form>
    </div>
</div>

<!-- SECTION RIWAYAT QR CODE TERSIMPAN -->
<div class="panel-card" style="margin-top:28px; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; box-shadow:var(--shadow-sm);">
    <div style="padding:16px 20px; border-bottom:1px solid var(--border); background:#fafbfc; display:flex; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:18px;">📱</span>
            <h3 style="font-size:14.5px; font-weight:700; color:var(--text-main); margin:0;">Riwayat QR Code Tersimpan ({{ $riwayatQr->count() }})</h3>
        </div>
        <span style="font-size:12px; color:var(--text-muted);">Tersimpan di database untuk digunakan sewaktu-waktu</span>
    </div>

    <div style="padding:0;">
        @if($riwayatQr->count() > 0)
            <table class="table-modern" style="width:100%; border-collapse:collapse; text-align:left;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border); background:#f8fafc;">
                        <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Judul QR</th>
                        <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Isi / URL Tautan</th>
                        <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Logo PU</th>
                        <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Tanggal</th>
                        <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-muted); font-weight:700; text-align:center; width:160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatQr as $qr)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 16px; font-weight:700; color:var(--text-main);">
                                {{ $qr->judul }}
                            </td>
                            <td style="padding:12px 16px; font-family:'JetBrains Mono', monospace; font-size:12px; color:var(--pu-blue); word-break:break-all; max-width:320px;">
                                {{ Str::limit($qr->konten, 45) }}
                            </td>
                            <td style="padding:12px 16px;">
                                <span style="font-size:11px; padding:2px 8px; border-radius:12px; font-weight:600; background:{{ $qr->use_logo_pu ? '#ecfdf5' : '#f1f5f9' }}; color:{{ $qr->use_logo_pu ? '#065f46' : '#64748b' }};">
                                    {{ $qr->use_logo_pu ? 'Aktif (' . $qr->ukuran_logo . '%)' : 'Tanpa Logo' }}
                                </span>
                            </td>
                            <td style="padding:12px 16px; font-size:12px; color:var(--text-muted);">
                                {{ $qr->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td style="padding:12px 16px; text-align:center;">
                                <div style="display:inline-flex; gap:6px; align-items:center;">
                                    <button type="button" class="btn-action" style="padding:5px 9px; font-size:11.5px; font-weight:600; background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe;"
                                            onclick="loadSavedQr('{{ addslashes($qr->konten) }}', '{{ $qr->warna_qr }}', '{{ $qr->warna_bg }}', {{ $qr->use_logo_pu ? 'true' : 'false' }}, {{ $qr->ukuran_logo }}, '{{ $qr->error_correction }}')">
                                        ⚡ Gunakan
                                    </button>
                                    <form method="POST" action="{{ route('qrcode.destroy', $qr->id) }}" onsubmit="return confirm('Hapus riwayat QR {{ addslashes($qr->judul) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action" style="padding:5px 9px; font-size:11.5px; font-weight:600; background:#fff5f5; color:#dc2626; border-color:#fecaca;">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align:center; padding:32px 20px; color:var(--text-muted);">
                <span style="font-size:32px; display:block; margin-bottom:8px;">📱</span>
                <p style="font-weight:600;">Belum ada riwayat QR Code yang disimpan.</p>
                <p style="font-size:12px; margin-top:4px;">Gunakan tombol "⭐ Simpan ke Riwayat" di atas untuk menyimpan QR Code agar dapat digunakan kembali.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script src="{{ asset('js/logo_pu.js') }}"></script>
<script>
(function(){
    qrcode.stringToBytes = qrcode.stringToBytesFuncs['UTF-8'];

    const $ = id => document.getElementById(id);
    const els = {
        text: $('text'),
        useLogo: $('useLogo'),
        logoSize: $('logoSize'),
        fg: $('fg'),
        bg: $('bg'),
        ecc: $('ecc'),
        size: $('size'),
        margin: $('margin'),
        cv: $('cv'),
        meta: $('meta')
    };

    const LOGO_SRC = window.LOGO_PU_BASE64;
    $('chkLogo').src = LOGO_SRC;

    const logoImg = new Image();
    logoImg.onload = render;
    logoImg.src = LOGO_SRC;

    let lastEcc = els.ecc.value;
    let current = null;

    function settings(){
        const useLogo = els.useLogo.checked;
        return {
            text: els.text.value,
            useLogo,
            logoPct: +els.logoSize.value / 100,
            fg: els.fg.value,
            bg: els.bg.value,
            ecc: useLogo ? 'H' : els.ecc.value,
            size: +els.size.value,
            margin: +els.margin.value
        };
    }

    function makeQR(text, ecc){
        const qr = qrcode(0, ecc);
        qr.addData(text, 'Byte');
        qr.make();
        return qr;
    }

    function logoGeom(count, margin, pct){
        const total = count + margin * 2;
        let box = Math.round(count * pct);
        if ((box % 2) !== (count % 2)) box += 1;
        const pad = Math.max(1, Math.round(box * 0.12));
        const outer = box + pad * 2;
        const start = margin + (count - outer) / 2;
        return {total, box, pad, outer, start};
    }

    function drawTo(canvas, s, qr){
        const count = qr.getModuleCount();
        const total = count + s.margin * 2;
        const N = canvas.width;
        const ctx = canvas.getContext('2d');
        const ms = N / total;

        ctx.imageSmoothingEnabled = true;
        ctx.fillStyle = s.bg;
        ctx.fillRect(0, 0, N, N);

        ctx.fillStyle = s.fg;
        for (let r = 0; r < count; r++){
            for (let c = 0; c < count; c++){
                if (!qr.isDark(r, c)) continue;
                const x0 = Math.round((c + s.margin) * ms);
                const x1 = Math.round((c + s.margin + 1) * ms);
                const y0 = Math.round((r + s.margin) * ms);
                const y1 = Math.round((r + s.margin + 1) * ms);
                ctx.fillRect(x0, y0, x1 - x0, y1 - y0);
            }
        }

        if (s.useLogo && logoImg.complete && logoImg.naturalWidth){
            const g = logoGeom(count, s.margin, s.logoPct);
            const ox = g.start * ms, ow = g.outer * ms, rad = ow * 0.12;
            ctx.fillStyle = '#ffffff';
            roundRect(ctx, ox, ox, ow, ow, rad);
            ctx.fill();

            const lx = (g.start + g.pad) * ms, lw = g.box * ms;
            ctx.save();
            roundRect(ctx, lx, lx, lw, lw, lw * 0.06);
            ctx.clip();
            ctx.drawImage(logoImg, lx, lx, lw, lw);
            ctx.restore();
        }
    }

    function roundRect(ctx, x, y, w, h, r){
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r);
        ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r);
        ctx.arcTo(x, y, x + w, y, r);
        ctx.closePath();
    }

    function render(){
        const s = settings();

        $('logoSizeVal').textContent = els.logoSize.value + '%';
        $('marginVal').textContent = els.margin.value + ' modul';
        $('logoOpts').classList.toggle('off', !s.useLogo);
        els.ecc.disabled = s.useLogo;

        if (s.useLogo){
            if (els.ecc.value !== 'H') lastEcc = els.ecc.value;
            els.ecc.value = 'H';
        } else if (els.ecc.value === 'H' && lastEcc !== 'H') {
            els.ecc.value = lastEcc;
            s.ecc = lastEcc;
        }

        const btns = [$('dlPng'), $('copyBtn')];

        if (!s.text.trim()){
            clearCanvas();
            els.meta.textContent = 'Isi teks atau URL terlebih dahulu.';
            els.meta.className = 'meta-info-box';
            btns.forEach(b => b.disabled = true);
            current = null;
            return;
        }

        let qr;
        try {
            qr = makeQR(s.text, s.ecc);
        } catch(e){
            clearCanvas();
            els.meta.textContent = 'Teks terlalu panjang untuk level error correction ' + s.ecc + '.';
            els.meta.className = 'meta-info-box err';
            btns.forEach(b => b.disabled = true);
            current = null;
            return;
        }

        btns.forEach(b => b.disabled = false);
        current = qr;

        els.cv.width = els.cv.height = 1024;
        drawTo(els.cv, s, qr);

        const n = qr.getModuleCount();
        const bytes = new TextEncoder().encode(s.text).length;
        els.meta.className = 'meta-info-box';
        els.meta.textContent = `Versi ${(n - 17) / 4} · ${n}×${n} modul · level ${s.ecc} · ${bytes} byte` + (s.useLogo ? ' · logo PU' : '');
    }

    function clearCanvas(){
        const ctx = els.cv.getContext('2d');
        ctx.clearRect(0, 0, els.cv.width, els.cv.height);
    }

    // DOWNLOAD PNG
    $('dlPng').addEventListener('click', function(){
        const a = document.createElement('a');
        a.download = 'QR_Code_PU_' + Date.now() + '.png';
        a.href = els.cv.toDataURL('image/png');
        a.click();
    });

    // COPY CLIPBOARD
    $('copyBtn').addEventListener('click', async function(){
        try {
            els.cv.toBlob(async blob => {
                await navigator.clipboard.write([new ClipboardItem({'image/png': blob})]);
                alert('Gambar QR Code berhasil disalin ke Clipboard!');
            });
        } catch(e) {
            alert('Gagal menyalin otomatis. Anda dapat klik kanan pada gambar lalu pilih Salin Gambar.');
        }
    });

    // EVENT LISTENERS
    ['input', 'change'].forEach(evt => {
        els.text.addEventListener(evt, render);
        els.fg.addEventListener(evt, render);
        els.bg.addEventListener(evt, render);
        els.logoSize.addEventListener(evt, render);
        els.margin.addEventListener(evt, render);
        els.ecc.addEventListener(evt, render);
        els.useLogo.addEventListener(evt, render);
    });

    // Initial render
    render();

    // Expose helpers globally
    window.openSaveModal = function() {
        const text = els.text.value.trim();
        if (!text) {
            alert('Teks atau tautan QR Code belum diisi.');
            return;
        }
        document.getElementById('inKontenSave').value = text;
        document.getElementById('inWarnaQr').value = els.fg.value;
        document.getElementById('inWarnaBg').value = els.bg.value;
        document.getElementById('inUseLogo').value = els.useLogo.checked ? 1 : 0;
        document.getElementById('inUkuranLogo').value = els.logoSize.value;
        document.getElementById('inEcc').value = els.ecc.value;
        document.getElementById('modalSaveQr').style.display = 'flex';
    };

    window.closeSaveModal = function() {
        document.getElementById('modalSaveQr').style.display = 'none';
    };

    window.loadSavedQr = function(konten, fg, bg, useLogo, logoSize, ecc) {
        els.text.value = konten;
        if (fg) els.fg.value = fg;
        if (bg) els.bg.value = bg;
        els.useLogo.checked = Boolean(useLogo);
        if (logoSize) els.logoSize.value = logoSize;
        if (ecc) els.ecc.value = ecc;
        render();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
})();
</script>
@endpush
