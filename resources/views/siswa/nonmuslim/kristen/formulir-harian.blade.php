<x-filament-panels::page>
    <div x-data="formulirNonMuslim()" x-init="init()" class="formulir-page">
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

        {{-- Top bar --}}
        <div class="formulir-topbar">
            <a href="{{ \App\Filament\Siswa\Pages\NonMuslim\Kristen\Dashboard::getUrl() }}" class="formulir-back-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="formulir-topbar-center">
                <h1 class="formulir-topbar-title">Formulir Harian Kegiatan Positif</h1>
                <p class="formulir-topbar-sub">Catatan kegiatan &amp; pembiasaan harian</p>
            </div>
            <span class="formulir-topbar-badge" x-text="'Hari ke-' + formDay"></span>
        </div>

        {{-- Form body --}}
        <div class="formulir-body">
            <div class="formulir-content">

                {{-- ── Backfill warning (shown when filling a past day) ── --}}
                <div x-show="formDay < currentDay" x-cloak
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

                {{-- Sunday church reminder --}}
                <div x-show="isSunday" x-cloak class="f-church-reminder">
                    <div class="f-church-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 0v4m0-4h4m-4 0H8m4 6v8m-5 0h10a1 1 0 001-1v-3a5 5 0 00-5-5h-2a5 5 0 00-5 5v3a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="f-church-body">
                        <p class="f-church-title">🙏 Pengingat Hari Minggu</p>
                        <p class="f-church-sub">Jangan lupa untuk mengikuti ibadah di gereja hari ini!</p>
                    </div>
                </div>

                {{-- Status badge --}}
                <div x-show="formSubmitted && currentDayStatus !== 'rejected'" class="f-status-banner">
                    <div class="f-status-icon">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="f-status-text">
                        <p class="f-status-title" x-text="currentDayStatus === 'verified' ? 'Formulir sudah diverifikasi guru' : 'Formulir hari ini sudah dikirim'"></p>
                        <p class="f-status-sub" x-text="currentDayStatus === 'verified' ? 'Formulir ini telah disetujui oleh guru' : 'Kamu bisa mengedit kembali jika perlu'"></p>
                    </div>
                    <button x-show="currentDayStatus !== 'verified'" @click="editForm()" class="f-status-edit-btn">Edit</button>
                </div>

                {{-- Rejection banner --}}
                <div x-show="currentDayStatus === 'rejected'" x-cloak style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px;margin-bottom:16px;display:flex;align-items:flex-start;gap:12px;">
                    <div style="flex-shrink:0;width:40px;height:40px;background:#ef4444;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div style="flex:1;">
                        <p style="margin:0 0 4px;font-weight:700;color:#991b1b;font-size:15px;">Formulir Ditolak oleh Guru</p>
                        <p style="margin:0 0 8px;color:#b91c1c;font-size:13px;">Silakan perbaiki dan kirim ulang formulir ini.</p>
                        <div x-show="currentDayNote" style="background:#fff;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;">
                            <p style="margin:0 0 2px;font-size:11px;font-weight:600;color:#991b1b;text-transform:uppercase;letter-spacing:.5px;">Catatan Guru:</p>
                            <p style="margin:0;color:#dc2626;font-size:13px;" x-text="currentDayNote"></p>
                        </div>
                    </div>
                </div>

                <fieldset :disabled="formSubmitted" class="f-fieldset">

                {{-- ═══ 1. PEMBIASAAN PENGENDALIAN DIRI ═══ --}}
                <div class="f-section" x-show="isSectionEnabled('pengendalian_diri')" x-transition>
                    <h4 class="f-section-title">
                        <span class="f-section-num">1</span> <span x-text="sectionTitles.pengendalian_diri">Pembiasaan Pengendalian Diri</span>
                    </h4>
                    <div class="f-kegiatan-list-yatidak">
                        <template x-for="(item, idx) in pengendalianDiri" :key="item.key">
                            <div class="f-yatidak-row">
                                <span class="f-yatidak-num" x-text="idx + 1"></span>
                                <span class="f-yatidak-label" x-text="item.label"></span>
                                <div class="f-yatidak-options">
                                    <button type="button" class="f-chip"
                                            :class="formData.pengendalian[item.key] === 'ya' && 'f-chip-active f-chip-green'"
                                            @click="formData.pengendalian[item.key] = formData.pengendalian[item.key] === 'ya' ? '' : 'ya'">
                                        Ya
                                    </button>
                                    <button type="button" class="f-chip"
                                            :class="formData.pengendalian[item.key] === 'tidak' && 'f-chip-active f-chip-gray'"
                                            @click="formData.pengendalian[item.key] = formData.pengendalian[item.key] === 'tidak' ? '' : 'tidak'">
                                        Tidak
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ═══ 2. KEGIATAN HARIAN (PEMBIASAAN POSITIF) ═══ --}}
                <div class="f-section" x-show="isSectionEnabled('kegiatan')" x-transition>
                    <h4 class="f-section-title">
                        <span class="f-section-num">2</span> <span x-text="sectionTitles.kegiatan">Kegiatan Harian (Pembiasaan Positif)</span>
                    </h4>

                    {{-- Group A --}}
                    <p class="f-group-label" x-text="groupTitles[0]">A. Karakter "Sehat, Baik, Benar"</p>
                    <div class="f-kegiatan-list-yatidak">
                        <template x-for="(item, idx) in kegiatanGroupA" :key="item.key">
                            <div class="f-yatidak-row">
                                <span class="f-yatidak-num" x-text="idx + 1"></span>
                                <span class="f-yatidak-label" x-text="item.label"></span>
                                <div class="f-yatidak-options">
                                    <button type="button" class="f-chip"
                                            :class="formData.kegiatan[item.key] === 'ya' && 'f-chip-active f-chip-green'"
                                            @click="formData.kegiatan[item.key] = formData.kegiatan[item.key] === 'ya' ? '' : 'ya'">
                                        Ya
                                    </button>
                                    <button type="button" class="f-chip"
                                            :class="formData.kegiatan[item.key] === 'tidak' && 'f-chip-active f-chip-gray'"
                                            @click="formData.kegiatan[item.key] = formData.kegiatan[item.key] === 'tidak' ? '' : 'tidak'">
                                        Tidak
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Group B --}}
                    <p class="f-group-label" x-text="groupTitles[1]">B. Pengembangan Diri "Pinter"</p>
                    <div class="f-kegiatan-list-yatidak">
                        <template x-for="(item, idx) in kegiatanGroupB" :key="item.key">
                            <div class="f-yatidak-row">
                                <span class="f-yatidak-num" x-text="idx + 10"></span>
                                <span class="f-yatidak-label" x-text="item.label"></span>
                                <div class="f-yatidak-options">
                                    <button type="button" class="f-chip"
                                            :class="formData.kegiatan[item.key] === 'ya' && 'f-chip-active f-chip-green'"
                                            @click="formData.kegiatan[item.key] = formData.kegiatan[item.key] === 'ya' ? '' : 'ya'">
                                        Ya
                                    </button>
                                    <button type="button" class="f-chip"
                                            :class="formData.kegiatan[item.key] === 'tidak' && 'f-chip-active f-chip-gray'"
                                            @click="formData.kegiatan[item.key] = formData.kegiatan[item.key] === 'tidak' ? '' : 'tidak'">
                                        Tidak
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Group C --}}
                    <p class="f-group-label" x-text="groupTitles[2]">C. Kemandirian "Mandiri & Disiplin"</p>
                    <div class="f-kegiatan-list-yatidak">
                        <template x-for="(item, idx) in kegiatanGroupC" :key="item.key">
                            <div class="f-yatidak-row">
                                <span class="f-yatidak-num" x-text="idx + 12"></span>
                                <span class="f-yatidak-label" x-text="item.label"></span>
                                <div class="f-yatidak-options">
                                    <button type="button" class="f-chip"
                                            :class="formData.kegiatan[item.key] === 'ya' && 'f-chip-active f-chip-green'"
                                            @click="formData.kegiatan[item.key] = formData.kegiatan[item.key] === 'ya' ? '' : 'ya'">
                                        Ya
                                    </button>
                                    <button type="button" class="f-chip"
                                            :class="formData.kegiatan[item.key] === 'tidak' && 'f-chip-active f-chip-gray'"
                                            @click="formData.kegiatan[item.key] = formData.kegiatan[item.key] === 'tidak' ? '' : 'tidak'">
                                        Tidak
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ═══ 3. CATATAN HARIAN ═══ --}}
                <div class="f-section f-section-last" x-show="isSectionEnabled('catatan')" x-transition>
                    <h4 class="f-section-title">
                        <span class="f-section-num">3</span> <span x-text="sectionTitles.catatan">Catatan Harian</span>
                    </h4>
                    <div class="f-field">
                        <label class="f-label">Catatan / refleksi hari ini (opsional)</label>
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
                                 x-ref="catatanEditor"
                                 @input="formData.catatan = $refs.catatanEditor.innerHTML"
                                 @blur="formData.catatan = $refs.catatanEditor.innerHTML"
                                 @mouseup="updateEditorFormats()"
                                 @keyup="updateEditorFormats()"
                                 data-placeholder="Tulis catatan atau refleksi hari ini..."></div>
                        </div>
                    </div>
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

                {{-- Validation Error Toast --}}
                <div x-show="showValidationError" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:9999;background:#ef4444;color:#fff;padding:14px 28px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.25);display:flex;align-items:center;gap:10px;font-size:14px;font-weight:500;max-width:90vw;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    <span x-text="validationMessage"></span>
                </div>

                {{-- Success Popup Toast --}}
                <div x-show="showSuccessPopup" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:9999;background:#fff;border-radius:20px;padding:36px 40px;box-shadow:0 12px 40px rgba(0,0,0,.2);text-align:center;min-width:300px;">
                    <div style="width:64px;height:64px;margin:0 auto 16px;background:#10b981;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <svg width="36" height="36" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                    <h3 style="margin:0 0 6px;font-size:20px;font-weight:700;color:#1e293b;">Berhasil Terkirim!</h3>
                    <p style="margin:0;color:#64748b;font-size:14px;">Formulir harian Anda sudah berhasil disimpan.</p>
                </div>
                <div x-show="showSuccessPopup" style="position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:9998;"></div>

            </div>
        </div>

    </div>
</x-filament-panels::page>

@push('styles')
    <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/formulir.css') }}?v={{ time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('themes/ramadhan/js/nonmuslim/kristen/formulir.js') }}?v={{ time() }}"></script>
@endpush
