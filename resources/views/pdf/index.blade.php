@extends('layouts.app')

@section('title', 'PDF Studio (All-in-One)')
@section('header_title', 'PDF Studio (All-in-One Tools)')
@section('header_subtitle', 'Alat Olah Dokumen PDF Internal — Gabung Surat dengan Lampiran, Pecah, Putar, dan Gambar ke PDF')

@push('styles')
<style>
    .pdf-tabs-nav {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 2px solid var(--border);
        padding-bottom: 4px;
        flex-wrap: wrap;
    }

    .pdf-tab-btn {
        padding: 10px 20px;
        border-radius: var(--radius-md) var(--radius-md) 0 0;
        border: none;
        background: transparent;
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        position: relative;
    }

    .pdf-tab-btn:hover {
        color: var(--pu-navy);
        background: #f1f5f9;
    }

    .pdf-tab-btn.active {
        color: var(--pu-navy);
        background: #ffffff;
        border: 1px solid var(--border);
        border-bottom-color: #ffffff;
        margin-bottom: -6px;
        box-shadow: 0 -2px 6px rgba(0,0,0,0.03);
    }

    .pdf-tab-btn.active::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--pu-gold);
        border-radius: 3px 3px 0 0;
    }

    .tool-pane {
        display: none;
    }

    .tool-pane.active {
        display: block;
    }

    .dropzone-box {
        border: 2px dashed #cbd5e1;
        border-radius: var(--radius-lg);
        background: #fafbfc;
        padding: 40px 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 20px;
    }

    .dropzone-box:hover, .dropzone-box.dragover {
        border-color: var(--pu-blue);
        background: #eff6ff;
    }

    .dropzone-icon {
        font-size: 42px;
        margin-bottom: 12px;
        display: block;
    }

    .file-queue-list {
        margin-bottom: 20px;
    }

    .file-item-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        box-shadow: var(--shadow-sm);
    }

    .file-item-name {
        font-weight: 600;
        font-size: 13.5px;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .file-item-size {
        font-size: 12px;
        color: var(--text-muted);
    }

    .btn-remove-file {
        border: none;
        background: transparent;
        color: #ef4444;
        cursor: pointer;
        font-size: 16px;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .btn-remove-file:hover {
        background: #fee2e2;
    }

    .progress-bar-wrap {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 14px;
        display: none;
    }

    .progress-fill {
        height: 100%;
        width: 0%;
        background: var(--pu-navy);
        transition: width 0.2s ease;
    }
</style>
@endpush

@section('content')

<!-- TABS NAVIGATION -->
<div class="pdf-tabs-nav">
    <button type="button" class="pdf-tab-btn active" data-target="tabMerge">
        <span>📎</span> Merge (Gabungkan PDF)
    </button>
    <button type="button" class="pdf-tab-btn" data-target="tabSplit">
        <span>✂️</span> Split (Pecah Halaman)
    </button>
    <button type="button" class="pdf-tab-btn" data-target="tabRotate">
        <span>🔄</span> Rotate (Putar Halaman)
    </button>
    <button type="button" class="pdf-tab-btn" data-target="tabConvert">
        <span>🖼️</span> Convert (Gambar ke PDF)
    </button>
</div>

<!-- ================= 1. TAB MERGE ================= -->
<div class="tool-pane active" id="tabMerge">
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">
        <div class="panel-card">
            <div class="panel-head">
                <h3>Unggah Berkas PDF Yang Akan Digabung</h3>
            </div>
            <div class="panel-body">
                <div class="dropzone-box" id="dropzoneMerge" onclick="document.getElementById('mergeFileInput').click()">
                    <span class="dropzone-icon">📥</span>
                    <h4 style="font-size:15px; font-weight:700; margin-bottom:4px;">Klik untuk memilih file PDF atau tarik ke sini</h4>
                    <p style="font-size:12.5px; color:var(--text-muted);">Pilih 2 atau lebih berkas PDF (misal: Surat Undangan + Lampiran Jadwal/Materi Rapat)</p>
                    <input type="file" id="mergeFileInput" accept=".pdf" multiple style="display:none;">
                </div>

                <div class="file-queue-list" id="mergeFileList">
                    <!-- Files will appear here -->
                </div>

                <div class="progress-bar-wrap" id="mergeProgress">
                    <div class="progress-fill" id="mergeFill"></div>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-head">
                <h3>Opsi Penggabungan</h3>
            </div>
            <div class="panel-body">
                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Nama File Output</label>
                    <input type="text" id="mergeOutName" value="Surat_dan_Lampiran_Tergabung.pdf" class="form-control" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:13.5px;">
                </div>

                <div style="background:#f8fafc; border:1px solid var(--border); border-radius:8px; padding:12px; margin-bottom:20px;">
                    <div style="font-size:12px; color:var(--text-muted); line-height:1.5;">
                        <b>🔒 100% Client-Side Processing:</b><br>
                        Berkas tidak dikirimkan ke server eksternal mana pun, seluruh proses penggabungan dieksekusi secara lokal di peramban Anda.
                    </div>
                </div>

                <button type="button" id="btnExecuteMerge" class="btn-quick-create" style="width:100%; justify-content:center; padding:12px;" disabled>
                    📎 Gabungkan & Unduh PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= 2. TAB SPLIT ================= -->
<div class="tool-pane" id="tabSplit">
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">
        <div class="panel-card">
            <div class="panel-head">
                <h3>Pilih Berkas PDF Yang Akan Dipecah</h3>
            </div>
            <div class="panel-body">
                <div class="dropzone-box" id="dropzoneSplit" onclick="document.getElementById('splitFileInput').click()">
                    <span class="dropzone-icon">✂️</span>
                    <h4 style="font-size:15px; font-weight:700; margin-bottom:4px;">Klik untuk memilih file PDF</h4>
                    <p style="font-size:12.5px; color:var(--text-muted);">Pilih 1 berkas PDF yang ingin diambil halaman tertentu</p>
                    <input type="file" id="splitFileInput" accept=".pdf" style="display:none;">
                </div>

                <div id="splitFileInfo" style="display:none; margin-bottom: 20px;">
                    <div class="file-item-card">
                        <div class="file-item-name">
                            <span>📄</span> <span id="splitName">-</span>
                        </div>
                        <span class="badge-unit" id="splitPageCount">0 Halaman</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-head">
                <h3>Rentang Halaman</h3>
            </div>
            <div class="panel-body">
                <div style="margin-bottom: 14px;">
                    <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Halaman Yang Ingin Diambil</label>
                    <input type="text" id="splitPagesInput" placeholder="Contoh: 1, 3-5, 8" class="form-control" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:13.5px;">
                    <small style="color:var(--text-muted); font-size:11.5px; margin-top:4px; display:block;">Gunakan tanda koma (,) untuk memisah atau tanda strip (-) untuk rentang.</small>
                </div>

                <button type="button" id="btnExecuteSplit" class="btn-quick-create" style="width:100%; justify-content:center; padding:12px;" disabled>
                    ✂️ Ekstrak & Unduh PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= 3. TAB ROTATE ================= -->
<div class="tool-pane" id="tabRotate">
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">
        <div class="panel-card">
            <div class="panel-head">
                <h3>Pilih Berkas PDF Yang Akan Diputar</h3>
            </div>
            <div class="panel-body">
                <div class="dropzone-box" id="dropzoneRotate" onclick="document.getElementById('rotateFileInput').click()">
                    <span class="dropzone-icon">🔄</span>
                    <h4 style="font-size:15px; font-weight:700; margin-bottom:4px;">Klik untuk memilih file PDF</h4>
                    <p style="font-size:12.5px; color:var(--text-muted);">Perbaiki orientasi hasil scan yang terbalik atau miring</p>
                    <input type="file" id="rotateFileInput" accept=".pdf" style="display:none;">
                </div>

                <div id="rotateFileInfo" style="display:none; margin-bottom: 20px;">
                    <div class="file-item-card">
                        <div class="file-item-name">
                            <span>📄</span> <span id="rotateName">-</span>
                        </div>
                        <span class="badge-unit" id="rotatePageCount">0 Halaman</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-head">
                <h3>Sudut Rotasi</h3>
            </div>
            <div class="panel-body">
                <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:20px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer;">
                        <input type="radio" name="rotateDegree" value="90" checked> Putar 90° Searah Jarum Jam (Kanan)
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer;">
                        <input type="radio" name="rotateDegree" value="180"> Putar 180° (Jungkir Balik)
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer;">
                        <input type="radio" name="rotateDegree" value="270"> Putar 90° Berlawanan Jarum Jam (Kiri)
                    </label>
                </div>

                <button type="button" id="btnExecuteRotate" class="btn-quick-create" style="width:100%; justify-content:center; padding:12px;" disabled>
                    🔄 Putar & Unduh PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= 4. TAB CONVERT (IMAGE TO PDF) ================= -->
<div class="tool-pane" id="tabConvert">
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">
        <div class="panel-card">
            <div class="panel-head">
                <h3>Pilih Gambar (JPG / JPEG / PNG) Yang Akan Diubah Menjadi PDF</h3>
            </div>
            <div class="panel-body">
                <div class="dropzone-box" id="dropzoneConvert" onclick="document.getElementById('convertFileInput').click()">
                    <span class="dropzone-icon">🖼️</span>
                    <h4 style="font-size:15px; font-weight:700; margin-bottom:4px;">Klik untuk memilih gambar atau tarik ke sini</h4>
                    <p style="font-size:12.5px; color:var(--text-muted);">Dapat memilih beberapa gambar foto dokumentasi lapangan, scan nota, atau lampiran</p>
                    <input type="file" id="convertFileInput" accept="image/jpeg,image/png,image/jpg" multiple style="display:none;">
                </div>

                <div class="file-queue-list" id="convertFileList">
                    <!-- Files will appear here -->
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-head">
                <h3>Pengaturan Format Dokumen PDF</h3>
            </div>
            <div class="panel-body">
                <div style="margin-bottom: 14px;">
                    <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Nama Berkas Output</label>
                    <input type="text" id="convertOutName" value="Dokumentasi_Lampiran.pdf" class="form-control" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:13.5px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Orientasi Kertas A4</label>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
                            <input type="radio" name="convertOrient" value="portrait" checked> Portrait (Tegak A4 Standar)
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
                            <input type="radio" name="convertOrient" value="landscape"> Landscape (Melebar)
                        </label>
                    </div>
                </div>

                <button type="button" id="btnExecuteConvert" class="btn-quick-create" style="width:100%; justify-content:center; padding:12px;" disabled>
                    🖼️ Konversi ke PDF & Unduh
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- PDF-lib Engine (Extracted locally) -->
<script src="{{ asset('js/pdf-lib.min.js') }}"></script>

<script>
(function(){
    const { PDFDocument, rgb, degrees } = PDFLib;

    // ================= TABS SWITCHER =================
    const tabBtns = document.querySelectorAll('.pdf-tab-btn');
    const panes = document.querySelectorAll('.tool-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            const targetId = btn.getAttribute('data-target');
            document.getElementById(targetId).classList.add('active');
        });
    });

    // Helper: Format bytes
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // ================= MERGE LOGIC =================
    const mergeFiles = [];
    const mergeInput = document.getElementById('mergeFileInput');
    const mergeList = document.getElementById('mergeFileList');
    const btnMerge = document.getElementById('btnExecuteMerge');

    function renderMergeQueue() {
        mergeList.innerHTML = '';
        mergeFiles.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'file-item-card';
            item.innerHTML = `
                <div class="file-item-name">
                    <span>📄</span>
                    <span>${file.name}</span>
                    <span class="file-item-size">(${formatBytes(file.size)})</span>
                </div>
                <button type="button" class="btn-remove-file" data-index="${index}">✕</button>
            `;
            mergeList.appendChild(item);
        });

        document.querySelectorAll('.btn-remove-file').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = Number(e.target.getAttribute('data-index'));
                mergeFiles.splice(idx, 1);
                renderMergeQueue();
            });
        });

        btnMerge.disabled = mergeFiles.length < 2;
    }

    mergeInput.addEventListener('change', (e) => {
        for (let file of e.target.files) {
            mergeFiles.push(file);
        }
        mergeInput.value = '';
        renderMergeQueue();
    });

    btnMerge.addEventListener('click', async () => {
        try {
            btnMerge.disabled = true;
            btnMerge.innerText = 'Menggabungkan Berkas...';

            const mergedPdf = await PDFDocument.create();

            for (let file of mergeFiles) {
                const arrayBuffer = await file.arrayBuffer();
                const pdf = await PDFDocument.load(arrayBuffer);
                const copiedPages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                copiedPages.forEach((page) => mergedPdf.addPage(page));
            }

            const pdfBytes = await mergedPdf.save();
            const blob = new Blob([pdfBytes], { type: 'application/pdf' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = document.getElementById('mergeOutName').value || 'Surat_dan_Lampiran_Tergabung.pdf';
            link.click();

            btnMerge.innerText = '📎 Gabungkan & Unduh PDF';
            btnMerge.disabled = false;
        } catch (err) {
            alert('Gagal menggabungkan PDF: ' + err.message);
            btnMerge.innerText = '📎 Gabungkan & Unduh PDF';
            btnMerge.disabled = false;
        }
    });

    // ================= SPLIT LOGIC =================
    let splitFile = null;
    let splitDoc = null;
    const splitInput = document.getElementById('splitFileInput');
    const btnSplit = document.getElementById('btnExecuteSplit');

    splitInput.addEventListener('change', async function(e){
        if (e.target.files.length) {
            splitFile = e.target.files[0];
            document.getElementById('splitName').innerText = splitFile.name;
            const buffer = await splitFile.arrayBuffer();
            splitDoc = await PDFDocument.load(buffer);
            document.getElementById('splitPageCount').innerText = `${splitDoc.getPageCount()} Halaman Terdeteksi`;
            document.getElementById('splitFileInfo').style.display = 'block';
            btnSplit.disabled = false;
        }
    });

    btnSplit.addEventListener('click', async function(){
        try {
            const raw = document.getElementById('splitPagesInput').value.trim();
            if (!raw) {
                alert('Tentukan nomor halaman yang ingin diekstrak (contoh: 1, 3-5)');
                return;
            }

            btnSplit.disabled = true;
            btnSplit.innerText = 'Mengekstrak Halaman...';

            const total = splitDoc.getPageCount();
            const targetIndices = new Set();

            raw.split(',').forEach(part => {
                part = part.trim();
                if (part.includes('-')) {
                    const [s, e] = part.split('-').map(Number);
                    for (let i = s; i <= e; i++) {
                        if (i >= 1 && i <= total) targetIndices.add(i - 1);
                    }
                } else {
                    const num = Number(part);
                    if (num >= 1 && num <= total) targetIndices.add(num - 1);
                }
            });

            if (targetIndices.size === 0) {
                alert('Tidak ada halaman valid yang dipilih.');
                btnSplit.disabled = false;
                btnSplit.innerText = '✂️ Ekstrak & Unduh PDF';
                return;
            }

            const sortedIndices = Array.from(targetIndices).sort((a,b) => a - b);
            const newPdf = await PDFDocument.create();
            const copiedPages = await newPdf.copyPages(splitDoc, sortedIndices);
            copiedPages.forEach(p => newPdf.addPage(p));

            const bytes = await newPdf.save();
            const blob = new Blob([bytes], { type: 'application/pdf' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'Extracted_' + splitFile.name;
            link.click();

            btnSplit.innerText = '✂️ Ekstrak & Unduh PDF';
            btnSplit.disabled = false;
        } catch(err) {
            alert('Gagal mengekstrak: ' + err.message);
            btnSplit.innerText = '✂️ Ekstrak & Unduh PDF';
            btnSplit.disabled = false;
        }
    });

    // ================= ROTATE LOGIC =================
    let rotateFile = null;
    let rotateDoc = null;
    const rotateInput = document.getElementById('rotateFileInput');
    const btnRotate = document.getElementById('btnExecuteRotate');

    rotateInput.addEventListener('change', async function(e){
        if (e.target.files.length){
            rotateFile = e.target.files[0];
            document.getElementById('rotateName').innerText = rotateFile.name;
            const buffer = await rotateFile.arrayBuffer();
            rotateDoc = await PDFDocument.load(buffer);
            document.getElementById('rotatePageCount').innerText = `${rotateDoc.getPageCount()} Halaman Terdeteksi`;
            document.getElementById('rotateFileInfo').style.display = 'block';
            btnRotate.disabled = false;
        }
    });

    btnRotate.addEventListener('click', async function(){
        try {
            btnRotate.disabled = true;
            btnRotate.innerText = 'Memutar Dokumen...';

            const deg = Number(document.querySelector('input[name="rotateDegree"]:checked').value);
            const pages = rotateDoc.getPages();

            pages.forEach(page => {
                const current = page.getRotation().angle;
                page.setRotation(degrees(current + deg));
            });

            const bytes = await rotateDoc.save();
            const blob = new Blob([bytes], { type: 'application/pdf' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'Rotated_' + rotateFile.name;
            link.click();

            btnRotate.innerText = '🔄 Putar & Unduh PDF';
            btnRotate.disabled = false;
        } catch(err) {
            alert('Gagal memutar: ' + err.message);
            btnRotate.innerText = '🔄 Putar & Unduh PDF';
            btnRotate.disabled = false;
        }
    });

    // ================= 4. CONVERT IMAGE TO PDF LOGIC =================
    const convertFiles = [];
    const convertInput = document.getElementById('convertFileInput');
    const convertList = document.getElementById('convertFileList');
    const btnConvert = document.getElementById('btnExecuteConvert');

    function renderConvertQueue() {
        convertList.innerHTML = '';
        convertFiles.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'file-item-card';
            item.innerHTML = `
                <div class="file-item-name">
                    <span>🖼️</span>
                    <span>${file.name}</span>
                    <span class="file-item-size">(${formatBytes(file.size)})</span>
                </div>
                <button type="button" class="btn-remove-file" data-cindex="${index}">✕</button>
            `;
            convertList.appendChild(item);
        });

        document.querySelectorAll('[data-cindex]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = Number(e.target.getAttribute('data-cindex'));
                convertFiles.splice(idx, 1);
                renderConvertQueue();
            });
        });

        btnConvert.disabled = convertFiles.length === 0;
    }

    convertInput.addEventListener('change', (e) => {
        for (let file of e.target.files) {
            convertFiles.push(file);
        }
        convertInput.value = '';
        renderConvertQueue();
    });

    btnConvert.addEventListener('click', async () => {
        try {
            btnConvert.disabled = true;
            btnConvert.innerText = 'Mengonversi Gambar ke PDF...';

            const orient = document.querySelector('input[name="convertOrient"]:checked').value;
            const pdfDoc = await PDFDocument.create();
            // A4 dimensions in points: 595.28 x 841.89
            const pageWidth = orient === 'landscape' ? 841.89 : 595.28;
            const pageHeight = orient === 'landscape' ? 595.28 : 841.89;

            for (let file of convertFiles) {
                const arrayBuffer = await file.arrayBuffer();
                let img;
                if (file.type === 'image/jpeg' || file.name.toLowerCase().endsWith('.jpg') || file.name.toLowerCase().endsWith('.jpeg')) {
                    img = await pdfDoc.embedJpg(arrayBuffer);
                } else {
                    img = await pdfDoc.embedPng(arrayBuffer);
                }

                const page = pdfDoc.addPage([pageWidth, pageHeight]);
                const margin = 36; // 0.5 inch margin
                const maxW = pageWidth - (margin * 2);
                const maxH = pageHeight - (margin * 2);

                const dims = img.scaleToFit(maxW, maxH);
                page.drawImage(img, {
                    x: (pageWidth - dims.width) / 2,
                    y: (pageHeight - dims.height) / 2,
                    width: dims.width,
                    height: dims.height
                });
            }

            const pdfBytes = await pdfDoc.save();
            const blob = new Blob([pdfBytes], { type: 'application/pdf' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = document.getElementById('convertOutName').value || 'Dokumentasi_Lampiran.pdf';
            link.click();

            btnConvert.innerText = '🖼️ Konversi ke PDF & Unduh';
            btnConvert.disabled = false;
        } catch (err) {
            alert('Gagal mengonversi gambar ke PDF: ' + err.message);
            btnConvert.innerText = '🖼️ Konversi ke PDF & Unduh';
            btnConvert.disabled = false;
        }
    });

})();
</script>
@endpush
