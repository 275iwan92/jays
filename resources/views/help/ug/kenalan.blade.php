{{-- REMARK
 Tujuan       : Panduan Pengguna bagian 0 "Kenali AdminKU": halaman pembuka yang sekaligus media promosi (ilustrasi dan kartu kemampuan), jalur belajar BERLEVEL (Pemula, Operator, Supervisor, Administrator), dan alur proses antar-modul
                (Stok, Pembelian, HR, GA, Penerima menjadi Karyawan) sebagai diagram. Tautan ke bagian modul hanya tampil sebagai tautan bila pengguna boleh membukanya (teks biasa bila tidak).
 Sumber hulu  : $can dan $user dari help/user-guide; diagram di resources/help/diagrams (ug-hero, ug-alur-*).
 Dipakai oleh : help/user-guide.blade.php (sebelum bagian 1).
 Status       : Aktif.
--}}
@php
    $lk = fn (?string $code, string $anchor, string $text) => ($code === null || $can($code)) ? '<a href="#'.$anchor.'">'.e($text).'</a>' : e($text);
@endphp
<style>
    .sstock-guide .kn-hero { margin: 8px 0 18px; }
    .sstock-guide .kn-tag { font-size: 17px; font-weight: 700; margin: 14px 0 4px; }
    .sstock-guide .kn-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px; margin: 12px 0 6px; }
    .sstock-guide .kn-card { border: 1px solid var(--dl-border); border-top: 5px solid var(--c); border-radius: 12px; padding: 12px 14px; background: var(--dl-surface); }
    .sstock-guide .kn-card h4 { margin: 0 0 4px; font-size: 15px; }
    .sstock-guide .kn-card p { margin: 0 0 6px; font-size: 13px; }
    .sstock-guide .kn-card ul { margin: 0 0 6px; padding-left: 18px; font-size: 12.5px; list-style: disc; }
    .sstock-guide .kn-level ol { list-style: decimal; }
    .sstock-guide .kn-lv { display: inline-block; padding: 0 8px; border-radius: 999px; font-size: 11px; font-weight: 700; color: #fff; background: var(--c); }
    .sstock-guide .kn-levels { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; margin: 12px 0; }
    .sstock-guide .kn-level { border: 1px solid var(--dl-border); border-radius: 12px; padding: 12px 14px; background: var(--dl-surface); }
    .sstock-guide .kn-level .kn-num { display: inline-flex; width: 28px; height: 28px; border-radius: 50%; align-items: center; justify-content: center; color: #fff; font-weight: 800; background: var(--c); margin-right: 6px; }
    .sstock-guide .kn-level h4 { margin: 0 0 2px; font-size: 15px; }
    .sstock-guide .kn-level .kn-time { font-size: 12px; opacity: .7; margin: 0 0 6px; }
    .sstock-guide .kn-level ol { margin: 0; padding-left: 18px; font-size: 13px; display: flex; flex-direction: column; gap: 3px; }
    .sstock-guide .kn-tip { border-left: 4px solid #14b8a6; background: color-mix(in srgb, #14b8a6 10%, var(--dl-surface)); padding: 8px 12px; border-radius: 0 8px 8px 0; font-size: 13px; margin: 10px 0; }
</style>

<h2 id="kenalan">0. Kenali AdminKU</h2>
<p class="g-lead">AdminKU menyatukan pekerjaan rutin kantor dan gudang dalam satu aplikasi: stok barang, pembelian, perawatan kendaraan dan AC, serta kepegawaian. Semua dikerjakan lewat layar yang sama polanya, jadi yang Anda pelajari di satu menu langsung berguna di menu lain.</p>
<div class="kn-hero">@include('help.diagram', ['name' => 'ug-hero', 'caption' => 'AdminKU: satu aplikasi, enam bidang kerja.'])</div>
<p class="kn-tag">Kerja jadi rapi: catat sekali, angka dan peringatan muncul sendiri.</p>
<ul class="g-list">
    <li><b>Stok selalu benar.</b> Sisa stok dihitung dari dokumen yang sudah dikonfirmasi, tidak bisa minus, dan setiap perubahan punya jejak.</li>
    <li><b>Peringatan datang sendiri.</b> Barang menipis, kedaluwarsa dekat, SIM dan kontrak yang hampir habis, servis kendaraan dan jadwal AC tampil di Beranda tanpa Anda mencari.</li>
    <li><b>Persetujuan jelas.</b> Permintaan Pembelian, Pesanan Pembelian, dan Penyesuaian Stok disetujui Supervisor; pembuat dokumen tidak menyetujui dokumennya sendiri.</li>
    <li><b>Hak akses per Grup.</b> Setiap orang hanya melihat menu yang perlu baginya, termasuk panduan ini.</li>
</ul>

<h3 id="kn-fitur">Apa saja yang bisa dikerjakan</h3>
<div class="kn-grid">
    <div class="kn-card" style="--c:#14b8a6"><h4>Stok dan Gudang</h4><p>Dari barang masuk sampai laporan.</p>
        <ul><li>Barang berbatch dan tanggal kedaluwarsa, urutan keluar FEFO</li><li>Titik pesan ulang per barang</li><li>Stok Opname terjadwal dan Penyesuaian</li><li>Kartu Stok dan Ringkasan Stok</li></ul>
        <span class="kn-lv">Pemula</span> {!! $lk('RCV', 't-rcv', 'Penerimaan Barang') !!}</div>
    <div class="kn-card" style="--c:#6366f1"><h4>Pembelian</h4><p>Dari permintaan sampai barang masuk stok.</p>
        <ul><li>Permintaan Pembelian dengan persetujuan</li><li>Keputusan Pesanan: pemasok dipilih dari riwayat harga</li><li>Pesanan (PO) tercetak dan Penerimaan (PV) menambah stok otomatis</li></ul>
        <span class="kn-lv">Operator</span> {!! $lk('PUP', 'pur-pup', 'Permintaan Pembelian') !!}</div>
    <div class="kn-card" style="--c:#f59e0b"><h4>Perawatan Kendaraan</h4><p>Tidak ada lagi servis atau pajak yang terlewat.</p>
        <ul><li>Status Aman, Segera, Terlambat</li><li>Odometer, servis, dan perpanjangan dokumen</li><li>Rencana perawatan per jenis kendaraan</li></ul>
        <span class="kn-lv">Operator</span> {!! $lk('GVM', 'ga-gvm', 'Monitoring Kendaraan') !!}</div>
    <div class="kn-card" style="--c:#38bdf8"><h4>Perawatan AC</h4><p>Jadwal cuci dan servis besar yang rapi.</p>
        <ul><li>Area, ruang, dan posisi unit di denah</li><li>Kunjungan vendor dengan konfirmasi</li><li>Foto sebelum dan sesudah, temuan, dan biaya</li></ul>
        <span class="kn-lv">Operator</span> {!! $lk('GAM', 'ga-gam', 'Perawatan AC') !!}</div>
    <div class="kn-card" style="--c:#ec4899"><h4>HR: Karyawan, Rekrutmen, Kedisiplinan</h4><p>Kepegawaian tanpa urusan gaji.</p>
        <ul><li>Kartu karyawan dengan foto dan peringatan kontrak</li><li>Papan kanban rekrutmen, jadwal tes dan interview</li><li>SP 1, SP 2, SP 3, PHK berwarna dengan surat cetak bernomor otomatis</li></ul>
        <span class="kn-lv">Supervisor</span> {!! $lk('HRE', 'hr-hre', 'Karyawan') !!}</div>
    <div class="kn-card" style="--c:#a78bfa"><h4>Penerima yang juga Karyawan</h4><p>Satu orang, satu data.</p>
        <ul><li>Tombol di form Penerima membuka form Karyawan yang sudah terisi</li><li>Kartu Penerima berbeda bentuk bagi yang sudah karyawan</li></ul>
        <span class="kn-lv">Operator</span> {!! $lk('RCP', 'm-rcp', 'Penerima') !!}</div>
    <div class="kn-card" style="--c:#0ea5e9"><h4>Laporan</h4><p>Jawaban cepat untuk pertanyaan atasan.</p>
        <ul><li>Penerimaan, Pengeluaran, Kedaluwarsa, Opname</li><li>Ekspor dan cetak</li></ul>
        <span class="kn-lv">Pemula</span> {!! $anyRpt ? '<a href="#laporan">Laporan</a>' : 'Laporan' !!}</div>
    <div class="kn-card" style="--c:#64748b"><h4>Keamanan dan Pengaturan</h4><p>Siapa boleh apa, diatur sendiri.</p>
        <ul><li>Pengguna, Grup, dan hak per modul (RO atau RW)</li><li>Daftar Modul dan Data Contoh</li></ul>
        <span class="kn-lv">Administrator</span> {!! $lk('GRP', 'sec-grp', 'Grup dan hak akses') !!}</div>
</div>

<h3 id="kn-level">Pilih jalur belajar sesuai level Anda</h3>
<p>Tidak perlu membaca semuanya. Ambil jalur yang cocok dengan pekerjaan Anda, lalu naik level saat sudah nyaman.</p>
<div class="kn-levels">
    <div class="kn-level" style="--c:#14b8a6"><h4><span class="kn-num">1</span>Pemula</h4><p class="kn-time">sekitar 10 menit</p>
        <ol><li>{!! $lk(null, 'masuk', 'Masuk, Beranda, dan Menu') !!}</li><li>{!! $lk(null, 'grid', 'Cara memakai Grid') !!}</li><li>{!! $lk(null, 'form', 'Cara memakai Form') !!}</li><li>{!! $lk(null, 'siklus', 'Siklus dokumen: Draf, Confirm') !!}</li></ol></div>
    <div class="kn-level" style="--c:#6366f1"><h4><span class="kn-num">2</span>Operator harian</h4><p class="kn-time">untuk pekerjaan rutin</p>
        <ol><li>{!! $lk('RCV', 't-rcv', 'Penerimaan Barang') !!}</li><li>{!! $lk('ISS', 't-iss', 'Pengeluaran Barang') !!}</li><li>{!! $lk('PUP', 'pur-pup', 'Permintaan Pembelian') !!}</li><li>{!! $lk('GVO', 'ga-gvo', 'Catat Odometer') !!} dan {!! $lk('GVS', 'ga-gvs', 'Servis Kendaraan') !!}</li><li>{!! $lk('R-CARD', 'r-card', 'Kartu Stok') !!}</li></ol></div>
    <div class="kn-level" style="--c:#f59e0b"><h4><span class="kn-num">3</span>Supervisor</h4><p class="kn-time">menyetujui dan mengawasi</p>
        <ol><li>Menyetujui PP, PO, dan Penyesuaian</li><li>{!! $lk('SO', 't-so', 'Stok Opname') !!}</li><li>{!! $lk('HRR', 'hr-hrr', 'Rekrutmen') !!} dan {!! $lk('HRD', 'hr-hrd', 'Kedisiplinan') !!}</li><li>{!! $lk('PUK', 'pur-puk', 'Keputusan Pesanan') !!}</li></ol></div>
    <div class="kn-level" style="--c:#64748b"><h4><span class="kn-num">4</span>Administrator</h4><p class="kn-time">menyiapkan dan mengatur</p>
        <ol><li>{!! $lk('USR', 'sec-usr', 'Pengguna') !!} dan {!! $lk('GRP', 'sec-grp', 'Grup dan hak akses') !!}</li><li>{!! $lk('GAP', 'ga-gap', 'Pengaturan GA') !!}</li><li>{!! $lk('CMP', 'm-cmp', 'Data Perusahaan') !!}</li><li>{!! $lk('SMP', 'sec-smp', 'Data Contoh') !!}</li></ol></div>
</div>
<p class="kn-tip">Butuh jawaban cepat? Buka {!! $lk(null, 'tanya', 'Pertanyaan umum') !!} atau cari istilah di {!! $lk(null, 'istilah', 'Istilah') !!}. Setiap bagian modul memakai pola yang sama: Untuk apa, Siapa yang bisa, Langkah, Yang perlu diperhatikan, Masalah umum.</p>

<h3 id="kn-alur">Alur proses antar-modul</h3>
<p>Gambar di bawah menunjukkan jalannya pekerjaan dari awal sampai akhir, supaya Anda tahu menu mana yang dipakai setelah menu mana.</p>
<p class="g-note">Alur dasar Stok (Data Master, Transaksi, stok berubah, Laporan) ada di bagian {!! $lk(null, 'mulai', '1. Mulai di sini') !!}.</p>
@if ($anyPur)
    @include('help.diagram', ['name' => 'ug-alur-pembelian', 'caption' => 'Pembelian: PP, persetujuan, Keputusan Pesanan, PO, lalu PV menambah stok.'])
@endif
@if ($anyHr)
    @include('help.diagram', ['name' => 'ug-alur-hr', 'caption' => 'HR: kandidat diterima menjadi karyawan; kedisiplinan berjenjang SP 1 sampai PHK (warna sama dengan di layar).'])
@endif
@if ($anyGa)
    @include('help.diagram', ['name' => 'ug-alur-ga', 'caption' => 'GA: Kendaraan dan AC, dari master sampai status dan jadwal.'])
@endif
@if ($can('RCP'))
    @include('help.diagram', ['name' => 'ug-alur-penerima', 'caption' => 'Penerima yang juga karyawan: satu tombol membuat data Karyawan, kartunya berubah bentuk.'])
@endif
