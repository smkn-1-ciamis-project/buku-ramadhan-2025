<x-filament-panels::page>
    <script>window.__siswaUserId = '{{ auth()->id() }}'; window.__appSettings = @json(\App\Models\AppSetting::getForFrontend());</script>
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
    <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/al-quran.css') }}">

    <div x-data="alQuranPage()" x-init="init()" class="quran-page">

        {{-- Top bar --}}
        <div class="quran-topbar">
            <a href="{{ \App\Filament\Siswa\Pages\Muslim\Dashboard::getUrl() }}" class="quran-back-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="quran-topbar-center">
                <h1 class="quran-topbar-title">Al-Quran Digital</h1>
                <p class="quran-topbar-sub">Bacaan lengkap 30 Juz &bull; 114 Surah</p>
            </div>
            <button class="quran-topbar-action" @click="if(view === 'read') { goBack(); $nextTick(() => { showSearch = true; }); } else { showSearch = !showSearch; }" title="Cari Surah">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            </button>
            {{-- Desktop search button for surah detail --}}
            <button x-show="view === 'read'" @click="goBack(); $nextTick(() => { if($refs.desktopSearchInput) $refs.desktopSearchInput.focus(); })" class="quran-topbar-search-detail" title="Cari Surah">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <span>Cari Surah</span>
            </button>
        </div>

        {{-- Search bar (mobile toggle) --}}
        <div x-show="showSearch && view === 'list'" x-transition.origin.top class="quran-search-wrap">
            <div class="quran-search-inner">
                <svg class="quran-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" x-model="searchQuery" @input.debounce.300ms="filterSurahs()"
                       placeholder="Cari surah... (contoh: Al-Baqarah, Yasin)"
                       class="quran-search-input">
                <button x-show="searchQuery.length > 0" @click="searchQuery = ''; filterSurahs()" class="quran-search-clear">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Search bar (desktop always visible) --}}
        <div x-show="view === 'list'" class="quran-search-desktop">
            <div class="quran-search-inner">
                <svg class="quran-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" x-model="searchQuery" @input.debounce.300ms="filterSurahs()"
                       placeholder="Cari surah... (contoh: Al-Baqarah, Yasin)"
                       class="quran-search-input" x-ref="desktopSearchInput">
                <button x-show="searchQuery.length > 0" @click="searchQuery = ''; filterSurahs()" class="quran-search-clear">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Main body --}}
        <div class="quran-body">
            <div class="quran-content">

                {{-- VIEW: Surah List --}}
                <div x-show="view === 'list'" x-transition.opacity.duration.150ms>

                    {{-- Juz filter --}}
                    <div class="quran-juz-filter">
                        <button @click="selectedJuz = 0; filterSurahs()" class="quran-juz-btn" :class="selectedJuz === 0 && 'quran-juz-active'">Semua</button>
                        <template x-for="j in 30" :key="j">
                            <button @click="selectedJuz = j; filterSurahs()" class="quran-juz-btn" :class="selectedJuz === j && 'quran-juz-active'" x-text="'Juz ' + j"></button>
                        </template>
                    </div>

                    {{-- Loading --}}
                    <div x-show="loadingSurahs" class="quran-loading">
                        <div class="quran-spinner"></div>
                        <span>Memuat daftar surah...</span>
                    </div>

                    {{-- Surah list --}}
                    <div x-show="!loadingSurahs" class="quran-surah-list">
                        <template x-for="surah in displayedSurahs" :key="surah.number">
                            <button @click="openSurah(surah.number)" class="quran-surah-item">
                                <div class="quran-surah-num">
                                    <span x-text="surah.number"></span>
                                </div>
                                <div class="quran-surah-info">
                                    <div class="quran-surah-name" x-text="surah.englishName"></div>
                                    <div class="quran-surah-meta">
                                        <span class="quran-surah-type" x-text="getSurahTranslation(surah.number)"></span>
                                        <span class="quran-surah-dot">&bull;</span>
                                        <span x-text="surah.numberOfAyahs + ' ayat'"></span>
                                    </div>
                                </div>
                                <div class="quran-surah-arabic" x-text="surah.name"></div>
                            </button>
                        </template>

                        {{-- Empty state --}}
                        <div x-show="displayedSurahs.length === 0 && !loadingSurahs" class="quran-empty">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            <p>Surah tidak ditemukan</p>
                        </div>
                    </div>
                </div>

                {{-- VIEW: Surah Detail (Ayah Reader) --}}
                <div x-show="view === 'read'" x-transition.opacity.duration.150ms>

                    {{-- Surah header --}}
                    <div class="quran-reader-header">
                        <button @click="goBack()" class="quran-reader-back">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                            Daftar Surah
                        </button>
                        <div class="quran-reader-title-card">
                            <div class="quran-reader-title-bg"></div>
                            <div class="quran-reader-title-inner">
                                <h2 class="quran-reader-surah-name" x-text="currentSurah.englishName"></h2>
                                <div class="quran-reader-surah-arabic" x-text="currentSurah.name"></div>
                                <div class="quran-reader-surah-info">
                                    <span x-text="getSurahTranslation(currentSurah.number)"></span>
                                    <span>&bull;</span>
                                    <span x-text="currentSurah.numberOfAyahs + ' ayat'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Audio controls OUTSIDE title-card so dropdown is not clipped --}}
                        <div class="quran-reader-audio-ctrls" x-show="!loadingAyahs && currentAyahs.length > 0">
                            {{-- Reciter picker --}}
                            <div class="quran-reciter-wrap">
                                {{-- Mobile backdrop --}}
                                <div x-show="showReciterPicker" @click="showReciterPicker = false" class="quran-reciter-backdrop"></div>
                                <button @click="showReciterPicker = !showReciterPicker" class="quran-reciter-btn" title="Pilih Qari">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/></svg>
                                    <span class="quran-reciter-name" x-text="getReciterName()"></span>
                                    <svg class="quran-reciter-chevron" :class="showReciterPicker && 'quran-reciter-chevron-up'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </button>
                                {{-- Dropdown --}}
                                <div x-show="showReciterPicker"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-full"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-full"
                                     @click.outside="showReciterPicker = false" class="quran-reciter-dropdown">
                                    {{-- Drag handle --}}
                                    <div class="quran-reciter-handle-bar"></div>
                                    <div class="quran-reciter-dropdown-header">
                                        <div class="quran-reciter-dropdown-title">Pilih Qari (Pembaca)</div>
                                        <button @click="showReciterPicker = false" class="quran-reciter-close-btn" aria-label="Tutup">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <template x-for="r in reciters" :key="r.id">
                                        <button @click="changeReciter(r.id)" class="quran-reciter-option" :class="selectedReciter === r.id && 'quran-reciter-option-active'">
                                            <div class="quran-reciter-option-info">
                                                <span class="quran-reciter-option-name" x-text="r.name"></span>
                                                <span class="quran-reciter-option-style" x-text="r.style"></span>
                                            </div>
                                            <svg x-show="selectedReciter === r.id" fill="currentColor" viewBox="0 0 20 20" class="quran-reciter-check"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <button @click="toggleAutoPlay()" class="quran-auto-play-btn" :class="autoPlay && 'quran-auto-play-active'" :title="autoPlay ? 'Matikan putar otomatis' : 'Putar otomatis semua ayat'">
                                <svg x-show="!autoPlay" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                                <svg x-show="autoPlay" fill="currentColor" viewBox="0 0 24 24"><path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                                <span x-text="autoPlay ? 'Auto-Play ON' : 'Putar Semua'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Bismillah --}}
                    <div x-show="currentSurah.number !== 1 && currentSurah.number !== 9" class="quran-bismillah">
                        بِسْمِ ٱللَّهِ ٱلرَّحْمَـٰنِ ٱلرَّحِيمِ
                    </div>

                    {{-- Ayah search bar --}}
                    <div x-show="!loadingAyahs && currentAyahs.length > 0" class="quran-ayah-search-wrap">
                        <div class="quran-ayah-search-inner">
                            <svg class="quran-ayah-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            <input type="text" x-model="ayahSearchQuery" @input.debounce.200ms="filterAyahs()"
                                   placeholder="Cari ayat... (nomor atau terjemahan)"
                                   class="quran-ayah-search-input" x-ref="ayahSearchInput">
                            <button x-show="ayahSearchQuery.length > 0" @click="ayahSearchQuery = ''; filterAyahs()" class="quran-ayah-search-clear">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div x-show="ayahSearchQuery.length > 0" class="quran-ayah-search-count">
                            <span x-text="filteredAyahs.length + ' dari ' + currentAyahs.length + ' ayat'"></span>
                        </div>
                    </div>

                    {{-- Loading ayahs --}}
                    <div x-show="loadingAyahs" class="quran-loading">
                        <div class="quran-spinner"></div>
                        <span>Memuat ayat...</span>
                    </div>

                    {{-- Ayah list --}}
                    <div x-show="!loadingAyahs" class="quran-ayah-list">
                        <template x-for="(ayah, idx) in filteredAyahs" :key="ayah.numberInSurah">
                            <div class="quran-ayah-item" :id="'ayah-' + (ayah.numberInSurah - 1)" :class="playingIndex === (ayah.numberInSurah - 1) && 'quran-ayah-playing'">
                                <div class="quran-ayah-header">
                                    <span class="quran-ayah-badge" x-text="currentSurah.englishName + ' : ' + ayah.numberInSurah"></span>
                                    <div class="quran-ayah-header-right">
                                        <span class="quran-ayah-juz" x-text="'Juz ' + ayah.juz"></span>
                                        <button x-show="ayah.audio" @click="playAyah(ayah.numberInSurah - 1)" class="quran-ayah-play-btn" :class="{'quran-ayah-play-active': playingIndex === (ayah.numberInSurah - 1) && isPlaying}" :title="playingIndex === (ayah.numberInSurah - 1) && isPlaying ? 'Jeda' : 'Putar'">
                                            {{-- Play icon --}}
                                            <svg x-show="!(playingIndex === (ayah.numberInSurah - 1) && isPlaying)" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5.14v14l11-7-11-7z"/></svg>
                                            {{-- Pause icon --}}
                                            <svg x-show="playingIndex === (ayah.numberInSurah - 1) && isPlaying" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                        </button>
                                    </div>
                                </div>
                                {{-- Audio loading indicator for this ayah --}}
                                <div x-show="playingIndex === (ayah.numberInSurah - 1) && audioLoading" class="quran-ayah-audio-loading">
                                    <div class="quran-spinner" style="width:18px;height:18px;border-width:2px"></div>
                                    <span>Memuat audio...</span>
                                </div>
                                <div class="quran-ayah-arabic" x-text="ayah.arabic"></div>
                                <div class="quran-ayah-translation" x-text="ayah.translation"></div>
                            </div>
                        </template>
                    </div>

                    {{-- Surah navigation --}}
                    <div x-show="!loadingAyahs && currentAyahs.length > 0" class="quran-surah-nav">
                        <button @click="openSurah(currentSurah.number - 1)" :disabled="currentSurah.number <= 1" class="quran-nav-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                            Surah Sebelumnya
                        </button>
                        <button @click="openSurah(currentSurah.number + 1)" :disabled="currentSurah.number >= 114" class="quran-nav-btn">
                            Surah Selanjutnya
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </button>
                    </div>

                    {{-- Error state --}}
                    <div x-show="ayahError" class="quran-error">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <p x-text="ayahError"></p>
                        <button @click="openSurah(currentSurah.number)" class="quran-retry-btn">Coba Lagi</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Floating audio player bar --}}
        <div x-show="playingIndex >= 0 && view === 'read'" x-transition.opacity.duration.200ms class="quran-audio-bar">
            <div class="quran-audio-bar-inner">
                {{-- Progress bar --}}
                <div class="quran-audio-progress" @click="seekAudio($event)">
                    <div class="quran-audio-progress-fill" :style="'width:' + (audioDuration ? (audioCurrentTime / audioDuration * 100) : 0) + '%'"></div>
                </div>
                <div class="quran-audio-bar-content">
                    {{-- Info --}}
                    <div class="quran-audio-info">
                        <span class="quran-audio-info-surah" x-text="currentSurah.englishName"></span>
                        <span class="quran-audio-info-ayah">
                            <span x-text="'Ayat ' + (currentAyahs[playingIndex] ? currentAyahs[playingIndex].numberInSurah : '')"></span>
                            <span class="quran-audio-info-sep">&bull;</span>
                            <span x-text="getReciterName()"></span>
                        </span>
                    </div>
                    {{-- Controls --}}
                    <div class="quran-audio-controls">
                        <span class="quran-audio-time" x-text="formatTime(audioCurrentTime)"></span>
                        {{-- Prev ayah --}}
                        <button @click="playingIndex > 0 && playAyah(playingIndex - 1)" :disabled="playingIndex <= 0" class="quran-audio-ctrl-btn" title="Ayat sebelumnya">
                            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
                        </button>
                        {{-- Play / Pause --}}
                        <button @click="playAyah(playingIndex)" class="quran-audio-play-main" :title="isPlaying ? 'Jeda' : 'Lanjut'">
                            <svg x-show="!isPlaying" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5.14v14l11-7-11-7z"/></svg>
                            <svg x-show="isPlaying" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                        </button>
                        {{-- Next ayah --}}
                        <button @click="playingIndex < currentAyahs.length - 1 && playAyah(playingIndex + 1)" :disabled="playingIndex >= currentAyahs.length - 1" class="quran-audio-ctrl-btn" title="Ayat selanjutnya">
                            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
                        </button>
                        <span class="quran-audio-time" x-text="formatTime(audioDuration)"></span>
                        {{-- Auto-play toggle --}}
                        <button @click="toggleAutoPlay()" class="quran-audio-ctrl-btn" :class="autoPlay && 'quran-audio-ctrl-active'" :title="autoPlay ? 'Auto-play ON' : 'Auto-play OFF'">
                            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M7 7h10v10H7z" x-show="!autoPlay"/><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z" x-show="autoPlay"/></svg>
                        </button>
                        {{-- Stop --}}
                        <button @click="stopAudio()" class="quran-audio-ctrl-btn" title="Berhenti">
                            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer watermark --}}
        <div class="watermark-footer">
            <a href="{{ route('tim-pengembang') }}" target="_blank" rel="noopener">SMKN 1 Ciamis &bull; Calakan</a>
        </div>
    </div>

    <script src="{{ asset('themes/ramadhan/js/al-quran.js') }}"></script>
</x-filament-panels::page>
