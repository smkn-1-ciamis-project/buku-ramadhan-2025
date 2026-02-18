<x-filament-panels::page>
    {{-- Kill ALL Filament wrapper spacing --}}
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
    <div x-data="ramadhanDashboard()" x-init="init()" class="ramadhan-app">

        {{-- ===== HERO HEADER ===== --}}
        <div class="hero-header">
            {{-- Islamic animated decorations --}}
            <div class="absolute inset-0 overflow-hidden pointer-events-none">

                {{-- Crescent moon kanan atas --}}
                <div class="islamic-deco islamic-deco-moon" style="top:8%; right:5%;">
                    <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M52 36C52 45.94 43.94 54 34 54C27.2 54 21.26 50.2 18 44.6C20.1 45.5 22.4 46 24.8 46C33.64 46 40.8 38.84 40.8 30C40.8 24.04 37.6 18.82 32.8 16C34.52 16 36.28 16.22 37.96 16.68C46.06 18.98 52 26.82 52 36Z" fill="white" opacity="0.15"/>
                        <path d="M52 36C52 45.94 43.94 54 34 54C27.2 54 21.26 50.2 18 44.6C20.1 45.5 22.4 46 24.8 46C33.64 46 40.8 38.84 40.8 30C40.8 24.04 37.6 18.82 32.8 16C34.52 16 36.28 16.22 37.96 16.68C46.06 18.98 52 26.82 52 36Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.35"/>
                    </svg>
                </div>

                {{-- Star bintang-bintang kecil --}}
                <div class="islamic-deco islamic-deco-star1" style="top:12%; right:18%;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white" opacity="0.4">
                        <polygon points="12,2 14.9,9.2 22.6,9.2 16.3,13.8 18.6,21 12,16.7 5.4,21 7.7,13.8 1.4,9.2 9.1,9.2"/>
                    </svg>
                </div>
                <div class="islamic-deco islamic-deco-star2" style="top:28%; right:12%;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="white" opacity="0.3">
                        <polygon points="12,2 14.9,9.2 22.6,9.2 16.3,13.8 18.6,21 12,16.7 5.4,21 7.7,13.8 1.4,9.2 9.1,9.2"/>
                    </svg>
                </div>
                <div class="islamic-deco islamic-deco-star3" style="top:18%; left:8%;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="white" opacity="0.2">
                        <polygon points="12,2 14.9,9.2 22.6,9.2 16.3,13.8 18.6,21 12,16.7 5.4,21 7.7,13.8 1.4,9.2 9.1,9.2"/>
                    </svg>
                </div>

                {{-- Masjid silhouette kiri bawah --}}
                <div class="islamic-deco islamic-deco-mosque" style="bottom:0; left:0;">
                    <svg width="180" height="100" viewBox="0 0 240 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Menara kiri -->
                        <rect x="10" y="40" width="18" height="90" fill="white" opacity="0.07"/>
                        <path d="M10 40 Q19 28 28 40Z" fill="white" opacity="0.07"/>
                        <rect x="7" y="36" width="24" height="5" fill="white" opacity="0.07"/>
                        <rect x="12" y="30" width="14" height="8" fill="white" opacity="0.07"/>
                        <path d="M14 30 Q19 20 24 30Z" fill="white" opacity="0.09"/>
                        <!-- Menara kanan -->
                        <rect x="212" y="40" width="18" height="90" fill="white" opacity="0.07"/>
                        <path d="M212 40 Q221 28 230 40Z" fill="white" opacity="0.07"/>
                        <rect x="209" y="36" width="24" height="5" fill="white" opacity="0.07"/>
                        <rect x="214" y="30" width="14" height="8" fill="white" opacity="0.07"/>
                        <path d="M216 30 Q221 20 226 30Z" fill="white" opacity="0.09"/>
                        <!-- Badan masjid -->
                        <rect x="30" y="60" width="180" height="70" fill="white" opacity="0.07"/>
                        <!-- Kubah utama -->
                        <path d="M80 60 Q120 10 160 60Z" fill="white" opacity="0.09"/>
                        <!-- Kubah kecil kiri -->
                        <path d="M38 60 Q58 38 78 60Z" fill="white" opacity="0.07"/>
                        <!-- Kubah kecil kanan -->
                        <path d="M162 60 Q182 38 202 60Z" fill="white" opacity="0.07"/>
                        <!-- Pintu -->
                        <path d="M108 130 L108 95 Q120 85 132 95 L132 130Z" fill="white" opacity="0.09"/>
                        <!-- Jendela -->
                        <path d="M60 80 Q70 70 80 80 L80 100 L60 100Z" fill="white" opacity="0.06"/>
                        <path d="M160 80 Q170 70 180 80 L180 100 L160 100Z" fill="white" opacity="0.06"/>
                    </svg>
                </div>

                {{-- Masjid silhouette kanan bawah --}}
                <div class="islamic-deco islamic-deco-mosque2" style="bottom:0; right:0;">
                    <svg width="160" height="90" viewBox="0 0 200 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Menara -->
                        <rect x="170" y="35" width="16" height="75" fill="white" opacity="0.07"/>
                        <path d="M170 35 Q178 24 186 35Z" fill="white" opacity="0.07"/>
                        <rect x="167" y="31" width="22" height="5" fill="white" opacity="0.07"/>
                        <path d="M172 28 Q178 18 184 28Z" fill="white" opacity="0.09"/>
                        <!-- Badan masjid -->
                        <rect x="0" y="55" width="170" height="55" fill="white" opacity="0.06"/>
                        <!-- Kubah -->
                        <path d="M45 55 Q85 15 125 55Z" fill="white" opacity="0.08"/>
                        <!-- Kubah kecil -->
                        <path d="M10 55 Q30 38 50 55Z" fill="white" opacity="0.06"/>
                        <path d="M125 55 Q145 38 165 55Z" fill="white" opacity="0.06"/>
                        <!-- Pintu -->
                        <path d="M75 110 L75 82 Q85 74 95 82 L95 110Z" fill="white" opacity="0.08"/>
                    </svg>
                </div>

                {{-- Geometric pattern (bintang 8) kiri atas --}}
                <div class="islamic-deco islamic-deco-geo" style="top:-20px; left:-20px;">
                    <svg width="120" height="120" viewBox="0 0 100 100" fill="none" opacity="0.06">
                        <path d="M50 10 L57 35 L82 25 L67 47 L90 58 L65 62 L72 88 L50 72 L28 88 L35 62 L10 58 L33 47 L18 25 L43 35 Z" fill="white" stroke="white" stroke-width="1"/>
                        <circle cx="50" cy="50" r="18" stroke="white" stroke-width="1.5" fill="none"/>
                        <path d="M50 32 L50 68 M32 50 L68 50 M36 36 L64 64 M64 36 L36 64" stroke="white" stroke-width="0.8" opacity="0.6"/>
                    </svg>
                </div>

                {{-- Lentera (fanoos) melayang --}}
                <div class="islamic-deco islamic-deco-lantern" style="top:10%; left:22%;">
                    <svg width="28" height="42" viewBox="0 0 28 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="14" y1="0" x2="14" y2="5" stroke="white" stroke-width="1.5" opacity="0.4"/>
                        <rect x="2" y="4" width="24" height="2" rx="1" fill="white" opacity="0.3"/>
                        <path d="M5 6 L3 32 Q14 38 25 32 L23 6Z" fill="white" opacity="0.08" stroke="white" stroke-width="0.8" opacity="0.25"/>
                        <path d="M5 6 L7 18 L14 14 L21 18 L23 6Z" fill="white" opacity="0.05"/>
                        <ellipse cx="14" cy="32" rx="11" ry="4" fill="white" opacity="0.1"/>
                        <rect x="10" y="32" width="8" height="6" rx="1" fill="white" opacity="0.15"/>
                        <line x1="8" y1="12" x2="8" y2="28" stroke="white" stroke-width="0.5" opacity="0.2"/>
                        <line x1="20" y1="12" x2="20" y2="28" stroke="white" stroke-width="0.5" opacity="0.2"/>
                        <line x1="5" y1="18" x2="23" y2="18" stroke="white" stroke-width="0.5" opacity="0.2"/>
                    </svg>
                </div>

                {{-- Titik-titik dekoratif --}}
                <div class="absolute top-12 right-16 w-2 h-2 bg-white/20 rounded-full islamic-deco-twinkle" style="animation-delay:0.3s"></div>
                <div class="absolute top-24 right-32 w-1.5 h-1.5 bg-white/15 rounded-full islamic-deco-twinkle" style="animation-delay:1.1s"></div>
                <div class="absolute bottom-32 left-1/4 w-1 h-1 bg-white/20 rounded-full islamic-deco-twinkle" style="animation-delay:0.7s"></div>
                <div class="absolute top-1/3 left-1/3 w-1.5 h-1.5 bg-white/10 rounded-full islamic-deco-twinkle" style="animation-delay:1.8s"></div>
                <div class="absolute top-1/2 right-1/4 w-1 h-1 bg-white/15 rounded-full islamic-deco-twinkle" style="animation-delay:2.2s"></div>
            </div>

            <div class="hero-inner">
                {{-- Top bar: logo + date --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/logo_smk.png') }}" alt="SMKN 1 Ciamis" class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-white/20 p-0.5 ring-2 ring-white/25">
                        <div>
                            <h1 class="text-white font-bold text-sm lg:text-lg leading-tight">Buku Ramadhan</h1>
                            <p class="text-blue-100 text-[11px] lg:text-xs">SMKN 1 Ciamis</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-white text-[11px] lg:text-sm font-medium" x-text="hijriDate"></p>
                        <p class="text-blue-100 text-[10px] lg:text-xs" x-text="gregorianDate"></p>
                    </div>
                </div>

                {{-- Current prayer time (centered vertically) --}}
                <div class="text-center flex-1 flex flex-col items-center justify-center gap-0">
                    {{-- Real-time clock WIB --}}
                    <p class="text-white text-6xl lg:text-7xl font-extrabold leading-none tracking-tight" x-text="clockWIB"></p>
                    {{-- WIB / WITA / WIT row --}}
                    <div class="mt-3 flex items-center gap-3 text-blue-100">
                        <span class="text-xs font-bold bg-white/15 rounded-full px-3 py-0.5">WIB</span>
                        <span class="text-blue-300/40 text-[10px]">&bull;</span>
                        <span class="text-xs font-medium"><span class="text-blue-200/60 mr-1">WITA</span> <span x-text="clockWITA"></span></span>
                        <span class="text-blue-300/40 text-[10px]">&bull;</span>
                        <span class="text-xs font-medium"><span class="text-blue-200/60 mr-1">WIT</span> <span x-text="clockWIT"></span></span>
                    </div>
                    <div class="mt-4 inline-flex items-center gap-2 bg-white/10 backdrop-blur-md rounded-full px-5 py-2">
                        <svg class="w-3.5 h-3.5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.828a1 1 0 101.415-1.414L11 9.586V6z" clip-rule="evenodd"/></svg>
                        <span class="text-yellow-100 text-xs font-semibold" x-text="countdown"></span>
                    </div>
                    {{-- Location row --}}
                    <div class="mt-4 inline-flex items-center gap-1.5 text-blue-200/90">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                        <span class="text-[11px] font-medium" x-text="locationCity"></span>
                        <span class="text-blue-300/50 text-[10px]">&bull;</span>
                        <span class="text-[10px] text-blue-200/70" x-text="locationCoords"></span>
                        <button @click="locationSearch = ''; filteredLocations = indonesiaLocations; showLocationPicker = true" class="ml-1 text-blue-300 hover:text-white transition-colors" title="Ubah lokasi">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Prayer times bar (inside hero, bottom section) --}}
                <div class="prayer-row-section">
                    <div class="prayer-row">
                        <template x-for="prayer in prayerTimes" :key="prayer.name">
                            <button class="prayer-slot" :class="prayer.isActive && 'active'">
                                <span class="prayer-slot-name" x-text="prayer.name"></span>
                                <span class="prayer-slot-time" x-text="prayer.time"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== LOCATION PICKER MODAL ===== --}}
        <div x-show="showLocationPicker" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center px-4" style="background:rgba(0,0,0,0.6)" @click.self="showLocationPicker = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" style="max-height:80vh; display:flex; flex-direction:column;" @click.stop>

                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-5 py-4 flex items-center justify-between" style="flex-shrink:0">
                    <h3 class="text-white font-bold text-sm">Pilih Lokasi (Kabupaten/Kota)</h3>
                    <button @click="showLocationPicker = false" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Search input --}}
                <div class="px-4 pt-4 pb-2" style="flex-shrink:0">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9ca3af" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input
                            x-model="locationSearch"
                            @input="filterLocations()"
                            type="text"
                            placeholder="Ketik nama kabupaten/kota atau provinsi..."
                            style="width:100%; padding:10px 16px 10px 36px; border:1px solid #d1d5db; border-radius:12px; font-size:14px; color:#111827; background:#fff; outline:none; box-sizing:border-box;"
                            @focus="$el.style.borderColor='#3b82f6'; $el.style.boxShadow='0 0 0 3px rgba(59,130,246,0.15)'"
                            @blur="$el.style.borderColor='#d1d5db'; $el.style.boxShadow='none'"
                        >
                    </div>
                    <p style="font-size:11px; color:#9ca3af; margin-top:6px; margin-left:4px;" x-text="filteredLocations.length + ' lokasi tersedia'"></p>
                </div>

                {{-- Scrollable list --}}
                <div style="overflow-y:auto; flex:1; border-top:1px solid #f3f4f6; margin:0 16px 8px; border-radius:12px; border:1px solid #f1f5f9;">
                    <template x-for="loc in filteredLocations.slice(0, 100)" :key="loc.id">
                        <button
                            @click="selectLocation(loc)"
                            style="width:100%; text-align:left; padding:10px 16px; border-bottom:1px solid #f9fafb; cursor:pointer; background:transparent; display:block;"
                            :style="locationCity && locationCity.includes(loc.kabupaten) ? 'background:#eff6ff;' : ''"
                            @mouseover="$el.style.background='#eff6ff'"
                            @mouseout="$el.style.background = (locationCity && locationCity.includes(loc.kabupaten)) ? '#eff6ff' : 'transparent'"
                        >
                            <span style="font-size:14px; font-weight:500; color:#111827;" x-text="loc.kabupaten"></span>
                            <span style="font-size:12px; color:#9ca3af; margin-left:6px;" x-text="loc.provinsi"></span>
                        </button>
                    </template>
                    <p x-show="filteredLocations.length === 0" style="text-align:center; color:#9ca3af; font-size:14px; padding:32px 16px;">Tidak ditemukan</p>
                </div>

                {{-- GPS button --}}
                <div style="padding:0 16px 16px; flex-shrink:0;">
                    <button @click="useGPS()" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:10px; border-radius:12px; border:2px solid #3b82f6; color:#2563eb; font-size:14px; font-weight:600; background:transparent; cursor:pointer;"
                        @mouseover="$el.style.background='#eff6ff'" @mouseout="$el.style.background='transparent'">
                        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        Gunakan GPS Otomatis
                    </button>
                </div>

            </div>
        </div>

        {{-- ===== BODY ===== --}}
        <div class="ramadhan-body">
            <div class="ramadhan-content">

                {{-- Centered menu bar --}}
                <div class="center-menu-wrap">
                    <div class="center-menu">
                        <template x-for="tab in sidebarTabs" :key="tab.id">
                            <button @click="activeTab = tab.id" class="center-menu-btn" :class="activeTab === tab.id && 'active'">
                                <div class="center-menu-icon">
                                    <svg x-show="tab.id === 'calendar'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                    <svg x-show="tab.id === 'qibla'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    <svg x-show="tab.id === 'schedule'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <svg x-show="tab.id === 'dua'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                </div>
                                <span class="center-menu-label" x-text="tab.mobileLabel"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Tab content --}}
                <div class="content-area">

                    {{-- KALENDER RAMADHAN --}}
                    <div x-show="activeTab === 'calendar'" x-transition.opacity.duration.200ms>
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="text-white font-bold text-sm lg:text-base">Kalender Ramadhan 1446 H</h3>
                                    <p class="text-blue-100 text-[11px] mt-0.5">Maret - April 2025</p>
                                </div>
                                <span class="bg-white/20 text-white text-[10px] font-bold px-3 py-1 rounded-md" x-text="'Hari ke-' + ramadhanDay"></span>
                            </div>
                            <div class="p-4 lg:p-5">
                                {{-- Weekday header --}}
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    <template x-for="d in ['Sen','Sel','Rab','Kam','Jum','Sab','Min']">
                                        <div class="text-center text-[10px] font-bold text-gray-500 uppercase py-1" x-text="d"></div>
                                    </template>
                                </div>
                                {{-- Calendar grid --}}
                                <div class="grid grid-cols-7 gap-1">
                                    <template x-for="item in calendarDays" :key="item.key">
                                        <div class="calendar-day"
                                            :class="{
                                                'calendar-day-today': item.isToday,
                                                'calendar-day-completed': item.isCompleted && !item.isToday,
                                                'calendar-day-future': !item.isToday && !item.isCompleted && item.day > 0,
                                                'calendar-day-empty': item.day <= 0
                                            }">
                                            <span x-text="item.day > 0 ? item.day : ''"></span>
                                        </div>
                                    </template>
                                </div>
                                {{-- Legend --}}
                                <div class="flex items-center justify-center gap-6 mt-4 pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div><span class="text-[10px] text-gray-500 font-medium">Hari ini</span></div>
                                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div><span class="text-[10px] text-gray-500 font-medium">Sudah lewat</span></div>
                                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-gray-200"></div><span class="text-[10px] text-gray-500 font-medium">Akan datang</span></div>
                                </div>
                            </div>
                        </div>

                        {{-- Progress --}}
                        <div class="card mt-3 p-4 lg:p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-bold text-gray-700">Progress Ramadhan</h4>
                                <span class="text-blue-600 font-bold text-sm" x-text="Math.round((ramadhanDay/30)*100) + '%'"></span>
                            </div>
                            <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-1000" :style="'width:'+Math.round((ramadhanDay/30)*100)+'%'"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2" x-text="'Hari ke-' + ramadhanDay + ' dari 30 hari'"></p>
                        </div>
                    </div>

                    {{-- ARAH KIBLAT --}}
                    <div x-show="activeTab === 'qibla'" x-transition.opacity.duration.200ms>
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="text-white font-bold text-sm lg:text-base">Arah Kiblat</h3>
                                    <p class="text-blue-100 text-[11px] mt-0.5">Menggunakan GPS perangkat Anda</p>
                                </div>
                            </div>
                            <div class="p-6 lg:p-8">
                                <div class="flex justify-center mb-6">
                                    <div class="relative w-56 h-56 lg:w-64 lg:h-64">
                                        <div class="w-full h-full rounded-full border-[3px] border-blue-100 bg-gradient-to-b from-slate-50 to-white shadow-[inset_0_2px_8px_rgba(0,0,0,0.06)] relative">
                                            <span class="absolute top-2 left-1/2 -translate-x-1/2 text-xs font-bold text-red-500">U</span>
                                            <span class="absolute bottom-2 left-1/2 -translate-x-1/2 text-xs font-bold text-gray-400">S</span>
                                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">B</span>
                                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">T</span>
                                            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 200 200">
                                                <template x-for="i in 72">
                                                    <line :x1="100+90*Math.sin(i*5*Math.PI/180)" :y1="100-90*Math.cos(i*5*Math.PI/180)"
                                                          :x2="100+(i%6===0?80:86)*Math.sin(i*5*Math.PI/180)" :y2="100-(i%6===0?80:86)*Math.cos(i*5*Math.PI/180)"
                                                          :stroke="i%6===0?'#94a3b8':'#e2e8f0'" :stroke-width="i%6===0?1.5:0.5"/>
                                                </template>
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center transition-transform duration-700 ease-out" :style="'transform:rotate('+qiblaDirection+'deg)'">
                                                <svg width="160" height="160" viewBox="0 0 200 200" class="drop-shadow">
                                                    <defs><linearGradient id="ag" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#2563eb"/><stop offset="100%" stop-color="#1e40af"/></linearGradient></defs>
                                                    <polygon points="100,30 107,92 100,84 93,92" fill="url(#ag)"/>
                                                    <polygon points="100,170 107,108 100,116 93,108" fill="#cbd5e1"/>
                                                    <circle cx="100" cy="100" r="5" fill="#2563eb" stroke="white" stroke-width="2"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mb-5">
                                    <p class="text-3xl font-extrabold text-blue-700" x-text="qiblaDirection.toFixed(1) + '°'"></p>
                                    <p class="text-sm text-gray-500 mt-1" x-text="qiblaStatus"></p>
                                </div>
                                <div class="bg-blue-50 rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                        <span class="text-sm font-semibold text-blue-700">Lokasi Anda</span>
                                    </div>
                                    <p class="text-xs text-blue-600 mb-3" x-text="locationText"></p>
                                    <button @click="getLocation()" class="text-xs font-semibold bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 active:scale-95 transition-all">
                                        Perbarui Lokasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- JADWAL SHOLAT --}}
                    <div x-show="activeTab === 'schedule'" x-transition.opacity.duration.200ms>
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="text-white font-bold text-sm lg:text-base">Jadwal Sholat Hari Ini</h3>
                                    <p class="text-blue-100 text-[11px] mt-0.5" x-text="gregorianDate"></p>
                                </div>
                                <span class="bg-white/20 text-white text-[10px] font-semibold px-3 py-1 rounded-md" x-text="cityName"></span>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <template x-for="prayer in fullPrayerSchedule" :key="prayer.name">
                                    <div class="flex items-center px-4 py-3.5 transition-colors" :class="prayer.isActive ? 'bg-blue-50' : 'hover:bg-gray-50'">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center mr-3.5"
                                            :class="prayer.isActive ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold" :class="prayer.isActive ? 'text-blue-700' : 'text-gray-700'" x-text="prayer.name"></p>
                                            <p class="text-[11px] text-gray-400" x-text="prayer.arabic"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold tabular-nums" :class="prayer.isActive ? 'text-blue-700' : 'text-gray-700'" x-text="prayer.time"></p>
                                            <template x-if="prayer.isActive">
                                                <span class="inline-block text-[9px] bg-blue-600 text-white px-1.5 py-0.5 rounded font-semibold mt-0.5">Sekarang</span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Imsak & Berbuka cards --}}
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div class="card-highlight card-highlight-imsak">
                                <div class="absolute top-0 right-0 w-20 h-20 bg-white/5 rounded-full -translate-y-6 translate-x-6"></div>
                                <p class="relative text-blue-100 text-[10px] font-semibold uppercase tracking-wider">Imsak</p>
                                <p class="relative text-white text-2xl font-extrabold mt-1" x-text="imsakTime"></p>
                            </div>
                            <div class="card-highlight card-highlight-berbuka">
                                <div class="absolute top-0 right-0 w-20 h-20 bg-white/5 rounded-full -translate-y-6 translate-x-6"></div>
                                <p class="relative text-orange-100 text-[10px] font-semibold uppercase tracking-wider">Berbuka</p>
                                <p class="relative text-white text-2xl font-extrabold mt-1" x-text="maghribTime"></p>
                            </div>
                        </div>
                    </div>

                    {{-- DOA HARIAN --}}
                    <div x-show="activeTab === 'dua'" x-transition.opacity.duration.200ms>
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="text-white font-bold text-sm lg:text-base">Doa Harian Ramadhan</h3>
                                    <p class="text-blue-100 text-[11px] mt-0.5">Doa-doa penting di bulan Ramadhan</p>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <template x-for="(dua, index) in duas" :key="dua.title">
                                    <div class="px-4 py-5 lg:px-6">
                                        <div class="flex items-center gap-2.5 mb-3">
                                            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-700 text-xs font-bold" x-text="index + 1"></span>
                                            </div>
                                            <h4 class="font-bold text-sm text-gray-800" x-text="dua.title"></h4>
                                        </div>
                                        <p class="text-right text-xl leading-[2.4] text-gray-800 font-arabic mb-3" x-text="dua.arabic" dir="rtl"></p>
                                        <p class="text-xs text-gray-500 italic mb-1.5" x-text="dua.latin"></p>
                                        <p class="text-xs text-gray-600 leading-relaxed" x-text="dua.meaning"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- AYAT HARI INI (always visible) --}}
                    <div class="card p-4 lg:p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-amber-600 font-bold uppercase tracking-wider mb-1.5">Ayat Hari Ini</p>
                                <p class="text-sm text-gray-700 italic leading-relaxed" x-text="dailyVerse.text"></p>
                                <p class="text-xs text-gray-500 mt-2 font-semibold" x-text="dailyVerse.source"></p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>

@push('styles')
    <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/dashboard.css') }}?v={{ time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('themes/ramadhan/js/dashboard.js') }}?v={{ time() }}"></script>
@endpush
