@php
  $data = $getState() ?? [];
  $record = $getRecord();
  $agama = $record->user->agama ?? '';
  $isMuslim = !in_array(strtolower($agama), ['kristen', 'katolik', 'hindu', 'budha', 'konghucu']);
@endphp

<style>
  .fsd-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    overflow: hidden;
  }
  .fsd-card-header {
    padding: 0.625rem 1rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
  }
  .fsd-card-header h3 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    letter-spacing: 0.025em;
    text-transform: uppercase;
  }
  .fsd-card-body { padding: 0.75rem 1rem; }
  .fsd-label { font-size: 0.75rem; color: #64748b; display: block; margin-bottom: 0.2rem; }
  .fsd-value { font-weight: 600; font-size: 0.875rem; color: #1e293b; }
  .fsd-label-sm { font-size: 0.7rem; color: #64748b; display: block; margin-bottom: 0.15rem; }
  .fsd-text-body { line-height: 1.6; color: #334155; font-size: 0.875rem; }
  .fsd-blockquote {
    padding: 0.75rem; background: #f8fafc; border-radius: 0.375rem;
    border: 1px solid #e2e8f0; line-height: 1.6; color: #334155; font-size: 0.875rem;
  }

  /* Dark mode */
  .dark .fsd-card { background: #1e293b; border-color: #334155; }
  .dark .fsd-card-header { background: #0f172a; border-bottom-color: #334155; }
  .dark .fsd-card-header h3 { color: #e2e8f0; }
  .dark .fsd-label { color: #94a3b8; }
  .dark .fsd-value { color: #f1f5f9; }
  .dark .fsd-label-sm { color: #94a3b8; }
  .dark .fsd-text-body { color: #cbd5e1; }
  .dark .fsd-blockquote { background: #0f172a; border-color: #334155; color: #cbd5e1; }

  /* Status cells */
  .fsd-cell-jamaah   { background: #dcfce7; }
  .fsd-cell-munfarid { background: #fef9c3; }
  .fsd-cell-tidak    { background: #fee2e2; }
  .fsd-cell-empty    { background: #f1f5f9; }
  .fsd-cell-ya       { background: #dcfce7; }
  .fsd-cell-online   { background: #dbeafe; }

  .fsd-text-jamaah   { color: #166534; font-weight: 600; }
  .fsd-text-munfarid { color: #854d0e; font-weight: 600; }
  .fsd-text-tidak    { color: #991b1b; font-weight: 600; }
  .fsd-text-empty    { color: #94a3b8; font-weight: 600; }
  .fsd-text-ya       { color: #166534; font-weight: 600; }
  .fsd-text-online   { color: #1e40af; font-weight: 600; }

  .dark .fsd-cell-jamaah   { background: #166534; }
  .dark .fsd-cell-munfarid { background: #854d0e; }
  .dark .fsd-cell-tidak    { background: #7f1d1d; }
  .dark .fsd-cell-empty    { background: #334155; }
  .dark .fsd-cell-ya       { background: #166534; }
  .dark .fsd-cell-online   { background: #1e3a5f; }

  .dark .fsd-text-jamaah   { color: #bbf7d0; }
  .dark .fsd-text-munfarid { color: #fef08a; }
  .dark .fsd-text-tidak    { color: #fecaca; }
  .dark .fsd-text-empty    { color: #64748b; }
  .dark .fsd-text-ya       { color: #bbf7d0; }
  .dark .fsd-text-online   { color: #93c5fd; }

  .dark .fsd-cell-jamaah .fsd-label-sm,
  .dark .fsd-cell-munfarid .fsd-label-sm,
  .dark .fsd-cell-tidak .fsd-label-sm,
  .dark .fsd-cell-ya .fsd-label-sm,
  .dark .fsd-cell-online .fsd-label-sm { color: rgba(255,255,255,0.6); }

  /* Checklist items */
  .fsd-check-done {
    background: #f0fdf4; padding: 0.4rem 0.6rem;
    display: flex; align-items: center; gap: 0.5rem; border-radius: 0.25rem;
  }
  .fsd-check-done .fsd-dot { background: #22c55e; }
  .fsd-check-done .fsd-check-text { font-size: 0.8rem; color: #15803d; font-weight: 500; }

  .fsd-check-no {
    background: #fef2f2; padding: 0.4rem 0.6rem;
    display: flex; align-items: center; gap: 0.5rem; border-radius: 0.25rem;
  }
  .fsd-check-no .fsd-dot { background: #ef4444; }
  .fsd-check-no .fsd-check-text { font-size: 0.8rem; color: #991b1b; }

  .fsd-check-neutral {
    padding: 0.4rem 0.6rem;
    display: flex; align-items: center; gap: 0.5rem; border-radius: 0.25rem;
  }
  .fsd-check-neutral .fsd-dot { background: #cbd5e1; }
  .fsd-check-neutral .fsd-check-text { font-size: 0.8rem; color: #94a3b8; }

  .fsd-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

  .dark .fsd-check-done { background: rgba(22,101,52,0.25); }
  .dark .fsd-check-done .fsd-dot { background: #4ade80; }
  .dark .fsd-check-done .fsd-check-text { color: #86efac; }

  .dark .fsd-check-no { background: rgba(127,29,29,0.25); }
  .dark .fsd-check-no .fsd-dot { background: #f87171; }
  .dark .fsd-check-no .fsd-check-text { color: #fca5a5; }

  .dark .fsd-check-neutral { background: transparent; }
  .dark .fsd-check-neutral .fsd-dot { background: #475569; }
  .dark .fsd-check-neutral .fsd-check-text { color: #64748b; }

  /* Pill badges */
  .fsd-pill { display: inline-block; padding: 0.3rem 0.75rem; border-radius: 9999px; font-weight: 600; font-size: 0.8rem; }
  .fsd-pill-sm { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; }
</style>

<div style="font-family: system-ui, -apple-system, sans-serif; display: flex; flex-direction: column; gap: 1.25rem;">
  @if($isMuslim)
    {{-- ═══════════════ MUSLIM FORM ═══════════════ --}}

    {{-- Puasa --}}
    <div class="fsd-card">
      <div class="fsd-card-header"><h3>Puasa</h3></div>
      <div class="fsd-card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
          <div>
            <span class="fsd-label">Puasa Hari Ini</span>
            @php $puasa = $data['puasa'] ?? ''; @endphp
            <span class="fsd-text-{{ $puasa === 'ya' ? 'ya' : ($puasa === 'tidak' ? 'tidak' : 'empty') }}" style="font-size: 0.875rem;">
              {{ $puasa === 'ya' ? 'Ya' : ($puasa === 'tidak' ? 'Tidak' : 'Belum diisi') }}
            </span>
          </div>
          @if(($data['puasa'] ?? '') === 'tidak' && !empty($data['puasa_alasan']))
            <div>
              <span class="fsd-label">Alasan</span>
              <span class="fsd-text-tidak" style="font-size: 0.875rem; font-weight: 500;">{{ $data['puasa_alasan'] }}</span>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Sholat Fardhu --}}
    <div class="fsd-card">
      <div class="fsd-card-header"><h3>Sholat Fardhu</h3></div>
      <div class="fsd-card-body">
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.5rem;">
          @foreach(['subuh' => 'Subuh', 'dzuhur' => 'Dzuhur', 'ashar' => 'Ashar', 'maghrib' => 'Maghrib', 'isya' => 'Isya'] as $key => $label)
            @php
              $val = $data['sholat'][$key] ?? '';
              $cls = match($val) { 'jamaah' => 'jamaah', 'munfarid' => 'munfarid', 'tidak' => 'tidak', default => 'empty' };
              $text = match($val) { 'jamaah' => 'Jamaah', 'munfarid' => 'Munfarid', 'tidak' => 'Tidak', default => '—' };
            @endphp
            <div class="fsd-cell-{{ $cls }}" style="padding: 0.5rem; border-radius: 0.375rem; text-align: center;">
              <span class="fsd-label-sm">{{ $label }}</span>
              <span class="fsd-text-{{ $cls }}" style="font-size: 0.8rem;">{{ $text }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Tarawih & Sholat Sunat --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
      <div class="fsd-card">
        <div class="fsd-card-header"><h3>Tarawih</h3></div>
        <div class="fsd-card-body">
          @php
            $tarawih = $data['tarawih'] ?? '';
            $cls = match($tarawih) { 'jamaah' => 'jamaah', 'munfarid' => 'munfarid', 'tidak' => 'tidak', default => 'empty' };
            $text = match($tarawih) { 'jamaah' => 'Jamaah', 'munfarid' => 'Munfarid', 'tidak' => 'Tidak', default => 'Belum diisi' };
          @endphp
          <span class="fsd-pill fsd-cell-{{ $cls }} fsd-text-{{ $cls }}">{{ $text }}</span>
        </div>
      </div>
      <div class="fsd-card">
        <div class="fsd-card-header"><h3>Sholat Sunat</h3></div>
        <div class="fsd-card-body">
          <div style="display: flex; gap: 0.5rem;">
            @foreach(['rowatib' => 'Rowatib', 'tahajud' => 'Tahajud', 'dhuha' => 'Dhuha'] as $key => $label)
              @php
                $val = $data['sunat'][$key] ?? '';
                $cls = $val === 'ya' ? 'ya' : ($val === 'tidak' ? 'tidak' : 'empty');
                $text = $val === 'ya' ? 'Ya' : ($val === 'tidak' ? 'Tidak' : '—');
              @endphp
              <div class="fsd-cell-{{ $cls }}" style="flex: 1; padding: 0.5rem; border-radius: 0.375rem; text-align: center;">
                <span class="fsd-label-sm">{{ $label }}</span>
                <span class="fsd-text-{{ $cls }}" style="font-size: 0.8rem;">{{ $text }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- Tadarus Al-Quran --}}
    <div class="fsd-card">
      <div class="fsd-card-header"><h3>Tadarus Al-Quran</h3></div>
      <div class="fsd-card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
          <div>
            <span class="fsd-label">Surat</span>
            <span class="fsd-value">{{ $data['tadarus_surat'] ?? 'Belum diisi' }}</span>
          </div>
          <div>
            <span class="fsd-label">Ayat</span>
            <span class="fsd-value">{{ $data['tadarus_ayat'] ?? 'Belum diisi' }}</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Kegiatan Harian --}}
    <div class="fsd-card">
      <div class="fsd-card-header"><h3>Kegiatan Harian</h3></div>
      <div class="fsd-card-body">
        @php
          $kegiatanLabels = [
            'dzikir_pagi' => 'Dzikir Pagi',
            'olahraga' => 'Olahraga Ringan',
            'membantu_ortu' => 'Membantu Orang Tua',
            'membersihkan_kamar' => 'Membersihkan Kamar',
            'membersihkan_rumah' => 'Membersihkan Rumah',
            'membersihkan_halaman' => 'Membersihkan Halaman',
            'merawat_lingkungan' => 'Merawat Lingkungan',
            'dzikir_petang' => 'Dzikir Petang',
            'sedekah' => 'Sedekah / Poe Ibu',
            'buka_keluarga' => 'Buka Bersama Keluarga',
            'literasi' => 'Literasi',
            'kajian' => 'Kajian Al-Quran, Tafsir & Hadits',
            'menabung' => 'Menabung',
            'tidur_cepat' => 'Tidur Cepat',
            'bangun_pagi' => 'Bangun Pagi / Sahur',
          ];
          $kegiatan = $data['kegiatan'] ?? [];
        @endphp
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.3rem;">
          @foreach($kegiatanLabels as $key => $label)
            @php $done = !empty($kegiatan[$key]) && $kegiatan[$key] !== false && $kegiatan[$key] !== 'tidak'; @endphp
            <div class="{{ $done ? 'fsd-check-done' : 'fsd-check-neutral' }}">
              <span class="fsd-dot"></span>
              <span class="fsd-check-text">{{ $label }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Ringkasan Ceramah --}}
    <div class="fsd-card">
      <div class="fsd-card-header"><h3>Ringkasan Ceramah</h3></div>
      <div class="fsd-card-body" style="display: flex; flex-direction: column; gap: 0.6rem;">
        @php
          $cMode = $data['ceramah_mode'] ?? '';
          $cls = match($cMode) { 'offline' => 'jamaah', 'online' => 'online', 'tidak' => 'tidak', default => 'empty' };
          $modeText = match($cMode) { 'offline' => 'Offline (Masjid/Musholla)', 'online' => 'Online (TV/YouTube)', 'tidak' => 'Tidak mengikuti', default => 'Belum diisi' };
        @endphp
        <div>
          <span class="fsd-label">Mode Ceramah</span>
          <span class="fsd-pill-sm fsd-cell-{{ $cls }} fsd-text-{{ $cls }}">{{ $modeText }}</span>
        </div>
        @if(!empty($data['ceramah_tema']))
          <div>
            <span class="fsd-label">Tema</span>
            <span class="fsd-value" style="font-weight: 500;">{{ $data['ceramah_tema'] }}</span>
          </div>
        @endif
        @if(!empty($data['ringkasan_ceramah']))
          <div>
            <span class="fsd-label">Ringkasan</span>
            <div class="fsd-blockquote">{!! $data['ringkasan_ceramah'] !!}</div>
          </div>
        @endif
      </div>
    </div>

  @else
    {{-- ═══════════════ NON-MUSLIM FORM ═══════════════ --}}

    {{-- Pembiasaan Pengendalian Diri --}}
    <div class="fsd-card">
      <div class="fsd-card-header"><h3>Pembiasaan Pengendalian Diri</h3></div>
      <div class="fsd-card-body">
        @php
          $pengendalianLabels = [
            'pengendalian_diri' => 'Latihan pengendalian diri (mengurangi jajan / screen time)',
            'refleksi_doa' => 'Refleksi / doa sesuai keyakinan',
            'baca_inspiratif' => 'Membaca buku inspiratif / nilai moral',
          ];
          $pengendalian = $data['pengendalian'] ?? [];
        @endphp
        <div style="display: flex; flex-direction: column; gap: 0.3rem;">
          @foreach($pengendalianLabels as $key => $label)
            @php
              $val = $pengendalian[$key] ?? '';
              $done = $val === 'ya';
              $checkCls = $done ? 'fsd-check-done' : ($val === 'tidak' ? 'fsd-check-no' : 'fsd-check-neutral');
            @endphp
            <div class="{{ $checkCls }}">
              <span class="fsd-dot"></span>
              <span class="fsd-check-text">{{ $label }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Kegiatan Harian (Pembiasaan Positif) --}}
    <div class="fsd-card">
      <div class="fsd-card-header"><h3>Kegiatan Harian (Pembiasaan Positif)</h3></div>
      <div class="fsd-card-body">
        @php
          $kegiatanNmLabels = [
            'refleksi_pagi' => 'Refleksi pagi (menulis rasa syukur)',
            'olahraga' => 'Olahraga ringan',
            'membantu_ortu' => 'Membantu orang tua',
            'membersihkan_kamar' => 'Membersihkan kamar',
            'membersihkan_rumah' => 'Membersihkan rumah / halaman',
            'merawat_lingkungan' => 'Merawat lingkungan / tanaman',
            'refleksi_sore' => 'Refleksi sore',
            'sedekah' => 'Aksi berbagi / sedekah',
            'makan_keluarga' => 'Makan bersama keluarga tanpa gadget',
            'literasi' => 'Literasi (membaca buku pengembangan diri / biografi)',
            'menulis_ringkasan' => 'Menulis ringkasan / refleksi bacaan',
            'menabung' => 'Menabung',
            'tidur_lebih_awal' => 'Tidur lebih awal',
            'bangun_pagi' => 'Bangun pagi',
            'target_kebaikan' => 'Menetapkan target kebaikan harian',
          ];
          $kegiatanNm = $data['kegiatan'] ?? [];
        @endphp
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.3rem;">
          @foreach($kegiatanNmLabels as $key => $label)
            @php
              $val = $kegiatanNm[$key] ?? '';
              $done = $val === 'ya';
              $checkCls = $done ? 'fsd-check-done' : ($val === 'tidak' ? 'fsd-check-no' : 'fsd-check-neutral');
            @endphp
            <div class="{{ $checkCls }}">
              <span class="fsd-dot"></span>
              <span class="fsd-check-text">{{ $label }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Catatan Harian --}}
    @if(!empty($data['catatan']))
      <div class="fsd-card">
        <div class="fsd-card-header"><h3>Catatan Harian</h3></div>
        <div class="fsd-card-body">
          <div class="fsd-text-body">{!! $data['catatan'] !!}</div>
        </div>
      </div>
    @endif

  @endif
</div>
