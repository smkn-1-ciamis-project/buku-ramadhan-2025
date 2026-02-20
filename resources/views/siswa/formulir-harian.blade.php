<x-filament-panels::page>
    <style>
        html.fi .fi-main { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        html.fi .fi-main-ctn { padding: 0 !important; margin: 0 !important; }
        html.fi .fi-page { padding: 0 !important; margin: 0 !important; }
        html.fi .fi-page > section,
        html.fi section.py-8,
        html.fi section.gap-y-8 { padding: 0 !important; margin: 0 !important; gap: 0 !important; }
        html.fi .fi-page > section > div,
        html.fi .fi-page > section > div > div { padding: 0 !important; margin: 0 !important; gap: 0 !important; }
        .fi-topbar, .fi-page-header, .fi-sidebar, .fi-sidebar-close-overlay { display: none !important; height: 0 !important; overflow: hidden !important; }
        *, *::before, *::after { box-sizing: border-box; }
        .fi-body { margin: 0 !important; padding: 0 !important; background: #f1f5f9 !important; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>

    <div x-data="formulirHarian()" x-init="init()" class="formulir-page">

        {{-- Top bar --}}
        <div class="formulir-topbar">
            <a href="{{ \App\Filament\Siswa\Pages\Dashboard::getUrl() }}" class="formulir-back-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="formulir-topbar-center">
                <h1 class="formulir-topbar-title">Formulir Harian Ramadhan</h1>
                <p class="formulir-topbar-sub">Catatan ibadah &amp; kegiatan harian</p>
            </div>
            <span class="formulir-topbar-badge" x-text="'Hari ke-' + formDay"></span>
        </div>

        {{-- Form body --}}
        <div class="formulir-body">
            <div class="formulir-content">

                {{-- ── Backfill warning (shown when filling a past day) ── --}}
                <div x-show="formDay < ramadhanDay" x-cloak
                     class="f-backfill-banner">
                    <div class="f-backfill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div class="f-backfill-body">
                        <p class="f-backfill-title">Isi hari yang tertunggak dulu</p>
                        <p class="f-backfill-sub" x-text="'Kamu sedang mengisi Hari ke-' + formDay + '. Setelah ini, kamu akan diarahkan ke hari berikutnya yang belum diisi.'"></p>
                    </div>
                    <div class="f-backfill-badge" x-text="getMissedCount() + ' hari lagi'"></div>
                </div>

                {{-- Status badge --}}
                <div x-show="formSubmitted" class="f-status-banner">
                    <div class="f-status-icon">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="f-status-text">
                        <p class="f-status-title">Formulir hari ini sudah dikirim</p>
                        <p class="f-status-sub">Kamu bisa mengedit kembali jika perlu</p>
                    </div>
                    <button @click="editForm()" class="f-status-edit-btn">Edit</button>
                </div>

                <fieldset :disabled="formSubmitted" class="f-fieldset">

                {{-- ═══ 1. PUASA ═══ --}}
                <div class="f-section">
                    <h4 class="f-section-title">
                        <span class="f-section-num">1</span> Puasa
                    </h4>
                    <div class="f-radio-row">
                        <label class="f-radio-card" :class="formData.puasa === 'ya' && 'f-radio-card-active f-radio-card-green'">
                            <input type="radio" x-model="formData.puasa" value="ya" class="f-hidden">
                            <div class="f-radio-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="f-radio-card-label">Ya, Puasa</span>
                        </label>
                        <label class="f-radio-card" :class="formData.puasa === 'tidak' && 'f-radio-card-active f-radio-card-red'">
                            <input type="radio" x-model="formData.puasa" value="tidak" class="f-hidden">
                            <div class="f-radio-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="f-radio-card-label">Tidak</span>
                        </label>
                    </div>
                    {{-- Reason input when Tidak --}}
                    <div x-show="formData.puasa === 'tidak'" x-transition class="f-reason-wrap">
                        <label class="f-label">Alasan tidak puasa</label>
                        <div class="f-input-suggest-wrap">
                            <input type="text" x-model="formData.puasa_alasan"
                                   @focus="showPuasaSuggest = true"
                                   @blur="setTimeout(() => showPuasaSuggest = false, 150)"
                                   placeholder="Ketik atau pilih alasan..."
                                   class="f-input">
                            <div x-show="showPuasaSuggest" class="f-suggest-list">
                                <template x-for="s in puasaSuggestions" :key="s">
                                    <button type="button" class="f-suggest-item" @mousedown.prevent="formData.puasa_alasan = s; showPuasaSuggest = false" x-text="s"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══ 2. SHOLAT FARDU ═══ --}}
                <div class="f-section" x-show="!(formData.puasa === 'tidak' && formData.puasa_alasan === 'Haid')" x-transition>
                    <h4 class="f-section-title">
                        <span class="f-section-num">2</span> Sholat Fardu
                    </h4>
                    <div class="f-sholat-list">
                        <template x-for="waktu in ['subuh','dzuhur','ashar','maghrib','isya']" :key="waktu">
                            <div class="f-sholat-row">
                                <span class="f-sholat-name" x-text="waktu.charAt(0).toUpperCase() + waktu.slice(1)"></span>
                                <div class="f-sholat-options">
                                    <button type="button" class="f-chip"
                                            :class="formData.sholat[waktu] === 'jamaah' && 'f-chip-active f-chip-blue'"
                                            @click="formData.sholat[waktu] = formData.sholat[waktu] === 'jamaah' ? '' : 'jamaah'">
                                        Jamaah
                                    </button>
                                    <button type="button" class="f-chip"
                                            :class="formData.sholat[waktu] === 'munfarid' && 'f-chip-active f-chip-teal'"
                                            @click="formData.sholat[waktu] = formData.sholat[waktu] === 'munfarid' ? '' : 'munfarid'">
                                        Munfarid
                                    </button>
                                    <button type="button" class="f-chip"
                                            :class="formData.sholat[waktu] === 'tidak' && 'f-chip-active f-chip-gray'"
                                            @click="formData.sholat[waktu] = formData.sholat[waktu] === 'tidak' ? '' : 'tidak'">
                                        Tidak
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ═══ 3. SHOLAT TARAWIH ═══ --}}
                <div class="f-section" x-show="!(formData.puasa === 'tidak' && formData.puasa_alasan === 'Haid')" x-transition>
                    <h4 class="f-section-title">
                        <span class="f-section-num">3</span> Sholat Tarawih
                    </h4>
                    <div class="f-sholat-list">
                        <div class="f-sholat-row">
                            <span class="f-sholat-name">Tarawih</span>
                            <div class="f-sholat-options">
                                <button type="button" class="f-chip" :class="formData.tarawih === 'jamaah' && 'f-chip-active f-chip-blue'"
                                        @click="formData.tarawih = formData.tarawih === 'jamaah' ? '' : 'jamaah'">
                                    Jamaah
                                </button>
                                <button type="button" class="f-chip" :class="formData.tarawih === 'munfarid' && 'f-chip-active f-chip-teal'"
                                        @click="formData.tarawih = formData.tarawih === 'munfarid' ? '' : 'munfarid'">
                                    Munfarid
                                </button>
                                <button type="button" class="f-chip" :class="formData.tarawih === 'tidak' && 'f-chip-active f-chip-gray'"
                                        @click="formData.tarawih = formData.tarawih === 'tidak' ? '' : 'tidak'">
                                    Tidak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══ 4. SHOLAT SUNAT ═══ --}}
                <div class="f-section" x-show="!(formData.puasa === 'tidak' && formData.puasa_alasan === 'Haid')" x-transition>
                    <h4 class="f-section-title">
                        <span class="f-section-num">4</span> Sholat Sunat
                    </h4>
                    <div class="f-sholat-list">
                        <template x-for="sn in [{key:'rowatib',label:'Rowatib'},{key:'tahajud',label:'Tahajud'},{key:'dhuha',label:'Dhuha'}]" :key="sn.key">
                            <div class="f-sholat-row">
                                <span class="f-sholat-name" x-text="sn.label"></span>
                                <div class="f-sholat-options">
                                    <button type="button" class="f-chip" :class="formData.sunat[sn.key] === 'ya' && 'f-chip-active f-chip-green'"
                                            @click="formData.sunat[sn.key] = formData.sunat[sn.key] === 'ya' ? '' : 'ya'">
                                        Ya
                                    </button>
                                    <button type="button" class="f-chip" :class="formData.sunat[sn.key] === 'tidak' && 'f-chip-active f-chip-gray'"
                                            @click="formData.sunat[sn.key] = formData.sunat[sn.key] === 'tidak' ? '' : 'tidak'">
                                        Tidak
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ═══ 5. TADARUS AL-QURAN ═══ --}}
                <div class="f-section" x-show="!(formData.puasa === 'tidak' && formData.puasa_alasan === 'Haid')" x-transition>
                    <h4 class="f-section-title">
                        <span class="f-section-num">5</span> Tadarus Al-Quran
                    </h4>
                    <div class="f-tadarus-grid">
                        <div class="f-field">
                            <label class="f-label">Surat</label>
                            <div class="f-input-suggest-wrap">
                                <input type="text" x-model="formData.tadarus_surat"
                                       @input="filterSurah($event.target.value)"
                                       @focus="showSurahList = true"
                                       @blur="setTimeout(() => showSurahList = false, 200)"
                                       placeholder="Cari surat..."
                                       class="f-input">
                                <div x-show="showSurahList && filteredSurahs.length > 0" class="f-suggest-list f-suggest-list-tall">
                                    <template x-for="s in filteredSurahs" :key="s.number">
                                        <button type="button" class="f-suggest-item f-suggest-surah"
                                                @mousedown.prevent="selectSurah(s)">
                                            <span class="f-suggest-surah-num" x-text="s.number"></span>
                                            <span class="f-suggest-surah-name" x-text="s.name"></span>
                                            <span class="f-suggest-surah-ayat" x-text="s.ayat + ' ayat'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="f-field">
                            <label class="f-label">Ayat <span x-show="selectedSurahAyat > 0" class="f-label-hint" x-text="'(maks. ' + selectedSurahAyat + ')'"></span></label>
                            <input type="text" x-model="formData.tadarus_ayat"
                                   @input="validateAyat($event.target.value)"
                                   placeholder="cth: 1-7 atau 15"
                                   :class="ayatError ? 'f-input f-input-error' : 'f-input'">
                            <p x-show="ayatError" x-text="ayatError" class="f-error-hint"></p>
                        </div>
                    </div>
                    <p x-show="selectedSurahAyat > 0" class="f-hint" x-text="'Surat ' + formData.tadarus_surat + ' memiliki ' + selectedSurahAyat + ' ayat'"></p>
                </div>

                {{-- ═══ 6. KEGIATAN HARIAN ═══ --}}
                <div class="f-section">
                    <h4 class="f-section-title">
                        <span class="f-section-num">6</span> Kegiatan Harian
                    </h4>

                    {{-- Group A: Amaliyah Cageur, Bageur dan Bener --}}
                    <p class="f-group-label">Amaliyah Cageur, Bageur dan Bener</p>
                    <div class="f-kegiatan-grid">
                        <template x-for="kg in kegiatanGroupA" :key="kg.key">
                            <label class="f-kegiatan-item" :class="formData.kegiatan[kg.key] && 'f-kegiatan-active'">
                                <input type="checkbox" x-model="formData.kegiatan[kg.key]" class="f-hidden">
                                <div class="f-kegiatan-check">
                                    <svg x-show="formData.kegiatan[kg.key]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </div>
                                <span class="f-kegiatan-label" x-text="kg.label"></span>
                            </label>
                        </template>
                    </div>

                    {{-- Group B: Amaliyah Pancawaluya Pinter --}}
                    <p class="f-group-label">Amaliyah Pancawaluya Pinter</p>
                    <div class="f-kegiatan-grid">
                        <template x-for="kg in kegiatanGroupB" :key="kg.key">
                            <label class="f-kegiatan-item" :class="formData.kegiatan[kg.key] && 'f-kegiatan-active'">
                                <input type="checkbox" x-model="formData.kegiatan[kg.key]" class="f-hidden">
                                <div class="f-kegiatan-check">
                                    <svg x-show="formData.kegiatan[kg.key]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </div>
                                <span class="f-kegiatan-label" x-text="kg.label"></span>
                            </label>
                        </template>
                    </div>

                    {{-- Group C: Amaliyah Pancawaluya Singer --}}
                    <p class="f-group-label">Amaliyah Pancawaluya Singer</p>
                    <div class="f-kegiatan-grid">
                        <template x-for="kg in kegiatanGroupC" :key="kg.key">
                            <label class="f-kegiatan-item" :class="formData.kegiatan[kg.key] && 'f-kegiatan-active'">
                                <input type="checkbox" x-model="formData.kegiatan[kg.key]" class="f-hidden">
                                <div class="f-kegiatan-check">
                                    <svg x-show="formData.kegiatan[kg.key]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </div>
                                <span class="f-kegiatan-label" x-text="kg.label"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- ═══ 7. RINGKASAN CERAMAH ═══ --}}
                <div class="f-section f-section-last">
                    <h4 class="f-section-title">
                        <span class="f-section-num">7</span> Ringkasan Ceramah
                    </h4>
                    {{-- Online / Offline / Tidak ada --}}
                    <div class="f-radio-row" style="margin-bottom:0.875rem;">
                        <label class="f-radio-card" :class="formData.ceramah_mode === 'offline' && 'f-radio-card-active f-radio-card-green'">
                            <input type="radio" x-model="formData.ceramah_mode" value="offline" class="f-hidden">
                            <div class="f-radio-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                            </div>
                            <span class="f-radio-card-label">Offline</span>
                        </label>
                        <label class="f-radio-card" :class="formData.ceramah_mode === 'online' && 'f-radio-card-active f-radio-card-blue'">
                            <input type="radio" x-model="formData.ceramah_mode" value="online" class="f-hidden">
                            <div class="f-radio-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/></svg>
                            </div>
                            <span class="f-radio-card-label">Online</span>
                        </label>
                        <label class="f-radio-card" :class="formData.ceramah_mode === 'tidak' && 'f-radio-card-active f-radio-card-gray'">
                            <input type="radio" x-model="formData.ceramah_mode" value="tidak" class="f-hidden">
                            <div class="f-radio-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </div>
                            <span class="f-radio-card-label">Tidak Ada</span>
                        </label>
                    </div>
                    {{-- Tema & ringkasan only when ada ceramah --}}
                    <div x-show="formData.ceramah_mode === 'offline' || formData.ceramah_mode === 'online'" x-transition>
                    <div class="f-field">
                        <label class="f-label">Tema</label>
                        <input type="text" x-model="formData.ceramah_tema" placeholder="Tema ceramah hari ini..." class="f-input">
                    </div>
                    <div class="f-field" style="margin-top:10px;">
                        <label class="f-label">Ringkasan</label>
                        {{-- Rich text toolbar --}}
                        <div class="f-editor-wrap">
                            <div class="f-editor-toolbar">
                                <button type="button" class="f-editor-btn" :class="editorFormats.bold && 'f-editor-btn-active'" @click="execCmd('bold')" title="Bold"><b>B</b></button>
                                <button type="button" class="f-editor-btn" :class="editorFormats.italic && 'f-editor-btn-active'" @click="execCmd('italic')" title="Italic"><i>I</i></button>
                                <button type="button" class="f-editor-btn" :class="editorFormats.underline && 'f-editor-btn-active'" @click="execCmd('underline')" title="Underline"><u>U</u></button>
                                <span class="f-editor-sep"></span>
                                <button type="button" class="f-editor-btn" :class="editorFormats.ul && 'f-editor-btn-active'" @click="execCmd('insertUnorderedList')" title="Bullet List">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm0 5.25h.007v.008H3.75V12zm0 5.25h.007v.008H3.75v-.008z"/></svg>
                                </button>
                                <button type="button" class="f-editor-btn" :class="editorFormats.ol && 'f-editor-btn-active'" @click="execCmd('insertOrderedList')" title="Numbered List">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5"/></svg>
                                </button>
                            </div>
                            <div class="f-editor-content"
                                 contenteditable="true"
                                 x-ref="ceramahEditor"
                                 @input="formData.ringkasan_ceramah = $refs.ceramahEditor.innerHTML"
                                 @blur="formData.ringkasan_ceramah = $refs.ceramahEditor.innerHTML"
                                 @mouseup="updateEditorFormats()"
                                 @keyup="updateEditorFormats()"
                                 data-placeholder="Tulis ringkasan ceramah hari ini..."></div>
                        </div>
                    </div>
                    </div>{{-- end x-show ceramah --}}
                </div>

                </fieldset>

                {{-- Submit button --}}
                <div class="f-submit-wrap">
                    <button @click="submitForm()" :disabled="formSubmitted || formSaving"
                            class="f-submit-btn" :class="formSubmitted ? 'f-submit-btn-disabled' : ''">
                        <template x-if="formSaving">
                            <svg class="f-spin" fill="none" viewBox="0 0 24 24"><circle class="f-spin-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="f-spin-path" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <svg x-show="!formSaving && !formSubmitted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                        <svg x-show="formSubmitted && !formSaving" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="formSaving ? 'Menyimpan...' : (formSubmitted ? 'Sudah Dikirim' : 'Kirim Formulir')"></span>
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-filament-panels::page>

@push('styles')
    <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/formulir.css') }}?v={{ time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('themes/ramadhan/js/formulir.js') }}?v={{ time() }}"></script>
@endpush
