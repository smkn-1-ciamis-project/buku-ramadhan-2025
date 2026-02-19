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
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="flex-1 text-center">
                <h1 class="text-white font-bold text-sm lg:text-base">Formulir Harian Ramadhan</h1>
                <p class="text-blue-100 text-[11px]">Catatan ibadah & kegiatan harian</p>
            </div>
            <span class="bg-white/20 text-white text-[10px] font-bold px-3 py-1 rounded-md" x-text="'Hari ke-' + formDay"></span>
        </div>

        {{-- Form body --}}
        <div class="formulir-body">
            <div class="formulir-content">

                {{-- Status badge --}}
                <div x-show="formSubmitted" class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-emerald-700">Formulir hari ini sudah dikirim</p>
                        <p class="text-[11px] text-emerald-600">Kamu bisa mengedit kembali jika perlu</p>
                    </div>
                    <button @click="editForm()" class="text-[11px] font-semibold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 px-3 py-1.5 rounded-lg transition-colors">Edit</button>
                </div>

                <fieldset :disabled="formSubmitted">

                {{-- 1. PUASA --}}
                <div class="form-section">
                    <h4 class="form-section-title">
                        <span class="form-section-num">1</span> Puasa
                    </h4>
                    <div class="flex gap-4 mt-2">
                        <label class="form-radio-card" :class="formData.puasa === 'ya' && 'form-radio-card-active'">
                            <input type="radio" x-model="formData.puasa" value="ya" class="hidden">
                            <span class="form-radio-dot"></span>
                            <span class="text-sm font-medium">Ya</span>
                        </label>
                        <label class="form-radio-card" :class="formData.puasa === 'tidak' && 'form-radio-card-active'">
                            <input type="radio" x-model="formData.puasa" value="tidak" class="hidden">
                            <span class="form-radio-dot"></span>
                            <span class="text-sm font-medium">Tidak</span>
                        </label>
                    </div>
                </div>

                {{-- 2. SHOLAT FARDU --}}
                <div class="form-section">
                    <h4 class="form-section-title">
                        <span class="form-section-num">2</span> Sholat Fardu
                    </h4>
                    <div class="overflow-x-auto mt-2">
                        <table class="form-table">
                            <thead>
                                <tr>
                                    <th class="text-left">Waktu</th>
                                    <th class="text-center w-16">J</th>
                                    <th class="text-center w-16">M</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="waktu in ['dzuhur','ashar','maghrib','isya','subuh']" :key="waktu">
                                    <tr>
                                        <td class="capitalize text-sm text-gray-700" x-text="waktu"></td>
                                        <td class="text-center">
                                            <label class="form-check-cell">
                                                <input type="checkbox" x-model="formData['sholat_'+waktu+'_j']" class="form-checkbox">
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="form-check-cell">
                                                <input type="checkbox" x-model="formData['sholat_'+waktu+'_m']" class="form-checkbox">
                                            </label>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">J = Jamaah &nbsp; M = Munfarid</p>
                </div>

                {{-- 3. TARAWIH --}}
                <div class="form-section">
                    <h4 class="form-section-title">
                        <span class="form-section-num">3</span> Sholat Tarawih
                    </h4>
                    <div class="overflow-x-auto mt-2">
                        <table class="form-table">
                            <thead>
                                <tr>
                                    <th class="text-left">Sholat</th>
                                    <th class="text-center w-16">J</th>
                                    <th class="text-center w-16">M</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-sm text-gray-700">Tarawih</td>
                                    <td class="text-center">
                                        <label class="form-check-cell"><input type="checkbox" x-model="formData.tarawih_j" class="form-checkbox"></label>
                                    </td>
                                    <td class="text-center">
                                        <label class="form-check-cell"><input type="checkbox" x-model="formData.tarawih_m" class="form-checkbox"></label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">J = Jamaah &nbsp; M = Munfarid</p>
                </div>

                {{-- 4. SHOLAT SUNAT --}}
                <div class="form-section">
                    <h4 class="form-section-title">
                        <span class="form-section-num">4</span> Sholat Sunat
                    </h4>
                    <div class="space-y-2 mt-2">
                        <template x-for="sn in [{key:'rowatib',label:'Rowatib'},{key:'tahajud',label:'Tahajud'},{key:'dhuha',label:'Dhuha'}]" :key="sn.key">
                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-700" x-text="sn.label"></span>
                                <div class="flex gap-3">
                                    <label class="form-radio-sm" :class="formData[sn.key] === 'ya' && 'form-radio-sm-active'">
                                        <input type="radio" :name="sn.key" x-model="formData[sn.key]" value="ya" class="hidden"><span>Ya</span>
                                    </label>
                                    <label class="form-radio-sm" :class="formData[sn.key] === 'tidak' && 'form-radio-sm-active'">
                                        <input type="radio" :name="sn.key" x-model="formData[sn.key]" value="tidak" class="hidden"><span>Tidak</span>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 5. TADARUS AL-QURAN --}}
                <div class="form-section">
                    <h4 class="form-section-title">
                        <span class="form-section-num">5</span> Tadarus Al-Quran
                    </h4>
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <div>
                            <label class="text-[11px] font-medium text-gray-500 mb-1 block">Surat</label>
                            <input type="text" x-model="formData.tadarus_surat" placeholder="Nama surat" class="form-input">
                        </div>
                        <div>
                            <label class="text-[11px] font-medium text-gray-500 mb-1 block">Ayat</label>
                            <input type="text" x-model="formData.tadarus_ayat" placeholder="No. ayat" class="form-input">
                        </div>
                    </div>
                </div>

                {{-- 6. KEGIATAN HARIAN --}}
                <div class="form-section">
                    <h4 class="form-section-title">
                        <span class="form-section-num">6</span> Kegiatan Harian
                    </h4>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <template x-for="kg in [
                            {key:'dzikir_pagi', label:'Dzikir Pagi'},
                            {key:'olahraga', label:'Olahraga'},
                            {key:'membantu_ortu', label:'Membantu Ortu'},
                            {key:'membersihkan_kamar', label:'Bersihkan Kamar'},
                            {key:'membersihkan_rumah', label:'Bersihkan Rumah'},
                            {key:'membersihkan_halaman', label:'Bersihkan Halaman'},
                            {key:'merawat_lingkungan', label:'Rawat Lingkungan'},
                            {key:'dzikir_petang', label:'Dzikir Petang'},
                            {key:'sedekah', label:'Sedekah'},
                            {key:'buka_keluarga', label:'Buka Bersama Keluarga'},
                            {key:'literasi', label:'Literasi'},
                            {key:'menabung', label:'Menabung'},
                            {key:'tidur_cepat', label:'Tidur Cepat'},
                            {key:'bangun_pagi', label:'Bangun Pagi'}
                        ]" :key="kg.key">
                            <label class="form-kegiatan-item" :class="formData.kegiatan[kg.key] && 'form-kegiatan-active'">
                                <input type="checkbox" x-model="formData.kegiatan[kg.key]" class="hidden">
                                <svg x-show="formData.kegiatan[kg.key]" class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                <svg x-show="!formData.kegiatan[kg.key]" class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>
                                <span class="text-xs" x-text="kg.label"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- 7. RINGKASAN CERAMAH --}}
                <div class="form-section">
                    <h4 class="form-section-title">
                        <span class="form-section-num">7</span> Ringkasan Ceramah
                    </h4>
                    <textarea x-model="formData.ringkasan_ceramah" rows="4" placeholder="Tulis ringkasan ceramah hari ini..." class="form-textarea mt-2"></textarea>
                </div>

                </fieldset>

                {{-- Submit button --}}
                <div class="mt-5 mb-8">
                    <button @click="submitForm()" :disabled="formSubmitted || formSaving" class="form-submit-btn" :class="formSubmitted ? 'opacity-60 cursor-not-allowed' : ''">
                        <svg x-show="formSaving" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="formSaving ? 'Menyimpan...' : (formSubmitted ? 'Sudah Dikirim' : 'Kirim Formulir')"></span>
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-filament-panels::page>

@push('styles')
    <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/dashboard.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/formulir.css') }}?v={{ time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('themes/ramadhan/js/formulir.js') }}?v={{ time() }}"></script>
@endpush
