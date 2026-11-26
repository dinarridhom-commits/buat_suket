var DocGen = (function () {
  var BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  var HARI = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

  function fmtTgl(iso) {
    if (!iso) return '';
    var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso);
    if (!m) return iso;
    return parseInt(m[3], 10) + ' ' + BULAN[parseInt(m[2], 10) - 1] + ' ' + m[1];
  }
  function fmtHariTgl(iso) {
    var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso || '');
    if (!m) return '';
    var d = new Date(+m[1], +m[2] - 1, +m[3]);
    return HARI[d.getDay()] + ', ' + fmtTgl(iso);
  }
  function terbilang(n) {
    n = Math.floor(Math.abs(+n || 0));
    var s = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    if (n < 12) return s[n];
    if (n < 20) return terbilang(n - 10) + ' Belas';
    if (n < 100) return (terbilang(Math.floor(n / 10)) + ' Puluh ' + terbilang(n % 10)).trim();
    if (n < 200) return ('Seratus ' + terbilang(n - 100)).trim();
    if (n < 1000) return (terbilang(Math.floor(n / 100)) + ' Ratus ' + terbilang(n % 100)).trim();
    return String(n);
  }

  /* ---------- XML helpers ---------- */
  function esc(s) {
    return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
  function rPr(o) {
    o = o || {};
    var x = '<w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>';
    if (o.b) x += '<w:b/><w:bCs/>';
    if (o.i) x += '<w:i/><w:iCs/>';
    if (o.strike) x += '<w:strike/>';
    if (o.sz) x += '<w:sz w:val="' + o.sz + '"/><w:szCs w:val="' + o.sz + '"/>';
    if (o.u) x += '<w:u w:val="single"/>';
    return '<w:rPr>' + x + '</w:rPr>';
  }
  // teks dengan \n -> line break, \t -> tab
  function r(text, o) {
    var out = '', rp = rPr(o);
    String(text == null ? '' : text).split('\n').forEach(function (line, i) {
      if (i > 0) out += '<w:r>' + rp + '<w:br/></w:r>';
      line.split('\t').forEach(function (seg, j) {
        if (j > 0) out += '<w:r>' + rp + '<w:tab/></w:r>';
        if (seg) out += '<w:r>' + rp + '<w:t xml:space="preserve">' + esc(seg) + '</w:t></w:r>';
      });
    });
    return out;
  }
  function p(runs, o) {
    o = o || {};
    var pp = '';
    if (o.keepNext) pp += '<w:keepNext/>';
    if (o.numId) pp += '<w:numPr><w:ilvl w:val="0"/><w:numId w:val="' + o.numId + '"/></w:numPr>';
    if (o.tabs) pp += '<w:tabs>' + o.tabs.map(function (t) { return '<w:tab w:val="' + (t[1] || 'left') + '"' + (t[2] ? ' w:leader="' + t[2] + '"' : '') + ' w:pos="' + t[0] + '"/>'; }).join('') + '</w:tabs>';
    var sp = o.sp || {};
    pp += '<w:spacing w:before="' + (sp.before || 0) + '" w:after="' + (sp.after || 0) + '" w:line="' + (sp.line || 240) + '" w:lineRule="' + (sp.rule || 'auto') + '"/>';
    if (o.ind) {
      var i = o.ind;
      pp += '<w:ind' + (i.left != null ? ' w:left="' + i.left + '"' : '') + (i.right != null ? ' w:right="' + i.right + '"' : '') +
        (i.hanging != null ? ' w:hanging="' + i.hanging + '"' : '') + (i.firstLine != null ? ' w:firstLine="' + i.firstLine + '"' : '') + '/>';
    }
    if (o.jc) pp += '<w:jc w:val="' + o.jc + '"/>';
    pp += rPr({ sz: o.sz });
    if (o.sect) pp += o.sect;
    return '<w:p><w:pPr>' + pp + '</w:pPr>' + (runs || '') + '</w:p>';
  }
  function sumW(a) { return a.reduce(function (x, y) { return x + y; }, 0); }
  function scaleCols(base, total) {
    var s = sumW(base), out = base.map(function (v) { return Math.round(v * total / s); });
    out[out.length - 1] += total - sumW(out);
    return out;
  }
  function tbl(widths, rows, o) {
    o = o || {};
    var total = sumW(widths), cm = o.cm == null ? 108 : o.cm;
    var bv = o.borders ? 'single' : 'nil';
    var tb = ['top', 'left', 'bottom', 'right', 'insideH', 'insideV'].map(function (k) {
      if (k === 'bottom' && o.bottomRule) return '<w:bottom w:val="single" w:sz="' + o.bottomRule + '" w:space="0" w:color="000000"/>';
      return '<w:' + k + ' w:val="' + bv + '" w:sz="4" w:space="0" w:color="000000"/>';
    }).join('');
    var x = '<w:tbl><w:tblPr><w:tblW w:w="' + total + '" w:type="dxa"/>' +
      (o.indent != null ? '<w:tblInd w:w="' + o.indent + '" w:type="dxa"/>' : '') +
      '<w:tblBorders>' + tb + '</w:tblBorders><w:tblLayout w:type="fixed"/>' +
      '<w:tblCellMar><w:top w:w="0" w:type="dxa"/><w:left w:w="' + cm + '" w:type="dxa"/><w:bottom w:w="0" w:type="dxa"/><w:right w:w="' + cm + '" w:type="dxa"/></w:tblCellMar>' +
      '<w:tblLook w:val="0000"/></w:tblPr><w:tblGrid>' + widths.map(function (w) { return '<w:gridCol w:w="' + w + '"/>'; }).join('') + '</w:tblGrid>';
    rows.forEach(function (row) {
      x += '<w:tr>';
      var trp = '';
      if (row.cantSplit) trp += '<w:cantSplit/>';
      if (row.h) trp += '<w:trHeight w:val="' + row.h + '" w:hRule="' + (row.hRule || 'atLeast') + '"/>';
      if (trp) x += '<w:trPr>' + trp + '</w:trPr>';
      var ci = 0;
      row.cells.forEach(function (c) {
        var span = c.span || 1, w = sumW(widths.slice(ci, ci + span));
        ci += span;
        var tc = '<w:tcW w:w="' + w + '" w:type="dxa"/>';
        if (span > 1) tc += '<w:gridSpan w:val="' + span + '"/>';
        if (c.vMerge) tc += c.vMerge === 'restart' ? '<w:vMerge w:val="restart"/>' : '<w:vMerge/>';
        if (c.borders != null) tc += '<w:tcBorders>' + ['top', 'left', 'bottom', 'right'].map(function (k) {
          return '<w:' + k + ' w:val="' + (c.borders.indexOf(k.charAt(0)) >= 0 ? 'single' : 'nil') + '" w:sz="' + (c.bsz || 4) + '" w:space="0" w:color="000000"/>';
        }).join('') + '</w:tcBorders>';
        if (c.mar) tc += '<w:tcMar><w:top w:w="' + c.mar[0] + '" w:type="dxa"/><w:left w:w="' + c.mar[1] + '" w:type="dxa"/><w:bottom w:w="' + c.mar[2] + '" w:type="dxa"/><w:right w:w="' + c.mar[3] + '" w:type="dxa"/></w:tcMar>';
        if (c.vAlign) tc += '<w:vAlign w:val="' + c.vAlign + '"/>';
        x += '<w:tc><w:tcPr>' + tc + '</w:tcPr>' + (c.content || '<w:p/>') + '</w:tc>';
      });
      x += '</w:tr>';
    });
    return x + '</w:tbl>';
  }
  var imgSeq = 1;
  function img(rid, wTw, hTw, name) {
    var cx = Math.round(wTw * 635), cy = Math.round(hTw * 635), id = imgSeq++;
    return '<w:r><w:rPr><w:noProof/></w:rPr><w:drawing><wp:inline distT="0" distB="0" distL="0" distR="0"><wp:extent cx="' + cx + '" cy="' + cy + '"/>' +
      '<wp:effectExtent l="0" t="0" r="0" b="0"/><wp:docPr id="' + id + '" name="' + name + ' ' + id + '"/><wp:cNvGraphicFramePr><a:graphicFrameLocks xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" noChangeAspect="1"/></wp:cNvGraphicFramePr>' +
      '<a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">' +
      '<pic:nvPicPr><pic:cNvPr id="0" name="' + name + '.png"/><pic:cNvPicPr><a:picLocks noChangeAspect="1"/></pic:cNvPicPr></pic:nvPicPr><pic:blipFill><a:blip r:embed="' + rid + '"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>' +
      '<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="' + cx + '" cy="' + cy + '"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr></pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing></w:r>';
  }

  function lines(s) { return String(s || '').replace(/\r/g, '').split('\n').map(function (x) { return x.trim(); }).filter(Boolean); }
  function paras(s) { return String(s || '').replace(/\r/g, '').split(/\n\s*\n/).map(function (x) { return x.trim(); }).filter(Boolean); }
  function withComma(s) { s = String(s || '').trim(); return s && !/,$/.test(s) ? s + ',' : s; }
  function noComma(s) { return String(s || '').trim().replace(/,$/, ''); }

  /* ---------- KOP SURAT (teks Arial + logo, seragam untuk semua kop) ----------
     baris 1: 18 pt · baris 2: 12 pt · baris 3: 8→6 pt (mengecil otomatis agar muat 1 baris) · baris 4 (alamat): 7,5 pt */
  var ARIALW = null;   // lebar glyph Arial (per 1000 em), diisi dari luar
  function textW(s, bold, pt) {
    var t = ARIALW && ARIALW[bold ? 'b' : 'r'];
    var w = 0; s = String(s || '');
    for (var i = 0; i < s.length; i++) w += t ? (t[s.charAt(i)] || 556) : (bold ? 640 : 560);
    return w / 1000 * pt * 20;   // twip
  }
  function fitPt(s, bold, maxPt, minPt, availTw) {
    for (var pt = maxPt; pt > minPt; pt -= 0.5) if (textW(s, bold, pt) <= availTw) return pt;
    return minPt;
  }
  var LOGO_SZ = 1276, LOGO_GAP = 170;   // logo bawaan 2,25 cm; jarak logo ke garis/teks
  function logoTw(k) { return k.logoTw || LOGO_SZ; }
  function kopLines(k) {
    var L = [{ t: k.line1, pt: 13.5, h: 300 }, { t: k.line2, pt: 9.5, h: 220 }];
    var l3 = (k.line3 || []).filter(function (s) { return String(s).trim(); });
    l3.forEach(function (t, i) { L.push({ t: t, pt: 8, min: 6, b: l3.length === 1 || i === l3.length - 1, h: 170 }); });
    L.push({ t: k.line4, pt: 6.5, h: 160, last: true });
    return L;
  }
  function kopHeight(k) { return Math.max(logoTw(k), sumW(kopLines(k).map(function (l) { return l.h; })) + 60); }
  function kopXml(k, width, rid, o) {
    o = o || {};
    var LS = logoTw(k), logoCol = LS + LOGO_GAP, textCol = width - logoCol;
    var content = kopLines(k).map(function (l) {
      var pt = l.min ? fitPt(l.t, l.b, l.pt, l.min, textCol - 40) : l.pt;
      var hs = Math.round(pt * 2);
      var hh = l.min ? Math.max(150, Math.round(l.h * pt / 8)) : l.h;
      return p(r(l.t, { sz: hs, b: l.b }), { sz: hs, jc: 'center', sp: { line: hh, rule: 'exact', after: l.last ? 60 : 0 } });
    }).join('');
    // garis bawah kop hanya di bawah kolom teks (mulai sedikit di kanan logo), sejajar tepi bawah logo
    return tbl([logoCol, textCol], [{ cantSplit: true, cells: [
      { vAlign: 'bottom', borders: '', content: p(img(rid, LS, LS, 'LogoPU'), {}) },
      { vAlign: 'bottom', borders: 'b', bsz: 12, content: content }
    ] }], { cm: 0, indent: o.indent });
  }

  /* isi surat sebagai daftar bagian; draf lama (pembuka/rincian/penutup) dikonversi otomatis */
  function bodyOf(d) {
    if (Array.isArray(d.body)) return d.body;
    return [
      { type: 'paragraph', indent: true, text: d.pembuka || '' },
      { type: 'points', items: d.rincian || [] },
      { type: 'paragraph', indent: false, text: d.penutup || '' }
    ];
  }

  /* ---------- isi surat (dipakai di hal konsep [mini], paraf, asli) ---------- */
  function letterBody(d, o) {
    var S = o.S, W = o.width, x = '';
    var NL = o.nextList;
    if (o.withKop && o.kop) {
      x += kopXml(o.kop, W, o.logoRid || 'rIdLogo');
      x += p('', { sz: 12, sp: { line: 120, rule: 'exact', after: 140 } });
    } else if (!o.mini) {
      x += p('', { sz: 12 });
    }
    function cp(t, e) { e = e || {}; return p(r(t, { sz: S, b: e.b }), { sz: S, sp: { line: 276 }, jc: e.jc }); }
    // blok Nomor / Sifat / Lampiran / Hal
    var hw = scaleCols([1150, 220, 4330, 3390], W);
    var tt = (d.kota ? d.kota + ', ' : '') + fmtTgl(d.tanggal);
    var lamp = (+d.lampiran > 0) ? (d.lampiran + ' Lembar') : '-';
    var rows = [['Nomor', d.nomor, tt], ['Sifat', d.sifat, ''], ['Lampiran', lamp, ''], ['Hal', d.hal, '']];
    x += tbl(hw, rows.map(function (rw, rIdx) {
      return { cells: [{ content: cp(rw[0]) }, { content: cp(':') }, { content: cp(rw[1], { b: rIdx === 3 }) }, { content: cp(rw[2], { jc: 'right' }) }] };
    }), { cm: 0 });
    var gap = d.gapYth == null || d.gapYth === '' ? 240 : +d.gapYth;   // jarak Hal -> Yth. (240 = 1 baris)
    x += p(r('Yth.', { sz: S }), { sz: S, sp: { line: 276, before: o.mini ? Math.round(gap * 0.75) : gap } });
    if (d.ythMode === 'langsung') {
      var yl = lines(d.ythText);
      if (yl.length) {
        var yNid = NL();
        yl.forEach(function (t, i) {
          x += p(r(t, { sz: S }), { sz: S, numId: yNid, ind: { left: 426, hanging: 284 }, sp: { line: 276, after: i === yl.length - 1 ? 100 : 0 } });
        });
      } else {
        x += p(r('', { sz: S }), { sz: S, sp: { line: 276, after: 100 } });
      }
    } else {
      x += p(r('(Daftar Terlampir)', { sz: S }), { sz: S, sp: { line: 276, after: 100 } });
    }
    x += p(r('di -', { sz: S }), { sz: S, sp: { after: 60, line: 276 } });
    x += p(r(d.diTempat || 'Tempat', { sz: S, b: true }), { sz: S, ind: { left: 284 }, sp: { after: 180, line: 276 } });
    // isi surat: daftar bagian (paragraf / poin)
    bodyOf(d).forEach(function (sec) {
      if (sec.type === 'points') {
        var rin = (sec.items || []).filter(function (it) { return (it.label || '').trim() || (it.value || '').trim(); });
        rin.forEach(function (it, i) {
          var val = String(it.value || '').replace(/\r/g, '').replace(/^\s*\n|\n\s*$/g, '').split('\n').map(function (q) { return q.trim(); }).join('\n');
          x += p(r((it.label || '') + '\t:\t' + val, { sz: S }), {
            sz: S, tabs: [[2127], [2410]], ind: { left: 2410, hanging: 1843 },
            sp: { line: 276, after: i === rin.length - 1 ? 160 : 0 }
          });
        });
      } else {
        paras(sec.text).forEach(function (t) {
          x += p(r(t.replace(/\n/g, ' '), { sz: S }), { sz: S, jc: 'both', ind: sec.indent ? { firstLine: 567 } : null, sp: { after: 160, line: 276 } });
        });
      }
    });
    x += p('', { sz: S });
    // tanda tangan (+ kotak paraf bila halaman paraf)
    var nameP = p(r(d.ttd_nama, { sz: S, b: true }), { sz: S, jc: 'center' });
    if (d.showNip && d.ttd_nip) nameP += p(r('NIP. ' + d.ttd_nip, { sz: S }), { sz: S, jc: 'center' });
    var jabP = p(r(String(d.ttd_jab || '').trim(), { sz: S }), { sz: S, jc: 'center' });
    if (o.mini) {
      var sw = scaleCols([3235, 1600, 4070], W);
      x += tbl(sw, [
        { cantSplit: true, cells: [{}, {}, { content: jabP }] },
        { cantSplit: true, h: 1050, cells: [{}, {}, { vAlign: 'bottom', content: nameP }] }
      ], { cm: 108 });
    } else if (!o.paraf) {
      var leftW = Math.round(W * 0.48), rightW = W - leftW;
      x += tbl([leftW, rightW], [
        { cantSplit: true, cells: [{}, { content: jabP }] },
        { cantSplit: true, h: 1140, cells: [{}, {}] },
        { cantSplit: true, cells: [{}, { vAlign: 'bottom', content: nameP }] }
      ], { cm: 0 });
    } else {
      // tabel menjorok dengan kotak paraf kecil di kiri
      var g = scaleCols([1350, 1980, 1024, 4723], W), ind = g[0], cols = g.slice(1);
      var PB = 18, box = 'tlbr';
      var konfJab = (d.konf_jabParaf || '').trim() || d.konf_jab;
      var sp0 = p('', { sz: 2, sp: { line: 20, rule: 'exact' } });
      x += tbl(cols, [
        { cantSplit: true, cells: [{}, {}, { content: jabP }] },
        { cantSplit: true, h: 260, hRule: 'exact', cells: [{ content: sp0 }, { content: sp0 }, { content: sp0 }] },
        { cantSplit: true, cells: [
          { borders: box, mar: [0, 40, 0, 40], content: p(r(konfJab, { sz: PB }), { sz: PB, jc: 'center', sp: { before: 20, after: 40 } }) },
          {}, {}] },
        { cantSplit: true, h: 830, cells: [
          { borders: box, mar: [0, 40, 0, 40], vAlign: 'bottom', content: p(r(d.konf_nama, { sz: PB, b: true }), { sz: PB, jc: 'center', sp: { after: 40 } }) },
          {}, {}] },
        { cantSplit: true, h: 560, cells: [{}, {}, {}] },
        { cantSplit: true, cells: [{}, {}, { vAlign: 'bottom', content: nameP }] }
      ], { cm: 108, indent: ind });
    }
    // tembusan
    var tb = lines(d.tembusan);
    if (tb.length) {
      var nid = NL();
      x += p(r('Tembusan:', { sz: S }), { sz: S, sp: { before: 200 }, keepNext: true });
      tb.forEach(function (t) { x += p(r(t, { sz: S }), { sz: S, numId: nid, ind: { left: 426, hanging: 284 } }); });
    }
    return x;
  }

  /* ---------- halaman konsep ---------- */
  function konsepPage(d, L, ctx) {
    var W = L.W, x = '';
    var kopW = Math.round(W * 0.78), dlW = W - kopW;   // 78%: alamat 7,5 pt tetap 1 baris di kop Satker
    x += tbl([kopW, dlW], [{ cells: [
      { vAlign: 'bottom', content: kopXml(ctx.kop, kopW, 'rIdLogo') + p('', { sz: 2, sp: { line: 20, rule: 'exact' } }) },
      { vAlign: 'bottom', content: p(r('DEADLINE:', { sz: 22 }), { sz: 22, ind: { left: 200 } }) + p(r(fmtTgl(d.deadline) || ' ', { sz: 22 }), { sz: 22, ind: { left: 200 }, sp: { after: 80 } }) }
    ] }], { cm: 0 });
    x += p('', { sz: 12 });
    var cw = scaleCols([3016, 2473, 2564, 2930], W);
    var topH = kopHeight(ctx.kop) + 260;
    var baseH = [1432, 1563, 1574, 2160, 3265, 3700], bs = sumW(baseH);
    var f = Math.min(1, (L.H - topH - 260) / bs);
    var H = baseH.map(function (h) { return Math.round(h * f); });
    function cp(t, e) { e = e || {}; return p(r(t, { sz: e.sz || 20, b: e.b }), { sz: e.sz || 20, jc: e.jc, sp: { line: e.line || 276, after: e.after || 0 } }); }
    var C = { jc: 'center' };
    function blanks(n) { var s = ''; for (var i = 0; i < n; i++) s += cp(''); return s; }
    var miniW = sumW(cw.slice(1)) - 2 * 108;
    var mini = letterBody(d, { S: 18, width: miniW, paraf: false, mini: true, nextList: ctx.nextList });
    var lembar = (+d.jumlahLembar || 0);
    x += tbl(cw, [
      { h: H[0], cells: [
        { vMerge: 'restart', content: cp('Konsep ini setelah suratnya dikirim harap dikembalikan kepada :') + blanks(2) + cp(d.kembali) },
        { vMerge: 'restart', content: cp('Konfirmasi/', C) + cp('paraf/tanggal', C) + blanks(1) + cp(d.konf_jab, C) + blanks(1) + cp(d.konf_nama, C) },
        { vMerge: 'restart', content: cp('Pemeriksa Akhir', C) + cp('Paraf & Tanggal', C) + blanks(1) + cp(d.pem_jab, C) + blanks(2) + cp(d.pem_nama, C) },
        { content: cp('Supaya diajukan lagi tanggal', { line: 360 }) + blanks(2) + cp(fmtTgl(d.ajukan) || '…………………………', { jc: 'center', line: 360 }) }] },
      { h: H[1], cells: [{ vMerge: 'cont' }, { vMerge: 'cont' }, { vMerge: 'cont' },
        { content: blanks(1) + cp('Ditetapkan:', C) + blanks(2) + cp(d.tetap_nama, C) }] },
      { h: H[2], cells: [
        { content: cp('Jumlah') + blanks(1) + cp('Lembar : ' + (lembar ? lembar + ' (' + terbilang(lembar) + ')' : '')) },
        { span: 3, vMerge: 'restart', content: mini }] },
      { h: H[3], cells: [
        { content: cp('No.') + cp('Kode Klasifikasi :') + cp('NOMOR', C) + cp(d.nomor, C) },
        { span: 3, vMerge: 'cont' }] },
      { h: H[4], cells: [
        { content: cp('Konsep/Penulis Redaksi') + cp('dari:', { after: 120 }) + cp(d.pen_jab, C) + blanks(4) + cp(d.pen_nama, C) },
        { span: 3, vMerge: 'cont' }] },
      { h: H[5], cells: [
        { content: cp('Diketik Oleh :') + blanks(5) + cp(d.ketik_nama, C) },
        { span: 3, vMerge: 'cont' }] }
    ], { borders: true, cm: 108 });
    return x;
  }

  /* ---------- lampiran daftar undangan (tanpa kop) ---------- */
  function lampiranPage(d, L, ctx) {
    var S = 22, x = '';
    var li = Math.round(L.W * 4410 / 9026), lt = li + 1350;
    x += p(r('Lampiran I Surat ' + noComma(d.ttd_jab), { sz: S }), { sz: S, ind: { left: li } });
    x += p(r('Nomor\t: ' + d.nomor, { sz: S }), { sz: S, ind: { left: li }, tabs: [[lt]] });
    x += p(r('Tanggal\t: ' + fmtTgl(d.tanggal), { sz: S }), { sz: S, ind: { left: li }, tabs: [[lt]] });
    x += p('', { sz: S }) + p(r('DAFTAR UNDANGAN', { sz: S, b: true }), { sz: S, jc: 'center' }) + p('', { sz: S });
    var nid = ctx.nextList();
    lines(d.daftarUndangan).forEach(function (t) {
      x += p(r(t, { sz: S }), { sz: S, numId: nid, jc: 'both', ind: { left: 426, hanging: 426 }, sp: { line: 276, after: 80 } });
    });
    return x;
  }

  /* ---------- Surat Keterangan lupa absen (format tetap, kop Balai) ---------- */
  var SUKET_OPSI = [
    ['terlambat', 'saya terlambat masuk kerja'],
    ['pulangawal', 'pulang sebelum waktunya'],
    ['masuk', 'tidak mengisi daftar hadir kedatangan'],
    ['pulang', 'tidak mengisi daftar hadir kepulangan']
  ];
  // pangkat dari database "Penata Muda ( III / a )" -> "Penata Muda / III/a"
  function fmtPangkat(s) {
    s = String(s || '').trim();
    var m = /^(.*?)\s*\/?\s*\(\s*([IV]+)\s*\/\s*([a-e])\s*\)\s*$/i.exec(s);
    return m ? m[1].trim() + ' / ' + m[2].toUpperCase() + '/' + m[3].toLowerCase() : s;
  }
  function suketBody(d, W, ctx) {
    var S = 22, IND = 567, x = '';
    var L = { line: 360 };
    function P(runs, o) { o = o || {}; return p(runs, { sz: S, jc: o.jc || 'both', ind: o.ind === undefined ? { left: IND } : o.ind, tabs: o.tabs, sp: o.sp || L }); }
    x += p('', { sz: S, sp: { line: 360, rule: 'exact' } });
    x += kopXml(ctx.kop, W - IND, 'rIdLogo', { indent: IND });
    x += p('', { sz: S, sp: { line: 240, rule: 'exact', after: 600 } });
    x += P(r('SURAT PERMOHONAN IZIN / PEMBERITAHUAN', { sz: S, b: true }), { jc: 'center' });
    x += P(r('NOMOR ' + (d.sk_nomor || ''), { sz: S }), { jc: 'center', sp: { line: 360, after: 408 } });
    x += P(r('Yang bertandatangan di bawah ini:', { sz: S }));
    var colon = 2835, val = colon + 170;
    [['Nama', d.sk_nama], ['NIP', d.sk_nip], ['Pangkat/Golongan', d.sk_pangkat], ['Jabatan', d.sk_jabatan]].forEach(function (row) {
      x += P(r(row[0] + '\t: ' + (row[1] || ''), { sz: S }), { tabs: [[colon]], ind: { left: val, hanging: val - IND } });
    });
    x += P('');
    // kalimat pernyataan: opsi yang tidak dipilih dicoret
    var validOpsi = ['terlambat', 'pulangawal', 'masuk', 'pulang'];
    var pick = validOpsi.indexOf(d.sk_jenis) >= 0 ? d.sk_jenis : 'masuk';
    var runs = r('Dengan ini menerangkan bahwa pada hari ' + fmtHariTgl(d.sk_tglAbsen).replace(', ', ' tanggal ') + ', ', { sz: S });
    SUKET_OPSI.forEach(function (o, i) {
      var on = o[0] === pick, last = i === SUKET_OPSI.length - 1;
      if (on) runs += r((i ? ' ' : '') + o[1] + (last ? '' : ' '), { sz: S });
      else if (last) runs += r('/ ' + o[1], { sz: S, strike: true });
      else runs += r((i ? ' ' : '') + o[1] + ' /', { sz: S, strike: true });
    });
    runs += r(' (*) karena: ' + (d.sk_alasan || ''), { sz: S });
    x += P(runs);
    x += P('');
    // tanda tangan
    var g = [3780, 1656], cols = g.concat([W - IND - g[0] - g[1]]);
    function c(t, o) { o = o || {}; return p(r(t || '', { sz: S, u: o.u }), { sz: S, jc: 'center', sp: L }); }
    var tgl = (d.sk_kota ? d.sk_kota + ', ' : '') + fmtTgl(d.sk_tglSurat);
    x += tbl(cols, [
      { cells: [{}, {}, { content: c(tgl) }] },
      { cells: [{}, {}, { content: c('Disetujui/Tidak disetujui (*) oleh') }] },
      { cantSplit: true, cells: [{ vAlign: 'bottom', content: c('Pegawai yang bersangkutan,') }, {}, { content: c(d.sk_appJab) }] },
      { cantSplit: true, h: 1250, cells: [{}, {}, {}] },
      { cantSplit: true, cells: [{ content: c(d.sk_nama, { u: true }) }, {}, { content: c(d.sk_appNama, { u: true }) }] },
      { cantSplit: true, cells: [{ content: c(d.sk_nip ? 'NIP. ' + d.sk_nip : '') }, {}, { content: c(d.sk_appNip ? 'NIP. ' + d.sk_appNip : '') }] }
    ], { cm: 0, indent: IND });
    x += P('') + P('') + P('');
    x += P(r('Catatan Alasan Tidak Disetujui:', { sz: S }), { jc: 'left' });
    for (var k = 0; k < 3; k++) x += P(r('\t', { sz: S }), { jc: 'left', tabs: [[W, 'right', 'dot']], sp: { line: 300, after: 84 } });
    x += P('', { sp: { line: 240 } });
    x += P(r('(*) Coret yang tidak perlu', { sz: S }), { jc: 'left', sp: { line: 255, rule: 'exact' } });
    return x;
  }

  /* ---------- package ---------- */
  var NS = 'xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"';

  function sectPr(m, kop) {
    return '<w:sectPr>' + (kop ? '<w:headerReference w:type="first" r:id="rIdHK"/>' : '') + '<w:headerReference w:type="default" r:id="rIdHE"/>' +
      '<w:type w:val="nextPage"/><w:pgSz w:w="11906" w:h="16838" w:code="9"/>' +
      '<w:pgMar w:top="' + m.top + '" w:right="' + m.right + '" w:bottom="' + m.bottom + '" w:left="' + m.left + '" w:header="' + m.header + '" w:footer="0" w:gutter="0"/>' +
      '<w:cols w:space="708"/>' + (kop ? '<w:titlePg/>' : '') + '</w:sectPr>';
  }
  var CM = 1440 / 2.54;   // twip per cm — semua ukuran margin dalam cm
  function marg(mm) { return { top: Math.round(mm.top * CM), bottom: Math.round(mm.bottom * CM), left: Math.round(mm.left * CM), right: Math.round(mm.right * CM), header: Math.round((mm.header == null ? 0.75 : mm.header) * CM) }; }

  /**
   * d: data form; cfg: { kop: {line1,line2,line3:[..],line4}, logo: {b64}, arialW, JSZip, outType }
   */
  function build(d, cfg) {
    imgSeq = 1;
    if (cfg.arialW) ARIALW = cfg.arialW;
    var listCount = 0;
    var ctx = { kop: cfg.kop, nextList: function () { return ++listCount; } };
    var PW = 11906, PH = 16838;
    var body = '', LS = { W: PW - Math.round(2 * 2.54 * CM) };

    if (d.mode === 'suket') {
      var mSk = marg({ top: 1.0, bottom: 1.25, left: 1.0, right: 1.75, header: 0.75 });
      body = suketBody(d, PW - mSk.left - mSk.right, ctx) + sectPr(mSk, false);
    } else {
      var mS = marg(d.margin);
      var mL = marg(Object.assign({ top: 1.0, bottom: 1.0, left: d.margin.left, right: d.margin.right }, d.marginLampiran || {}, { header: 0.4 }));
      var mK = d.konsepMarginTemplate ? marg({ top: 1.5, bottom: 1.0, left: 1.0, right: 1.0, header: 0 }) : marg(d.margin);
      LS = { W: PW - mS.left - mS.right, H: PH - mS.top - mS.bottom };
      var LK = { W: PW - mK.left - mK.right, H: PH - mK.top - mK.bottom };
      var order = d.urutan === 'asli-dulu' ? ['konsep', 'asli', 'paraf'] : ['konsep', 'paraf', 'asli'];
      var pages = order.filter(function (k) { return d.pages[k]; });
      if (d.ythMode !== 'langsung' && d.pages.lampiran && lines(d.daftarUndangan).length) pages.push('lampiran');
      if (!pages.length) throw new Error('Pilih minimal satu halaman untuk dicetak.');
      pages.forEach(function (pg, i) {
        var isLast = i === pages.length - 1, content, sp;
        if (pg === 'konsep') { content = konsepPage(d, LK, ctx); sp = sectPr(mK, false); }
        else if (pg === 'lampiran') { content = lampiranPage(d, LS, ctx); sp = sectPr(mL, false); /* lampiran tanpa kop */ }
        else { content = letterBody(d, { S: 22, width: LS.W, paraf: pg === 'paraf', withKop: true, kop: ctx.kop, logoRid: 'rIdLogo', nextList: ctx.nextList }); sp = sectPr(mS, false); }
        body += content;
        if (isLast) body += sp;
        else body += p('', { sz: 2, sp: { line: 20, rule: 'exact' }, sect: sp });
      });
    }

    var doc = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document ' + NS + '><w:body>' + body + '</w:body></w:document>';
    var hdrKop = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:hdr ' + NS + '>' + kopXml(cfg.kop, LS.W, 'rIdLogoH') + p('', { sz: 2, sp: { line: 20, rule: 'exact' } }) + '</w:hdr>';
    var hdrEmpty = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:hdr ' + NS + '>' + p('', { sz: 2 }) + '</w:hdr>';

    var numXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' +
      '<w:abstractNum w:abstractNumId="0"><w:multiLevelType w:val="singleLevel"/><w:lvl w:ilvl="0"><w:start w:val="1"/><w:numFmt w:val="decimal"/><w:lvlText w:val="%1."/><w:lvlJc w:val="left"/><w:pPr><w:ind w:left="426" w:hanging="284"/></w:pPr></w:lvl></w:abstractNum>';
    for (var n = 1; n <= Math.max(1, listCount); n++) numXml += '<w:num w:numId="' + n + '"><w:abstractNumId w:val="0"/><w:lvlOverride w:ilvl="0"><w:startOverride w:val="1"/></w:lvlOverride></w:num>';
    numXml += '</w:numbering>';

    var styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' +
      '<w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Arial" w:eastAsia="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/><w:lang w:val="id-ID" w:eastAsia="en-US" w:bidi="ar-SA"/></w:rPr></w:rPrDefault>' +
      '<w:pPrDefault><w:pPr><w:spacing w:after="0" w:line="240" w:lineRule="auto"/></w:pPr></w:pPrDefault></w:docDefaults>' +
      '<w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:qFormat/></w:style>' +
      '<w:style w:type="character" w:default="1" w:styleId="DefaultParagraphFont"><w:name w:val="Default Paragraph Font"/><w:uiPriority w:val="1"/><w:semiHidden/></w:style>' +
      '<w:style w:type="table" w:default="1" w:styleId="TableNormal"><w:name w:val="Normal Table"/><w:semiHidden/><w:tblPr><w:tblInd w:w="0" w:type="dxa"/><w:tblCellMar><w:top w:w="0" w:type="dxa"/><w:left w:w="108" w:type="dxa"/><w:bottom w:w="0" w:type="dxa"/><w:right w:w="108" w:type="dxa"/></w:tblCellMar></w:tblPr></w:style>' +
      '</w:styles>';
    var settings = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:defaultTabStop w:val="720"/><w:characterSpacingControl w:val="doNotCompress"/><w:compat><w:compatSetting w:name="compatibilityMode" w:uri="http://schemas.microsoft.com/office/word" w:val="15"/></w:compat></w:settings>';
    var REL = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/';
    var docRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' +
      '<Relationship Id="rIdSt" Type="' + REL + 'styles" Target="styles.xml"/><Relationship Id="rIdNum" Type="' + REL + 'numbering" Target="numbering.xml"/>' +
      '<Relationship Id="rIdSet" Type="' + REL + 'settings" Target="settings.xml"/><Relationship Id="rIdHK" Type="' + REL + 'header" Target="header1.xml"/>' +
      '<Relationship Id="rIdHE" Type="' + REL + 'header" Target="header2.xml"/><Relationship Id="rIdLogo" Type="' + REL + 'image" Target="media/logo_pu.png"/></Relationships>';
    var hdrRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rIdLogoH" Type="' + REL + 'image" Target="media/logo_pu.png"/></Relationships>';
    var ct = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' +
      '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Default Extension="png" ContentType="image/png"/>' +
      '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>' +
      '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>' +
      '<Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/>' +
      '<Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>' +
      '<Override PartName="/word/header1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"/>' +
      '<Override PartName="/word/header2.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"/>' +
      '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>' +
      '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>';
    var rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' +
      '<Relationship Id="rId1" Type="' + REL + 'officeDocument" Target="word/document.xml"/>' +
      '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>' +
      '<Relationship Id="rId3" Type="' + REL + 'extended-properties" Target="docProps/app.xml"/></Relationships>';
    var now = new Date().toISOString().replace(/\.\d+Z$/, 'Z');
    var title = d.mode === 'suket' ? 'Surat Permohonan Izin / Pemberitahuan' : d.hal;
    var core = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' +
      '<dc:title>' + esc(title) + '</dc:title><dc:creator>Generator Surat BWS Kalimantan I</dc:creator><dcterms:created xsi:type="dcterms:W3CDTF">' + now + '</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">' + now + '</dcterms:modified></cp:coreProperties>';
    var app = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>Generator Surat</Application></Properties>';

    var zip = new cfg.JSZip();
    zip.file('[Content_Types].xml', ct);
    zip.file('_rels/.rels', rootRels);
    zip.file('docProps/core.xml', core);
    zip.file('docProps/app.xml', app);
    zip.file('word/document.xml', doc);
    zip.file('word/styles.xml', styles);
    zip.file('word/numbering.xml', numXml);
    zip.file('word/settings.xml', settings);
    zip.file('word/header1.xml', hdrKop);
    zip.file('word/header2.xml', hdrEmpty);
    zip.file('word/_rels/document.xml.rels', docRels);
    zip.file('word/_rels/header1.xml.rels', hdrRels);
    zip.file('word/media/logo_pu.png', cfg.logo.b64, { base64: true });
    return zip.generateAsync({ type: cfg.outType || 'blob', compression: 'DEFLATE', mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
  }

  return { build: build, bodyOf: bodyOf, fmtPangkat: fmtPangkat, fmtTgl: fmtTgl, fmtHariTgl: fmtHariTgl, terbilang: terbilang,
    lines: lines, paras: paras, withComma: withComma, noComma: noComma, fitPt: fitPt, kopLines: kopLines,
    setArialW: function (w) { ARIALW = w; } };
})();
if (typeof module !== 'undefined') module.exports = DocGen;
