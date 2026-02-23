<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Kesiswaan — SMKN 1 Ciamis</title>
  <style>
    @page {
      margin: 20mm 15mm 15mm 15mm;
    }
    body {
      font-family: sans-serif;
      font-size: 11px;
      color: #1a1a1a;
      margin: 0;
      padding: 0;
    }

    /* ── Header / Kop Surat ─────────────────── */
    .header {
      text-align: center;
      border-bottom: 3px double #1e3a5f;
      padding-bottom: 10px;
      margin-bottom: 18px;
    }
    .header table {
      width: 100%;
    }
    .header .logo {
      width: 65px;
      height: auto;
    }
    .header .title {
      font-size: 16px;
      font-weight: bold;
      color: #1e3a5f;
      letter-spacing: 0.5px;
    }
    .header .subtitle {
      font-size: 11px;
      color: #444;
      margin-top: 2px;
    }
    .header .address {
      font-size: 9px;
      color: #666;
      margin-top: 2px;
    }

    /* ── Info Bar ────────────────────────────── */
    .info-bar {
      margin-bottom: 14px;
      font-size: 11px;
    }
    .info-bar table {
      width: 100%;
    }
    .info-bar td {
      padding: 2px 0;
    }
    .info-bar .label {
      font-weight: bold;
      width: 120px;
      color: #333;
    }

    /* ── Judul Dokumen ──────────────────────── */
    .doc-title {
      text-align: center;
      font-size: 14px;
      font-weight: bold;
      color: #1e3a5f;
      margin-bottom: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* ── Tabel Data ─────────────────────────── */
    table.data {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
    }
    table.data thead th {
      background-color: #1e3a5f;
      color: #fff;
      font-weight: bold;
      font-size: 10px;
      text-transform: uppercase;
      padding: 7px 6px;
      text-align: left;
      border: 1px solid #1e3a5f;
    }
    table.data tbody td {
      padding: 6px;
      border: 1px solid #ccc;
      font-size: 10.5px;
      vertical-align: top;
    }
    table.data tbody tr:nth-child(even) {
      background-color: #f7f9fc;
    }
    table.data tbody tr:hover {
      background-color: #eef2f7;
    }
    .text-center {
      text-align: center;
    }

    /* ── Badge ──────────────────────────────── */
    .badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 10px;
      font-size: 9px;
      font-weight: bold;
      color: #fff;
    }
    .badge-l {
      background-color: #3b82f6;
    }
    .badge-p {
      background-color: #ef4444;
    }

    /* ── Summary ────────────────────────────── */
    .summary {
      margin-top: 4px;
      margin-bottom: 20px;
    }
    .summary table {
      border-collapse: collapse;
    }
    .summary td {
      padding: 4px 12px 4px 0;
      font-size: 10px;
    }
    .summary .num {
      font-weight: bold;
      font-size: 12px;
      color: #1e3a5f;
    }

    /* ── Footer ─────────────────────────────── */
    .footer {
      margin-top: 30px;
    }
    .footer table {
      width: 100%;
    }
    .footer .sign-block {
      text-align: center;
      width: 45%;
    }
    .footer .sign-line {
      margin-top: 60px;
      border-bottom: 1px solid #333;
      display: inline-block;
      width: 180px;
    }
    .footer .sign-name {
      font-weight: bold;
      font-size: 11px;
      margin-top: 4px;
    }
    .footer .sign-nip {
      font-size: 9px;
      color: #666;
    }

    .print-date {
      font-size: 9px;
      color: #999;
      text-align: right;
      margin-top: 10px;
    }
  </style>
</head>
<body>

  {{-- ═══ KOP SURAT ═══ --}}
  <div class="header">
    <table>
      <tr>
        <td style="width: 75px; text-align: center; vertical-align: middle;">
          @if(file_exists(public_path('img/logo_smk.png')))
            <img src="{{ public_path('img/logo_smk.png') }}" class="logo" alt="Logo">
          @endif
        </td>
        <td style="vertical-align: middle;">
          <div class="title">SMK NEGERI 1 CIAMIS</div>
          <div class="subtitle">Buku Ramadhan Digital — Calakan</div>
          <div class="address">Jl. Jend. Sudirman No. 269, Ciamis, Jawa Barat 46211 | Telp. (0265) 771204</div>
        </td>
        <td style="width: 75px;"></td>
      </tr>
    </table>
  </div>

  {{-- ═══ JUDUL ═══ --}}
  <div class="doc-title">Data Petugas Kesiswaan</div>

  {{-- ═══ INFO BAR ═══ --}}
  <div class="info-bar">
    <table>
      <tr>
        <td class="label">Tahun Ajaran</td>
        <td>: {{ $tahunAjaran }}</td>
        <td class="label" style="text-align: right; width: 130px;">Tanggal Cetak</td>
        <td style="width: 150px;">: {{ $tanggalCetak }}</td>
      </tr>
      <tr>
        <td class="label">Total Petugas</td>
        <td>: {{ $totalKesiswaan }} orang</td>
        <td></td>
        <td></td>
      </tr>
    </table>
  </div>

  {{-- ═══ TABEL DATA KESISWAAN ═══ --}}
  <table class="data">
    <thead>
      <tr>
        <th class="text-center" style="width: 30px;">No</th>
        <th>Nama Lengkap</th>
        <th class="text-center" style="width: 35px;">JK</th>
        <th style="width: 190px;">Email</th>
        <th class="text-center" style="width: 100px;">No. HP</th>
        <th class="text-center" style="width: 100px;">Terdaftar</th>
      </tr>
    </thead>
    <tbody>
      @forelse($kesiswaan as $i => $k)
        <tr>
          <td class="text-center">{{ $i + 1 }}</td>
          <td>{{ $k->name }}</td>
          <td class="text-center">
            @if($k->jenis_kelamin)
              <span class="badge {{ $k->jenis_kelamin === 'L' ? 'badge-l' : 'badge-p' }}">
                {{ $k->jenis_kelamin === 'L' ? 'L' : 'P' }}
              </span>
            @else
              -
            @endif
          </td>
          <td>{{ $k->email ?? '-' }}</td>
          <td class="text-center">{{ $k->no_hp ?? '-' }}</td>
          <td class="text-center">{{ $k->created_at?->translatedFormat('d M Y') ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center" style="padding: 20px; color: #999;">Belum ada data kesiswaan</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- ═══ SUMMARY ═══ --}}
  <div class="summary">
    <table>
      <tr>
        <td>Total Petugas:</td>
        <td class="num">{{ $totalKesiswaan }}</td>
        <td style="padding-left: 20px;">Laki-laki:</td>
        <td class="num">{{ $totalL }}</td>
        <td style="padding-left: 20px;">Perempuan:</td>
        <td class="num">{{ $totalP }}</td>
      </tr>
    </table>
  </div>

  {{-- ═══ TTD ═══ --}}
  <div class="footer">
    <table>
      <tr>
        <td></td>
        <td class="sign-block">
          <div>Ciamis, {{ $tanggalCetak }}</div>
          <div style="margin-top: 4px;">Kepala Sekolah</div>
          <div class="sign-line"></div>
          <div class="sign-name">...............................</div>
          <div class="sign-nip">NIP. ................................</div>
        </td>
      </tr>
    </table>
  </div>

  <div class="print-date">
    Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB
  </div>

</body>
</html>
