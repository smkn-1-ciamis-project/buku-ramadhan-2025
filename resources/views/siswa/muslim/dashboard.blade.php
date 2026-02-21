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
            <div class="absolute inset-0 overflow-hidden pointer-events-none" style="border-radius: 0 0 2rem 2rem;">

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
                        <img src="{{ asset('img/logo_smk.png') }}" alt="SMKN 1 Ciamis" class="w-10 h-10 lg:w-12 lg:h-12">
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
                <div class="text-center flex-1 flex flex-col items-center justify-center gap-2">
                    {{-- Greeting --}}
                    <p class="greeting-text" x-text="greeting"></p>
                    {{-- Real-time clock (local to selected city) --}}
                    <p class="clock-display" x-text="clockMain"></p>
                    {{-- Timezone row --}}
                    <div class="clock-timezone-row">
                        <span class="tz-badge" :class="selectedTz === 'WIB' && 'tz-active'">WIB <span x-show="selectedTz !== 'WIB'" x-text="clockWIB" style="font-weight:400"></span></span>
                        <span class="tz-dot">&bull;</span>
                        <span class="tz-badge" :class="selectedTz === 'WITA' && 'tz-active'">WITA <span x-show="selectedTz !== 'WITA'" x-text="clockWITA" style="font-weight:400"></span></span>
                        <span class="tz-dot">&bull;</span>
                        <span class="tz-badge" :class="selectedTz === 'WIT' && 'tz-active'">WIT <span x-show="selectedTz !== 'WIT'" x-text="clockWIT" style="font-weight:400"></span></span>
                    </div>
                    <div class="countdown-badge">
                        <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.828a1 1 0 101.415-1.414L11 9.586V6z" clip-rule="evenodd"/></svg>
                        <span x-text="countdown"></span>
                    </div>
                    {{-- Location row with dropdown --}}
                    <div class="location-dropdown-wrap mt-5">
                        <button @click="openLocationPicker()" class="inline-flex items-center gap-1.5 text-blue-200/90 hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                            </svg>
                            <span class="text-[11px] font-medium" x-text="locationCity"></span>
                            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="showLocationPicker && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </button>

                        {{-- Click-outside overlay --}}
                        <div x-show="showLocationPicker" class="location-dropdown-overlay" @click="showLocationPicker = false"></div>

                        {{-- Dropdown --}}
                        <div x-show="showLocationPicker" class="location-dropdown" @click.stop>
                            {{-- Search --}}
                            <div class="location-dropdown-search" style="position:relative;">
                                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input x-model="locationSearch" @input="filterLocations()" type="text" placeholder="Cari kecamatan, kabupaten...">
                            </div>
                            {{-- List --}}
                            <div class="location-dropdown-list">
                                {{-- Loading state --}}
                                <p x-show="locationsLoading" class="location-dropdown-loading">Memuat lokasi...</p>
                                {{-- Location items --}}
                                <template x-if="!locationsLoading">
                                    <div>
                                        <template x-for="loc in filteredLocations.slice(0, 100)" :key="loc.id">
                                            <button @click="selectLocation(loc)" class="location-dropdown-item" :style="locationCity && locationCity.includes(loc.kabupaten) ? 'background:#eff6ff' : ''">
                                                <span class="loc-name" x-text="loc.kecamatan ? loc.kecamatan : loc.kabupaten"></span>
                                                <span class="loc-detail" x-text="loc.kecamatan ? loc.kabupaten + ', ' + loc.provinsi : loc.provinsi"></span>
                                            </button>
                                        </template>
                                        <p x-show="filteredLocations.length > 100 && !locationSearch" class="location-dropdown-hint">Ketik untuk mempersempit pencarian...</p>
                                        <p x-show="filteredLocations.length === 0 && locationSearch" class="location-dropdown-empty">Tidak ditemukan</p>
                                    </div>
                                </template>
                            </div>
                            {{-- GPS button --}}
                            <div class="location-dropdown-footer">
                                <button @click="useGPS()" class="location-dropdown-gps">
                                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    GPS Otomatis
                                </button>
                            </div>
                        </div>
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

        {{-- ===== BODY ===== --}}
        <div class="ramadhan-body">
            <div class="ramadhan-content">

                {{-- Centered menu bar --}}
                <div class="center-menu-wrap">
                    <div class="center-menu">
                        {{-- Left tabs: Kalender, Jadwal --}}
                        <template x-for="tab in sidebarTabs.slice(0, 2)" :key="tab.id">
                            <button @click="activeTab = tab.id" class="center-menu-btn" :class="activeTab === tab.id && 'active'">
                                <div class="center-menu-icon">
                                    <svg x-show="tab.id === 'calendar'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                    <svg x-show="tab.id === 'schedule'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="center-menu-label" x-text="tab.mobileLabel"></span>
                            </button>
                        </template>

                        {{-- Center: Formulir (same style, links to separate page) --}}
                        <a href="{{ \App\Filament\Siswa\Pages\Muslim\FormulirHarian::getUrl() }}" class="center-menu-btn" style="text-decoration:none;" target="_blank" rel="noopener noreferrer">
                            <div class="center-menu-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                            </div>
                            <span class="center-menu-label">Formulir</span>
                        </a>

                        {{-- Right tabs: Kiblat, Doa, Akun --}}
                        <template x-for="tab in sidebarTabs.slice(2)" :key="tab.id">
                            <button @click="activeTab = tab.id" class="center-menu-btn" :class="activeTab === tab.id && 'active'">
                                <div class="center-menu-icon">
                                    <svg x-show="tab.id === 'qibla'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    <svg x-show="tab.id === 'dua'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                    <svg x-show="tab.id === 'account'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
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
                                    <h3 class="text-white font-bold text-sm lg:text-base">Kalender Ramadhan 1447 H</h3>
                                    <p class="text-white/80 text-[11px] mt-0.5" x-text="calendarMonthLabel" style="color: rgba(255,255,255,0.8) !important;"></p>
                                </div>
                                <span class="bg-white/20 text-white text-[10px] font-bold px-3 py-1 rounded-md" x-text="'Hari ke-' + ramadhanDay"></span>
                            </div>

                            {{-- Missed days alert banner --}}
                            <template x-if="calendarDays.filter(d => d.isPastUnfilled).length > 0">
                                <div class="cal-alert">
                                    <div class="cal-alert-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                    </div>
                                    <div class="cal-alert-body">
                                        <p class="cal-alert-title">Ada hari yang belum diisi!</p>
                                        <p class="cal-alert-sub" x-text="calendarDays.filter(d => d.isPastUnfilled).length + ' hari lalu belum mengisi buku Ramadhan'"></p>
                                    </div>
                                    <a href="{{ \App\Filament\Siswa\Pages\Muslim\FormulirHarian::getUrl() }}" class="cal-alert-btn" style="text-decoration:none;">
                                        Isi Sekarang
                                    </a>
                                </div>
                            </template>

                            <div class="p-4 lg:p-5">
                                {{-- Weekday header --}}
                                <div class="cal-week-header">
                                    <template x-for="d in ['Sen','Sel','Rab','Kam','Jum','Sab','Min']">
                                        <div class="cal-week-label" x-text="d"></div>
                                    </template>
                                </div>
                                {{-- Calendar grid --}}
                                <div class="cal-grid">
                                    <template x-for="item in calendarDays" :key="item.key">
                                        <div class="cal-cell"
                                            :class="{
                                                'cal-cell-today':     item.isToday,
                                                'cal-cell-done':      item.isVerified && !item.isToday,
                                                'cal-cell-pending':   item.isPending && !item.isToday,
                                                'cal-cell-rejected':  item.isRejected && !item.isToday,
                                                'cal-cell-missed':    item.isPastUnfilled,
                                                'cal-cell-future':    !item.isToday && !item.isPast && item.hijriDay > 0,
                                                'cal-cell-empty':     item.hijriDay <= 0
                                            }">
                                            <template x-if="item.hijriDay > 0">
                                                <div class="cal-cell-inner">
                                                    {{-- Today ring --}}
                                                    <template x-if="item.isToday">
                                                        <span class="cal-today-label">Hari ini</span>
                                                    </template>
                                                    {{-- Verified checkmark --}}
                                                    <template x-if="item.isVerified && !item.isToday">
                                                        <div class="cal-check-icon">
                                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        </div>
                                                    </template>
                                                    {{-- Pending clock --}}
                                                    <template x-if="item.isPending && !item.isToday">
                                                        <div class="cal-check-icon" style="color: #854d0e;">
                                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </div>
                                                    </template>
                                                    {{-- Rejected X --}}
                                                    <template x-if="item.isRejected && !item.isToday">
                                                        <div class="cal-check-icon" style="color: #991b1b;">
                                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </div>
                                                    </template>
                                                    {{-- Missed warning --}}
                                                    <template x-if="item.isPastUnfilled">
                                                        <div class="cal-warn-icon">
                                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.007v.008H12v-.008zm9.303-3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.303-12.748c.866-1.5 3.032-1.5 3.898 0l7.303 12.748z"/></svg>
                                                        </div>
                                                    </template>
                                                    <span class="cal-day-num" x-text="item.masehiDay"></span>
                                                    <span class="cal-hijri-num" x-text="item.hijriDay + ' R'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                {{-- Legend --}}
                                <div class="cal-legend">
                                    <div class="cal-legend-item">
                                        <div class="cal-legend-dot cal-legend-dot-today"></div>
                                        <span class="cal-legend-text">Hari ini</span>
                                    </div>
                                    <div class="cal-legend-item">
                                        <div class="cal-legend-dot cal-legend-dot-done"></div>
                                        <span class="cal-legend-text">Diverifikasi</span>
                                    </div>
                                    <div class="cal-legend-item">
                                        <div class="cal-legend-dot cal-legend-dot-pending"></div>
                                        <span class="cal-legend-text">Menunggu Verifikasi</span>
                                    </div>
                                    <div class="cal-legend-item">
                                        <div class="cal-legend-dot cal-legend-dot-rejected"></div>
                                        <span class="cal-legend-text">Ditolak</span>
                                    </div>
                                    <div class="cal-legend-item">
                                        <div class="cal-legend-dot cal-legend-dot-missed"></div>
                                        <span class="cal-legend-text">Belum diisi</span>
                                    </div>
                                    <div class="cal-legend-item">
                                        <div class="cal-legend-dot cal-legend-dot-future"></div>
                                        <span class="cal-legend-text">Akan datang</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Progress Ramadhan --}}
                        <div class="progress-card">
                            <div class="progress-header">
                                <div class="progress-header-left">
                                    <div class="progress-icon-wrap">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="progress-title">Progress Ramadhan</h4>
                                        <p class="progress-subtitle" x-text="submittedDays.length + ' dari 30 hari terisi'"></p>
                                    </div>
                                </div>
                                <div class="progress-percent" x-text="getProgressPercent() + '%'"></div>
                            </div>
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" :style="'width:'+getProgressPercent()+'%'"></div>
                            </div>
                            <div class="progress-stats">
                                <div class="progress-stat">
                                    <span class="progress-stat-num" x-text="submittedDays.length"></span>
                                    <span class="progress-stat-label">Terisi</span>
                                </div>
                                <div class="progress-stat">
                                    <span class="progress-stat-num" x-text="30 - submittedDays.length"></span>
                                    <span class="progress-stat-label">Tersisa</span>
                                </div>
                                <div class="progress-stat">
                                    <span class="progress-stat-num" x-text="ramadhanDay"></span>
                                    <span class="progress-stat-label">Hari ke</span>
                                </div>
                                <div class="progress-stat">
                                    <span class="progress-stat-num" x-text="calendarDays.filter(d => d.isPastUnfilled).length"></span>
                                    <span class="progress-stat-label">Belum isi</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ARAH KIBLAT --}}
                    <div x-show="activeTab === 'qibla'" x-transition.opacity.duration.200ms
                         x-init="$watch('activeTab', v => { if(v==='qibla' && compassPermission==='granted' && !compassActive) { if('ondeviceorientationabsolute' in window) _startAbsoluteCompassListener(); else _startCompassListener(); } if(v!=='qibla') stopCompass(); })">
                        <div class="qibla-card">
                            {{-- Header --}}
                            <div class="qibla-header">
                                <div class="qibla-header-title">
                                    <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5.5-2.5l7.51-3.49L17.5 6.5 9.99 9.99 6.5 17.5zm5.5-6.6c.61 0 1.1.49 1.1 1.1s-.49 1.1-1.1 1.1-1.1-.49-1.1-1.1.49-1.1 1.1-1.1z"/></svg>
                                    <h3>Arah Kiblat</h3>
                                </div>
                                <p class="qibla-header-sub">Kompas digital &bull; GPS &bull; Sensor perangkat</p>
                            </div>

                            {{-- Location bar --}}
                            <div class="qibla-location-bar">
                                <div class="qibla-loc-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                </div>
                                <div class="qibla-loc-details">
                                    <div class="qibla-loc-city" x-text="locationCity"></div>
                                    <div class="qibla-loc-coords" x-text="locationCoords || 'Mendeteksi...'"></div>
                                    <div class="qibla-loc-gps">
                                        <span class="dot" :style="'background:' + gpsQualityColor"></span>
                                        <span :style="'color:' + gpsQualityColor" x-text="gpsQualityLabel + (gpsAccuracy ? ' (\u00b1' + gpsAccuracy + 'm)' : '')"></span>
                                    </div>
                                </div>
                                <button class="qibla-refresh-btn" @click="getLocation()" title="Perbarui lokasi">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                                </button>
                            </div>

                            {{-- Compass --}}
                            <div class="qibla-compass-wrap">
                                <div class="qibla-compass-container">
                                    {{-- Dial (rotates with device heading) --}}
                                    <div class="qibla-compass-dial" :style="'transform: rotate(' + compassRotation + 'deg)'">
                                        {{-- Inner face with pre-rendered compass rose --}}
                                        <div class="qibla-compass-face">
                                            <svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">
                                                {{-- Outer ring --}}
                                                <circle cx="150" cy="150" r="140" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                                                <circle cx="150" cy="150" r="130" fill="none" stroke="#f1f5f9" stroke-width="0.5"/>

                                                {{-- Degree ticks: every 5° (72 ticks total) --}}
                                                @for($i = 0; $i < 360; $i += 5)
                                                    @php
                                                        $rad = deg2rad($i);
                                                        $sinI = sin($rad);
                                                        $cosI = cos($rad);
                                                        $isMajor = ($i % 30 === 0);
                                                        $isMid = ($i % 10 === 0 && !$isMajor);
                                                        $outerR = 140;
                                                        $innerR = $isMajor ? 124 : ($isMid ? 128 : 133);
                                                        $strokeW = $isMajor ? 2 : ($isMid ? 1.2 : 0.6);
                                                        $color = ($i === 0) ? '#dc2626' : ($isMajor ? '#475569' : ($isMid ? '#94a3b8' : '#cbd5e1'));
                                                    @endphp
                                                    <line x1="{{ 150 + $outerR * $sinI }}" y1="{{ 150 - $outerR * $cosI }}"
                                                          x2="{{ 150 + $innerR * $sinI }}" y2="{{ 150 - $innerR * $cosI }}"
                                                          stroke="{{ $color }}" stroke-width="{{ $strokeW }}" stroke-linecap="round"/>
                                                @endfor

                                                {{-- Degree numbers every 30° --}}
                                                @foreach([30,60,90,120,150,180,210,240,270,300,330] as $deg)
                                                    @php
                                                        $rad = deg2rad($deg);
                                                        $tx = 150 + 113 * sin($rad);
                                                        $ty = 150 - 113 * cos($rad) + 4;
                                                    @endphp
                                                    <text x="{{ $tx }}" y="{{ $ty }}" text-anchor="middle" fill="#94a3b8" font-size="10" font-weight="600" font-family="system-ui, sans-serif">{{ $deg }}</text>
                                                @endforeach

                                                {{-- Inner circle rings --}}
                                                <circle cx="150" cy="150" r="96" fill="none" stroke="#f1f5f9" stroke-width="0.5"/>
                                                <circle cx="150" cy="150" r="60" fill="none" stroke="#f1f5f9" stroke-width="0.5" stroke-dasharray="4,4"/>

                                                {{-- Compass rose / crosshair lines --}}
                                                <line x1="150" y1="56" x2="150" y2="96" stroke="#e2e8f0" stroke-width="0.8"/>
                                                <line x1="150" y1="204" x2="150" y2="244" stroke="#e2e8f0" stroke-width="0.8"/>
                                                <line x1="56" y1="150" x2="96" y2="150" stroke="#e2e8f0" stroke-width="0.8"/>
                                                <line x1="204" y1="150" x2="244" y2="150" stroke="#e2e8f0" stroke-width="0.8"/>

                                                {{-- Diagonal crosshair lines --}}
                                                @foreach([45, 135, 225, 315] as $deg)
                                                    @php $r = deg2rad($deg); @endphp
                                                    <line x1="{{ 150 + 60 * sin($r) }}" y1="{{ 150 - 60 * cos($r) }}"
                                                          x2="{{ 150 + 96 * sin($r) }}" y2="{{ 150 - 96 * cos($r) }}"
                                                          stroke="#f1f5f9" stroke-width="0.5"/>
                                                @endforeach

                                                {{-- North arrow (red diamond) --}}
                                                <polygon points="150,54 145,70 150,64 155,70" fill="#dc2626" opacity="0.9"/>

                                                {{-- Cardinal labels --}}
                                                <text x="150" y="50" text-anchor="middle" fill="#dc2626" font-size="16" font-weight="800" font-family="system-ui, sans-serif">U</text>
                                                <text x="150" y="260" text-anchor="middle" fill="#94a3b8" font-size="14" font-weight="700" font-family="system-ui, sans-serif">S</text>
                                                <text x="258" y="155" text-anchor="middle" fill="#94a3b8" font-size="14" font-weight="700" font-family="system-ui, sans-serif">T</text>
                                                <text x="42" y="155" text-anchor="middle" fill="#94a3b8" font-size="14" font-weight="700" font-family="system-ui, sans-serif">B</text>

                                                {{-- Intercardinal labels --}}
                                                <text x="{{ 150 + 100 * sin(deg2rad(45)) }}" y="{{ 150 - 100 * cos(deg2rad(45)) + 3 }}" text-anchor="middle" fill="#cbd5e1" font-size="9" font-weight="600" font-family="system-ui, sans-serif">TL</text>
                                                <text x="{{ 150 + 100 * sin(deg2rad(135)) }}" y="{{ 150 - 100 * cos(deg2rad(135)) + 3 }}" text-anchor="middle" fill="#cbd5e1" font-size="9" font-weight="600" font-family="system-ui, sans-serif">TG</text>
                                                <text x="{{ 150 + 100 * sin(deg2rad(225)) }}" y="{{ 150 - 100 * cos(deg2rad(225)) + 3 }}" text-anchor="middle" fill="#cbd5e1" font-size="9" font-weight="600" font-family="system-ui, sans-serif">BD</text>
                                                <text x="{{ 150 + 100 * sin(deg2rad(315)) }}" y="{{ 150 - 100 * cos(deg2rad(315)) + 3 }}" text-anchor="middle" fill="#cbd5e1" font-size="9" font-weight="600" font-family="system-ui, sans-serif">BL</text>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Kaaba pointer (rotates to show qibla direction) --}}
                                    <div class="qibla-kaaba-pointer" :style="'transform: rotate(' + qiblaOnCompass + 'deg)'">
                                        <div class="qibla-kaaba-marker">
                                            <div class="qibla-kaaba-icon">
                                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.18L18.5 7.5 12 10.82 5.5 7.5 12 4.18zM5 8.82l6 3.33v7.53l-6-3.33V8.82zm8 10.86V12.15l6-3.33v7.53l-6 3.33z"/></svg>
                                            </div>
                                            <div class="qibla-kaaba-line"></div>
                                        </div>
                                    </div>

                                    {{-- Center dot --}}
                                    <div class="qibla-compass-center"></div>

                                    {{-- Fixed north indicator (triangle at top of container) --}}
                                    <div class="qibla-north-indicator">
                                        <svg width="18" height="14" viewBox="0 0 18 14">
                                            <polygon points="9,0 1,14 17,14" fill="#dc2626"/>
                                            <polygon points="9,3 4,14 14,14" fill="#ef4444"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Heading display --}}
                                <div class="qibla-heading-display">
                                    <div class="qibla-heading-degrees" x-show="compassActive">
                                        <span x-text="Math.round(compassHeading)"></span><span>&deg;</span>
                                        <span x-text="compassCardinal" style="margin-left:6px;font-size:18px;color:#64748b;font-weight:600;"></span>
                                    </div>
                                    <div class="qibla-heading-degrees" x-show="!compassActive">
                                        <span x-text="qiblaDirection.toFixed(1)"></span><span>&deg;</span>
                                    </div>
                                    <div class="qibla-heading-cardinal" x-text="compassActive ? 'Arah perangkat Anda saat ini' : 'Arah kiblat dari lokasi Anda'"></div>
                                </div>

                                {{-- Compass status --}}
                                <div class="qibla-compass-status">
                                    <template x-if="compassActive">
                                        <span class="active-dot"></span>
                                    </template>
                                    <template x-if="!compassActive">
                                        <span class="inactive-dot"></span>
                                    </template>
                                    <span x-text="compassActive ? 'Kompas aktif \u2014 arahkan HP ke kiblat' : (compassSupported ? 'Kompas tidak aktif' : 'Sensor tidak tersedia')"></span>
                                </div>
                            </div>

                            {{-- Info grid --}}
                            <div class="qibla-info-grid">
                                <div class="qibla-info-item">
                                    <div class="qibla-info-item-icon qibla-color">
                                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-5.5 15.5l7.51-3.49L17.5 6.5 9.99 9.99 6.5 17.5zm5.5-6.6c.61 0 1.1.49 1.1 1.1s-.49 1.1-1.1 1.1-1.1-.49-1.1-1.1.49-1.1 1.1-1.1z"/></svg>
                                    </div>
                                    <div class="qibla-info-value"><span x-text="qiblaDirection.toFixed(1) + '\u00b0'"></span></div>
                                    <div class="qibla-info-label" x-text="'Arah Kiblat (' + qiblaCardinal + ')'"></div>
                                </div>
                                <div class="qibla-info-item">
                                    <div class="qibla-info-item-icon distance-color">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/></svg>
                                    </div>
                                    <div class="qibla-info-value"><span x-text="distanceToKaaba.toLocaleString()"></span> <span>km</span></div>
                                    <div class="qibla-info-label">Jarak ke Ka'bah</div>
                                </div>
                            </div>

                            {{-- Permission request for iOS --}}
                            <div class="qibla-permission-box" x-show="compassPermission === 'unknown'" x-transition>
                                <p>Untuk mengaktifkan kompas digital, izinkan akses sensor orientasi perangkat Anda.</p>
                                <button class="qibla-permission-btn" @click="requestCompassPermission()">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                    Aktifkan Kompas
                                </button>
                            </div>

                            {{-- Denied message --}}
                            <div class="qibla-permission-box" x-show="compassPermission === 'denied'" x-transition>
                                <p>Izin sensor ditolak. Kompas menampilkan arah kiblat statis berdasarkan GPS Anda.</p>
                            </div>

                            {{-- Static fallback note --}}
                            <div class="qibla-static-note" x-show="!compassActive && compassPermission !== 'unknown'">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                                <p>Buka di <strong>HP/tablet</strong> untuk kompas real-time. Penanda <strong style="color:#d97706;">&#9632; Ka'bah</strong> menunjuk arah kiblat. Putar badan sampai penanda di atas = Anda menghadap kiblat.</p>
                            </div>
                        </div>
                    </div>

                    {{-- JADWAL SHOLAT --}}
                    <div x-show="activeTab === 'schedule'" x-transition.opacity.duration.200ms>
                        <div class="jadwal-card">
                            {{-- Header --}}
                            <div class="jadwal-header">
                                <div class="jadwal-header-bg"></div>
                                <div class="jadwal-header-inner">
                                    <div>
                                        <div class="jadwal-header-title-row">
                                            <svg class="jadwal-header-sun" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3a1 1 0 01.993.883L13 4v1a1 1 0 01-1.993.117L11 5V4a1 1 0 011-1zM6.343 6.343a1 1 0 011.32-.083l.094.083.707.707a1 1 0 01-1.32 1.497l-.094-.083-.707-.707a1 1 0 010-1.414zm11.314 0a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM12 7a5 5 0 110 10 5 5 0 010-10zm-9 4a1 1 0 01.117 1.993L3 13H2a1 1 0 01-.117-1.993L2 11h1zm19 0a1 1 0 01.117 1.993L22 13h-1a1 1 0 01-.117-1.993L21 11h1zM7.757 16.243a1 1 0 011.414 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707zm8.486 0l.707.707a1 1 0 01-1.414 1.414l-.707-.707a1 1 0 011.414-1.414zM12 19a1 1 0 01.993.883L13 20v1a1 1 0 01-1.993.117L11 21v-1a1 1 0 011-1z"/></svg>
                                            <h3 class="jadwal-header-title">Jadwal Sholat Hari Ini</h3>
                                        </div>
                                        <p class="jadwal-header-date" x-text="gregorianDate"></p>
                                    </div>
                                    <div>
                                        <span class="jadwal-city-badge" x-text="cityName"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Prayer list --}}
                            <div class="jadwal-body">
                                <template x-for="(prayer, index) in fullPrayerSchedule" :key="prayer.name">
                                    <div class="jadwal-row" :class="{ 'jadwal-row-active': prayer.isActive }">
                                        {{-- Left icon --}}
                                        <div class="jadwal-icon" :class="prayer.isActive ? 'jadwal-icon-active' : ''">
                                            <template x-if="prayer.name === 'Imsak'">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                            </template>
                                            <template x-if="prayer.name === 'Subuh'">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                            </template>
                                            <template x-if="prayer.name === 'Terbit'">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                                            </template>
                                            <template x-if="prayer.name === 'Dhuha'">
                                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zm11.394-5.834a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/></svg>
                                            </template>
                                            <template x-if="prayer.name === 'Dzuhur'">
                                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zm11.394-5.834a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/></svg>
                                            </template>
                                            <template x-if="prayer.name === 'Ashar'">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                                            </template>
                                            <template x-if="prayer.name === 'Maghrib'">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/></svg>
                                            </template>
                                            <template x-if="prayer.name === 'Isya'">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                                            </template>
                                        </div>

                                        {{-- Name & Arabic --}}
                                        <div class="jadwal-name-col">
                                            <p class="jadwal-name" x-text="prayer.name"></p>
                                            <p class="jadwal-arabic" x-text="prayer.arabic"></p>
                                        </div>

                                        {{-- Time & Badge --}}
                                        <div class="jadwal-time-col">
                                            <p class="jadwal-time" x-text="prayer.time"></p>
                                            <template x-if="prayer.isActive">
                                                <span class="jadwal-badge-active">Selanjutnya</span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Footer --}}
                            <div class="jadwal-footer">
                                <div class="jadwal-footer-content">
                                    <svg class="jadwal-footer-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    <span>Sumber: Kemenag RI via Aladhan API &bull;</span>
                                    <span x-text="cityName"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Imsak & Berbuka cards --}}
                        <div class="highlight-row">
                            <div class="highlight-card highlight-card-imsak">
                                <div class="highlight-card-orb"></div>
                                <div class="highlight-card-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                </div>
                                <p class="highlight-card-label">Imsak</p>
                                <p class="highlight-card-time" x-text="imsakTime"></p>
                                <p class="highlight-card-sub">Batas sahur</p>
                            </div>
                            <div class="highlight-card highlight-card-berbuka">
                                <div class="highlight-card-orb"></div>
                                <div class="highlight-card-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                                </div>
                                <p class="highlight-card-label">Berbuka</p>
                                <p class="highlight-card-time" x-text="maghribTime"></p>
                                <p class="highlight-card-sub">Waktu maghrib</p>
                            </div>
                        </div>
                    </div>

                    {{-- DOA HARIAN --}}
                    <div x-show="activeTab === 'dua'" x-transition.opacity.duration.200ms>
                        <div class="doa-card">
                            {{-- Header --}}
                            <div class="doa-header">
                                <div class="doa-header-bg"></div>
                                <div class="doa-header-inner">
                                    <div class="doa-header-title-row">
                                        <div class="doa-header-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="doa-header-title-text">Koleksi Doa</h3>
                                            <p class="doa-header-sub">
                                                <span x-text="filteredDuas.length"></span> doa dari Al-Quran, Hadits & Ulama
                                            </p>
                                        </div>
                                    </div>
                                    <div class="doa-header-badge">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
                                            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/>
                                        </svg>
                                        <span x-text="allDuas.length"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Search Bar --}}
                            <div class="doa-search-bar">
                                <div class="doa-search-input-wrap">
                                    <svg class="doa-search-icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                                    </svg>
                                    <input type="text"
                                           x-model="doaSearch"
                                           @input.debounce.200ms="filterDuas()"
                                           placeholder="Cari doa... (contoh: niat puasa, ampunan, tidur)"
                                           class="doa-search-input">
                                    <button x-show="doaSearch.length > 0"
                                            @click="doaSearch = ''; filterDuas()"
                                            class="doa-search-clear"
                                            x-transition.opacity>
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Category Filters --}}
                            <div class="doa-categories">
                                <template x-for="cat in doaCategories" :key="cat.id">
                                    <button @click="activeDoaCategory = cat.id; filterDuas()"
                                            :class="activeDoaCategory === cat.id ? 'doa-cat-active' : 'doa-cat-inactive'"
                                            class="doa-cat-btn">
                                        <span x-text="cat.label"></span>
                                        <span class="doa-cat-count" x-text="cat.count"></span>
                                    </button>
                                </template>
                            </div>

                            {{-- Doa List --}}
                            <div class="doa-list">
                                {{-- Loading state --}}
                                <div x-show="doasLoading" class="doa-loading">
                                    <div class="doa-loading-spinner"></div>
                                    <p>Memuat koleksi doa...</p>
                                </div>

                                {{-- Empty state --}}
                                <div x-show="!doasLoading && filteredDuas.length === 0" class="doa-empty">
                                    <svg class="doa-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                    </svg>
                                    <p class="doa-empty-title">Doa tidak ditemukan</p>
                                    <p class="doa-empty-sub">Coba kata kunci lain atau pilih kategori berbeda</p>
                                </div>

                                {{-- Doa items --}}
                                <template x-for="(dua, index) in paginatedDuas" :key="dua.id">
                                    <div class="doa-item" @click="toggleDoaExpand(dua.id)">
                                        {{-- Doa header row --}}
                                        <div class="doa-item-header">
                                            <div class="doa-item-number">
                                                <span x-text="(doaPage - 1) * doaPerPage + index + 1"></span>
                                            </div>
                                            <div class="doa-item-info">
                                                <h4 class="doa-item-title" x-text="dua.title"></h4>
                                                <div class="doa-item-meta">
                                                    <span class="doa-item-source" x-text="dua.source"></span>
                                                    <span class="doa-item-cat-tag" x-text="getCategoryLabel(dua.category)"></span>
                                                </div>
                                            </div>
                                            <div class="doa-item-toggle" :class="expandedDoas.includes(dua.id) ? 'doa-item-toggle-open' : ''">
                                                <svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>

                                        {{-- Doa content (expandable) --}}
                                        <div x-show="expandedDoas.includes(dua.id)"
                                             x-collapse
                                             class="doa-item-content">
                                            {{-- Arabic text --}}
                                            <div class="doa-arabic-wrap">
                                                <p class="doa-arabic" x-text="dua.arabic" dir="rtl"></p>
                                            </div>

                                            {{-- Latin transliteration --}}
                                            <div class="doa-latin-wrap">
                                                <span class="doa-label">Bacaan Latin</span>
                                                <p class="doa-latin" x-text="dua.latin"></p>
                                            </div>

                                            {{-- Translation --}}
                                            <div class="doa-translation-wrap">
                                                <span class="doa-label">Arti</span>
                                                <p class="doa-translation" x-text="dua.translation"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Pagination --}}
                            <div x-show="!doasLoading && doaTotalPages > 1" class="doa-pagination">
                                <button class="doa-page-btn" :disabled="doaPage <= 1" @click="doaPage--; paginateDuas()">
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
                                </button>
                                <template x-for="p in doaPageNumbers" :key="p">
                                    <button class="doa-page-num" :class="p === doaPage ? 'doa-page-active' : ''"
                                            @click="doaPage = p; paginateDuas()" x-text="p"></button>
                                </template>
                                <button class="doa-page-btn" :disabled="doaPage >= doaTotalPages" @click="doaPage++; paginateDuas()">
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
                                </button>
                                <span class="doa-page-info" x-text="'Hal ' + doaPage + ' dari ' + doaTotalPages"></span>
                            </div>

                            {{-- Footer info --}}
                            <div x-show="!doasLoading && filteredDuas.length > 0" class="doa-footer">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-blue-400">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                                </svg>
                                <span>Ketuk doa untuk membuka detail lengkap</span>
                            </div>
                        </div>
                    </div>

                    {{-- PENGATURAN AKUN --}}
                    <div x-show="activeTab === 'account'" x-transition.opacity.duration.200ms>
                        {{-- Profile Card --}}
                        <div class="akun-card">
                            <div class="akun-header">
                                <div class="akun-header-bg"></div>
                                <div class="akun-header-inner">
                                    <div class="akun-avatar">
                                        <span>{{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}</span>
                                    </div>
                                    <div class="akun-header-info">
                                        <h3 class="akun-name">{{ auth()->user()->name ?? 'Siswa' }}</h3>
                                        <p class="akun-email">{{ auth()->user()->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Data Diri Siswa --}}
                            <div class="akun-biodata">
                                <div class="akun-biodata-title">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm-3.375 3.375h3.75"/></svg>
                                    <span>Data Diri Siswa</span>
                                </div>

                                <div class="akun-bio-grid">
                                    <div class="akun-bio-item">
                                        <span class="akun-bio-label">Nama Lengkap</span>
                                        <span class="akun-bio-value">{{ auth()->user()->name ?? '-' }}</span>
                                    </div>
                                    <div class="akun-bio-item">
                                        <span class="akun-bio-label">NISN</span>
                                        <span class="akun-bio-value">{{ auth()->user()->nisn ?? '-' }}</span>
                                    </div>
                                    <div class="akun-bio-item">
                                        <span class="akun-bio-label">Email</span>
                                        <span class="akun-bio-value">{{ auth()->user()->email ?? '-' }}</span>
                                    </div>
                                    <div class="akun-bio-item">
                                        <span class="akun-bio-label">Agama</span>
                                        <span class="akun-bio-value">
                                            @if(auth()->user()->agama)
                                                <span class="akun-agama-badge {{ strtolower(auth()->user()->agama) === 'islam' ? 'akun-agama-islam' : 'akun-agama-other' }}">
                                                    {{ auth()->user()->agama }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 italic text-xs">Belum diisi</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Wali Kelas --}}
                            @php
                                $waliKelas = auth()->user()->kelas?->wali;
                            @endphp
                            @if($waliKelas)
                            <div class="akun-biodata akun-wali-section">
                                <div class="akun-biodata-title">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    <span>Wali Kelas</span>
                                </div>

                                <div class="akun-wali-card">
                                    <div class="akun-wali-avatar">
                                        <span>{{ strtoupper(substr($waliKelas->name ?? 'G', 0, 1)) }}</span>
                                    </div>
                                    <div class="akun-wali-info">
                                        <div class="akun-wali-name">{{ $waliKelas->name }}</div>
                                        <div class="akun-wali-kelas">Wali Kelas {{ auth()->user()->kelas->nama ?? '' }}</div>
                                    </div>
                                </div>

                                @if($waliKelas->no_hp)
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $waliKelas->no_hp)) }}" target="_blank" class="akun-wali-wa-btn">
                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px;flex-shrink:0;">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    <span>Hubungi via WhatsApp</span>
                                    <span class="akun-wali-wa-num">{{ $waliKelas->no_hp }}</span>
                                </a>
                                @else
                                <div class="akun-wali-no-contact">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:16px;height:16px;flex-shrink:0;color:#94a3b8;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                                    </svg>
                                    <span>Kontak belum tersedia</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- Menu Items --}}
                            <div class="akun-menu">
                                {{-- Ubah Password --}}
                                <button class="akun-menu-item" @click="showChangePassword = !showChangePassword">
                                    <div class="akun-menu-icon akun-menu-icon-amber">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="akun-menu-title">Ubah Password</p>
                                        <p class="akun-menu-sub">Perbarui kata sandi akun</p>
                                    </div>
                                    <svg class="akun-menu-chevron" :class="showChangePassword && 'akun-menu-chevron-open'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
                                </button>

                                {{-- Change password form (expandable) --}}
                                <div x-show="showChangePassword" x-collapse class="akun-pw-form">
                                    <div>
                                        <label class="akun-pw-label">Password Lama</label>
                                        <input type="password" x-model="pwOld" class="akun-pw-input" placeholder="Masukkan password lama">
                                    </div>
                                    <div>
                                        <label class="akun-pw-label">Password Baru</label>
                                        <input type="password" x-model="pwNew" class="akun-pw-input" placeholder="Masukkan password baru">
                                    </div>
                                    <div>
                                        <label class="akun-pw-label">Konfirmasi Password</label>
                                        <input type="password" x-model="pwConfirm" class="akun-pw-input" placeholder="Ulangi password baru">
                                    </div>
                                    <div x-show="pwMessage" x-transition class="akun-pw-message" :class="pwSuccess ? 'akun-pw-success' : 'akun-pw-error'" x-text="pwMessage"></div>
                                    <button class="akun-pw-btn" @click="changePassword()" :disabled="pwLoading" x-text="pwLoading ? 'Menyimpan...' : 'Simpan Password'"></button>
                                </div>

                                {{-- Tentang Aplikasi --}}
                                <div class="akun-menu-item">
                                    <div class="akun-menu-icon akun-menu-icon-blue">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="akun-menu-title">Tentang Aplikasi</p>
                                        <p class="akun-menu-sub">Buku Ramadhan v1.0 - SMKN 1 Ciamis</p>
                                    </div>
                                </div>

                                {{-- Logout --}}
                                <button type="button" @click="showLogoutConfirm = true" class="akun-menu-item akun-menu-logout w-full">
                                    <div class="akun-menu-icon akun-menu-icon-red">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="akun-menu-title" style="color: #dc2626;">Keluar</p>
                                        <p class="akun-menu-sub">Logout dari akun</p>
                                    </div>
                                </button>

                                {{-- Logout Confirmation Popup --}}
                                <div x-show="showLogoutConfirm" x-transition.opacity class="logout-overlay" @click.self="showLogoutConfirm = false" style="display:none;">
                                    <div class="logout-modal" x-show="showLogoutConfirm" x-transition.scale.90>
                                        <div class="logout-modal-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                        </div>
                                        <h3 class="logout-modal-title">Konfirmasi Logout</h3>
                                        <p class="logout-modal-text">Apakah Anda yakin ingin keluar dari akun?</p>
                                        <div class="logout-modal-actions">
                                            <button type="button" @click="showLogoutConfirm = false" class="logout-btn-cancel">Batal</button>
                                            <form method="POST" action="{{ route('filament.siswa.auth.logout') }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="logout-btn-confirm">Ya, Keluar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- AYAT / MOTIVASI KONTEKSTUAL --}}
                    <div class="verse-card">
                        <div class="verse-card-bg"></div>
                        <div class="verse-card-content">
                            <div class="verse-badge">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                <span x-text="dailyVerse.contextLabel || 'Ayat Hari Ini'"></span>
                            </div>
                            <div class="verse-arabic" x-show="dailyVerse.arabic" x-text="dailyVerse.arabic"></div>
                            <div class="verse-text" x-text="dailyVerse.text"></div>
                            <div class="verse-source" x-text="dailyVerse.source"></div>
                            <div class="verse-footer">
                                <button class="verse-refresh-btn" @click="fetchContextualVerse()" :disabled="dailyVerse.loading">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" :class="dailyVerse.loading && 'verse-spin'"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M2.985 19.644l3.182-3.182"/></svg>
                                    <span>Ayat Lain</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    @include('components.password-change-modal')
</x-filament-panels::page>

@push('styles')
    <link rel="stylesheet" href="{{ asset('themes/ramadhan/css/dashboard.css') }}?v={{ time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('themes/ramadhan/js/muslim/dashboard.js') }}?v={{ time() }}"></script>
@endpush
