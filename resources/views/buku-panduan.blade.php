<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }}</title>
  <style>
    :root {
      --bg: #f1f5f9;
      --ink: #0f172a;
      --muted: #475569;
      --card: #ffffff;
      --line: #e2e8f0;
      --primary: #1e3a8a;
      --primary-soft: #dbeafe;
      --primary-2: #2563eb;
      --chip: #eff6ff;
      --shadow: 0 18px 45px rgba(15, 23, 42, 0.1);
    }

    body {
      margin: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: var(--bg);
      color: var(--ink);
    }

    .container {
      max-width: 980px;
      margin: 30px auto;
      padding: 0 18px;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
      gap: 12px;
      flex-wrap: wrap;
    }

    .card {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 18px;
      overflow: hidden;
      box-shadow: var(--shadow);
    }

    .hero {
      background: #1e3a8a;
      padding: 24px 22px;
      color: #e2e8f0;
    }

    .hero h1 {
      margin: 0;
      font-size: 30px;
      letter-spacing: -0.3px;
      color: #ffffff;
    }

    .hero p {
      margin: 8px 0 0;
      color: rgba(226, 232, 240, 0.9);
      font-size: 14px;
    }

    .meta {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 14px;
    }

    .chip {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      padding: 5px 10px;
      font-size: 12px;
      font-weight: 700;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.22);
      color: #ffffff;
    }

    .body {
      padding: 22px;
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
      color: var(--primary-2);
      font-weight: 700;
      font-size: 14px;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 10px;
      padding: 7px 12px;
      transition: 0.2s ease;
    }

    .back-link:hover {
      border-color: #93c5fd;
      background: #f8fafc;
    }

    .empty {
      background: var(--chip);
      border: 1px solid #bfdbfe;
      border-radius: 12px;
      padding: 14px 16px;
      color: var(--primary);
      font-size: 14px;
    }

    .guide-content {
      line-height: 1.75;
      color: #1e293b;
    }

    .guide-content h2,
    .guide-content h3 {
      margin-top: 1.2em;
      margin-bottom: 0.55em;
      letter-spacing: -0.2px;
    }

    .guide-content h2 {
      color: #0f172a;
      font-size: 24px;
      border-left: 4px solid #2563eb;
      padding-left: 10px;
    }

    .guide-content h3 {
      color: #1e3a8a;
      font-size: 18px;
    }

    .guide-content p {
      margin: 0.6em 0;
      color: #334155;
    }

    .guide-content ul,
    .guide-content ol {
      margin: 0.6em 0 0.6em 1.35em;
      color: #1f2937;
    }

    .guide-content li {
      margin: 0.28em 0;
    }

    .support-card {
      margin-top: 18px;
      border: 1px solid #bfdbfe;
      background: #eff6ff;
      border-radius: 12px;
      padding: 14px;
    }

    .support-title {
      margin: 0 0 6px;
      color: #1e3a8a;
      font-size: 15px;
      font-weight: 700;
    }

    .support-desc {
      margin: 0 0 10px;
      color: #1e40af;
      font-size: 13px;
      line-height: 1.5;
    }

    .support-actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .support-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      background: #1e3a8a;
      color: #ffffff;
      border-radius: 9px;
      padding: 7px 10px;
      font-size: 12px;
      font-weight: 700;
      border: 1px solid #1e3a8a;
      transition: 0.2s ease;
    }

    .support-btn:hover {
      background: #1d4ed8;
      border-color: #1d4ed8;
    }

    .flow-card {
      margin: 0 0 18px;
      border: 1px solid #bfdbfe;
      border-radius: 12px;
      background: #f8fbff;
      padding: 14px;
    }

    .flow-title {
      margin: 0 0 8px;
      color: #1e3a8a;
      font-size: 15px;
      font-weight: 800;
    }

    .flow-desc {
      margin: 0 0 10px;
      color: #334155;
      font-size: 13px;
    }

    .flow-steps {
      display: flex;
      align-items: stretch;
      gap: 10px;
      flex-wrap: wrap;
    }

    .flow-step {
      flex: 1 1 160px;
      background: #ffffff;
      border: 1px solid #dbeafe;
      border-radius: 10px;
      padding: 10px 10px 11px;
      min-height: 108px;
    }

    .flow-head {
      display: flex;
      align-items: center;
      gap: 0;
      margin-bottom: 6px;
    }

    .flow-index {
      color: #1e40af;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.2px;
      text-transform: uppercase;
    }

    .flow-step-title {
      color: #1e293b;
      font-size: 13px;
      font-weight: 800;
      line-height: 1.35;
      margin-bottom: 4px;
    }

    .flow-step-desc {
      color: #475569;
      font-size: 11.5px;
      line-height: 1.45;
    }

    .flow-arrow {
      align-self: center;
      color: #1d4ed8;
      font-weight: 900;
      font-size: 14px;
      line-height: 1;
      width: 24px;
      height: 24px;
      border-radius: 999px;
      border: 1px solid #bfdbfe;
      background: #eff6ff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 640px) {
      .container {
        margin: 20px auto;
      }

      .hero h1 {
        font-size: 24px;
      }

      .body,
      .hero {
        padding: 16px;
      }

      .flow-arrow {
        transform: rotate(90deg);
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <a class="back-link" href="{{ $loginUrl }}">&larr; Kembali ke halaman login</a>
    </div>

    <div class="card">
      <div class="hero">
        <h1>{{ $title }}</h1>
        <p>Panduan operasional role ini disusun agar pengguna baru bisa mengikuti alur dari awal sampai akhir dengan jelas.</p>
        <div class="meta">
          <span class="chip">Role: {{ strtoupper($role) }}</span>
          <span class="chip">Terakhir diperbarui: {{ $updatedAtLabel }}</span>
        </div>
      </div>

      <div class="body">
        @if (!empty($flowSteps ?? []))
          <div class="flow-card">
            <h3 class="flow-title">Grafik Alur Singkat</h3>
            <p class="flow-desc">Ikuti urutan di bawah ini agar penggunaan sistem lebih cepat dipahami.</p>
            <div class="flow-steps">
              @foreach ($flowSteps as $idx => $step)
                <div class="flow-step">
                  <div class="flow-head">
                    <span class="flow-index">Langkah {{ $idx + 1 }}</span>
                  </div>
                  <div class="flow-step-title">{{ $step['title'] }}</div>
                  <div class="flow-step-desc">{{ $step['desc'] }}</div>
                </div>
                @if (!$loop->last)
                  <div class="flow-arrow">&rarr;</div>
                @endif
              @endforeach
            </div>
          </div>
        @endif

        @if (blank($guideContent))
          <div class="empty">
            Buku panduan untuk role ini belum diisi oleh superadmin.
          </div>
        @else
          <div class="guide-content">{!! $guideContent !!}</div>
        @endif

        @if (!empty($adminContacts ?? []))
          @php
            $roleLabel = strtoupper($role);
            $supportTemplatePlain = "Assalamu'alaikum admin, saya pengguna role {$roleLabel} butuh bantuan pada aplikasi Calakan.\n\nNama:\nID/NISN/NIP:\nKelas/Jabatan:\nKendala:\nWaktu kejadian:";
            $supportTemplate = rawurlencode($supportTemplatePlain);
          @endphp
          <div class="support-card">
            <h3 class="support-title">Butuh Bantuan Admin?</h3>
            <p class="support-desc">Klik salah satu tombol di bawah untuk langsung kirim pesan template ke admin via WhatsApp.</p>
            <div class="support-actions">
              @foreach (($adminContacts ?? []) as $contact)
                @php
                  $rawPhone = (string) ($contact['phone'] ?? '');
                  $phoneDigits = preg_replace('/\D+/', '', $rawPhone);
                  if (str_starts_with($phoneDigits, '0')) {
                    $phoneDigits = '62' . substr($phoneDigits, 1);
                  }
                  $contactName = (string) ($contact['name'] ?? 'Admin');
                @endphp
                @if (!blank($phoneDigits))
                  <a class="support-btn" target="_blank" rel="noopener noreferrer" href="https://wa.me/{{ $phoneDigits }}?text={{ $supportTemplate }}">
                    Kontak {{ $contactName }} ({{ $rawPhone }})
                  </a>
                @endif
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</body>
</html>