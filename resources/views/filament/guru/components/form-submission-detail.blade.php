@php
  $data = $getState() ?? [];
  $record = $getRecord();
  $agama = $record->user->agama ?? '';
  $isMuslim = !in_array(strtolower($agama), ['kristen', 'katolik', 'hindu', 'budha', 'konghucu']);
@endphp

<div style="font-family: system-ui, -apple-system, sans-serif;">
  @if($isMuslim)
    {{-- MUSLIM FORM --}}

    {{-- Puasa --}}
    <div style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
        🌙 Puasa
      </h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
        <div style="padding: 0.5rem 0.75rem; background: #f8fafc; border-radius: 0.375rem;">
          <span style="font-size: 0.75rem; color: #64748b;">Puasa Hari Ini</span><br>
          @php $puasa = $data['puasa'] ?? ''; @endphp
          <span style="font-weight: 600; color: {{ $puasa === 'ya' ? '#16a34a' : ($puasa === 'tidak' ? '#dc2626' : '#94a3b8') }};">
            {{ $puasa === 'ya' ? '✅ Ya' : ($puasa === 'tidak' ? '❌ Tidak' : '— Belum diisi') }}
          </span>
        </div>
        @if(($data['puasa'] ?? '') === 'tidak' && !empty($data['puasa_alasan']))
          <div style="padding: 0.5rem 0.75rem; background: #fef2f2; border-radius: 0.375rem;">
            <span style="font-size: 0.75rem; color: #64748b;">Alasan</span><br>
            <span style="font-weight: 500; color: #991b1b;">{{ $data['puasa_alasan'] }}</span>
          </div>
        @endif
      </div>
    </div>

    {{-- Sholat Fardhu --}}
    <div style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
        🕌 Sholat Fardhu
      </h3>
      <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.5rem;">
        @foreach(['subuh' => 'Subuh', 'dzuhur' => 'Dzuhur', 'ashar' => 'Ashar', 'maghrib' => 'Maghrib', 'isya' => 'Isya'] as $key => $label)
          @php
            $val = $data['sholat'][$key] ?? '';
            $bg = match($val) { 'jamaah' => '#dcfce7', 'munfarid' => '#fef9c3', 'tidak' => '#fee2e2', default => '#f1f5f9' };
            $color = match($val) { 'jamaah' => '#166534', 'munfarid' => '#854d0e', 'tidak' => '#991b1b', default => '#94a3b8' };
            $text = match($val) { 'jamaah' => 'Jamaah', 'munfarid' => 'Munfarid', 'tidak' => 'Tidak', default => '—' };
          @endphp
          <div style="padding: 0.5rem; background: {{ $bg }}; border-radius: 0.375rem; text-align: center;">
            <span style="font-size: 0.7rem; color: #64748b; display: block;">{{ $label }}</span>
            <span style="font-weight: 600; font-size: 0.8rem; color: {{ $color }};">{{ $text }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Tarawih --}}
    <div style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
        🌃 Tarawih
      </h3>
      @php
        $tarawih = $data['tarawih'] ?? '';
        $bg = match($tarawih) { 'jamaah' => '#dcfce7', 'munfarid' => '#fef9c3', 'tidak' => '#fee2e2', default => '#f1f5f9' };
        $color = match($tarawih) { 'jamaah' => '#166534', 'munfarid' => '#854d0e', 'tidak' => '#991b1b', default => '#94a3b8' };
        $text = match($tarawih) { 'jamaah' => 'Jamaah', 'munfarid' => 'Munfarid', 'tidak' => 'Tidak', default => '— Belum diisi' };
      @endphp
      <div style="display: inline-block; padding: 0.5rem 1rem; background: {{ $bg }}; border-radius: 0.375rem;">
        <span style="font-weight: 600; color: {{ $color }};">{{ $text }}</span>
      </div>
    </div>

    {{-- Sholat Sunat --}}
    <div style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
        🤲 Sholat Sunat
      </h3>
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
        @foreach(['rowatib' => 'Rowatib', 'tahajud' => 'Tahajud', 'dhuha' => 'Dhuha'] as $key => $label)
          @php
            $val = $data['sunat'][$key] ?? '';
            $bg = $val === 'ya' ? '#dcfce7' : ($val === 'tidak' ? '#fee2e2' : '#f1f5f9');
            $color = $val === 'ya' ? '#166534' : ($val === 'tidak' ? '#991b1b' : '#94a3b8');
            $text = $val === 'ya' ? '✅ Ya' : ($val === 'tidak' ? '❌ Tidak' : '—');
          @endphp
          <div style="padding: 0.5rem; background: {{ $bg }}; border-radius: 0.375rem; text-align: center;">
            <span style="font-size: 0.7rem; color: #64748b; display: block;">{{ $label }}</span>
            <span style="font-weight: 600; font-size: 0.8rem; color: {{ $color }};">{{ $text }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Tadarus --}}
    <div style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
        📖 Tadarus Al-Quran
      </h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
        <div style="padding: 0.5rem 0.75rem; background: #f8fafc; border-radius: 0.375rem;">
          <span style="font-size: 0.75rem; color: #64748b;">Surat</span><br>
          <span style="font-weight: 600; color: #1e3a5f;">{{ $data['tadarus_surat'] ?? '— Belum diisi' }}</span>
        </div>
        <div style="padding: 0.5rem 0.75rem; background: #f8fafc; border-radius: 0.375rem;">
          <span style="font-size: 0.75rem; color: #64748b;">Ayat</span><br>
          <span style="font-weight: 600; color: #1e3a5f;">{{ $data['tadarus_ayat'] ?? '— Belum diisi' }}</span>
        </div>
      </div>
    </div>

    {{-- Kegiatan Harian --}}
    <div style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
        📋 Kegiatan Harian
      </h3>
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
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.35rem;">
        @foreach($kegiatanLabels as $key => $label)
          @php $done = !empty($kegiatan[$key]) && $kegiatan[$key] !== false && $kegiatan[$key] !== 'tidak'; @endphp
          <div style="padding: 0.4rem 0.6rem; background: {{ $done ? '#dcfce7' : '#f1f5f9' }}; border-radius: 0.25rem; display: flex; align-items: center; gap: 0.4rem;">
            <span style="font-size: 0.85rem;">{{ $done ? '✅' : '⬜' }}</span>
            <span style="font-size: 0.8rem; color: {{ $done ? '#166534' : '#64748b' }};">{{ $label }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Ceramah --}}
    <div style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
        🎤 Ringkasan Ceramah
      </h3>
      @php
        $cMode = $data['ceramah_mode'] ?? '';
        $modeText = match($cMode) { 'offline' => 'Offline (Masjid/Musholla)', 'online' => 'Online (TV/YouTube)', 'tidak' => 'Tidak mengikuti', default => '— Belum diisi' };
        $modeBg = match($cMode) { 'offline' => '#dcfce7', 'online' => '#dbeafe', 'tidak' => '#fee2e2', default => '#f1f5f9' };
        $modeColor = match($cMode) { 'offline' => '#166534', 'online' => '#1e40af', 'tidak' => '#991b1b', default => '#94a3b8' };
      @endphp
      <div style="margin-bottom: 0.75rem;">
        <span style="font-size: 0.75rem; color: #64748b; display: block; margin-bottom: 0.25rem;">Mode Ceramah</span>
        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $modeBg }}; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; color: {{ $modeColor }};">{{ $modeText }}</span>
      </div>
      @if(!empty($data['ceramah_tema']))
        <div style="margin-bottom: 0.75rem;">
          <span style="font-size: 0.75rem; color: #64748b; display: block; margin-bottom: 0.25rem;">Tema</span>
          <span style="font-weight: 500; color: #1e3a5f;">{{ $data['ceramah_tema'] }}</span>
        </div>
      @endif
      @if(!empty($data['ringkasan_ceramah']))
        <div>
          <span style="font-size: 0.75rem; color: #64748b; display: block; margin-bottom: 0.25rem;">Ringkasan</span>
          <div style="padding: 0.75rem; background: #f8fafc; border-radius: 0.375rem; border: 1px solid #e2e8f0; line-height: 1.6; color: #334155;">
            {!! $data['ringkasan_ceramah'] !!}
          </div>
        </div>
      @endif
    </div>

  @else
    {{-- NON-MUSLIM FORM --}}

    {{-- Pengendalian Diri --}}
    <div style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
        🧘 Pembiasaan Pengendalian Diri
      </h3>
      @php
        $pengendalianLabels = [
          'pengendalian_diri' => 'Latihan pengendalian diri (mengurangi jajan / screen time)',
          'refleksi_doa' => 'Refleksi / doa sesuai keyakinan',
          'baca_inspiratif' => 'Membaca buku inspiratif / nilai moral',
        ];
        $pengendalian = $data['pengendalian'] ?? [];
      @endphp
      <div style="display: grid; gap: 0.35rem;">
        @foreach($pengendalianLabels as $key => $label)
          @php
            $val = $pengendalian[$key] ?? '';
            $done = $val === 'ya';
          @endphp
          <div style="padding: 0.5rem 0.75rem; background: {{ $done ? '#dcfce7' : ($val === 'tidak' ? '#fee2e2' : '#f1f5f9') }}; border-radius: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 0.85rem;">{{ $done ? '✅' : ($val === 'tidak' ? '❌' : '⬜') }}</span>
            <span style="font-size: 0.8rem; color: {{ $done ? '#166534' : ($val === 'tidak' ? '#991b1b' : '#64748b') }};">{{ $label }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Kegiatan Harian NonMuslim --}}
    <div style="margin-bottom: 1.5rem;">
      <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
        📋 Kegiatan Harian (Pembiasaan Positif)
      </h3>
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
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.35rem;">
        @foreach($kegiatanNmLabels as $key => $label)
          @php
            $val = $kegiatanNm[$key] ?? '';
            $done = $val === 'ya';
          @endphp
          <div style="padding: 0.4rem 0.6rem; background: {{ $done ? '#dcfce7' : ($val === 'tidak' ? '#fee2e2' : '#f1f5f9') }}; border-radius: 0.25rem; display: flex; align-items: center; gap: 0.4rem;">
            <span style="font-size: 0.85rem;">{{ $done ? '✅' : ($val === 'tidak' ? '❌' : '⬜') }}</span>
            <span style="font-size: 0.8rem; color: {{ $done ? '#166534' : ($val === 'tidak' ? '#991b1b' : '#64748b') }};">{{ $label }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Catatan --}}
    @if(!empty($data['catatan']))
      <div style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0;">
          📝 Catatan Harian
        </h3>
        <div style="padding: 0.75rem; background: #f8fafc; border-radius: 0.375rem; border: 1px solid #e2e8f0; line-height: 1.6; color: #334155;">
          {!! $data['catatan'] !!}
        </div>
      </div>
    @endif

  @endif
</div>
