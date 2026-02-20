<x-filament-panels::page>
    <div x-data="buddhaDashboard()" x-init="init()" class="ramadhan-app">
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

        {{-- ===== HERO HEADER ===== --}}
        <div class="hero-header">
            {{-- Buddhist-themed decorations --}}
            <div class="absolute inset-0 overflow-hidden pointer-events-none" style="border-radius: 0 0 2rem 2rem;">

                {{-- ★ Dharma wheel (Dharmachakra) top-right --}}
                <div class="islamic-deco islamic-deco-moon" style="top:6%; right:4%;">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Outer rim --}}
                        <circle cx="32" cy="32" r="28" stroke="white" stroke-width="1.2" opacity="0.12"/>
                        {{-- Inner rim --}}
                        <circle cx="32" cy="32" r="20" stroke="white" stroke-width="1" opacity="0.09"/>
                        {{-- Hub --}}
                        <circle cx="32" cy="32" r="6" fill="white" opacity="0.1"/>
                        <circle cx="32" cy="32" r="3" fill="white" opacity="0.15"/>
                        {{-- 8 spokes --}}
                        <line x1="32" y1="4" x2="32" y2="12" stroke="white" stroke-width="1" opacity="0.12"/>
                        <line x1="32" y1="52" x2="32" y2="60" stroke="white" stroke-width="1" opacity="0.12"/>
                        <line x1="4" y1="32" x2="12" y2="32" stroke="white" stroke-width="1" opacity="0.12"/>
                        <line x1="52" y1="32" x2="60" y2="32" stroke="white" stroke-width="1" opacity="0.12"/>
                        <line x1="12.2" y1="12.2" x2="17.9" y2="17.9" stroke="white" stroke-width="1" opacity="0.1"/>
                        <line x1="46.1" y1="46.1" x2="51.8" y2="51.8" stroke="white" stroke-width="1" opacity="0.1"/>
                        <line x1="51.8" y1="12.2" x2="46.1" y2="17.9" stroke="white" stroke-width="1" opacity="0.1"/>
                        <line x1="17.9" y1="46.1" x2="12.2" y2="51.8" stroke="white" stroke-width="1" opacity="0.1"/>
                        {{-- Inner spokes to hub --}}
                        <line x1="32" y1="12" x2="32" y2="26" stroke="white" stroke-width="0.8" opacity="0.08"/>
                        <line x1="32" y1="38" x2="32" y2="52" stroke="white" stroke-width="0.8" opacity="0.08"/>
                        <line x1="12" y1="32" x2="26" y2="32" stroke="white" stroke-width="0.8" opacity="0.08"/>
                        <line x1="38" y1="32" x2="52" y2="32" stroke="white" stroke-width="0.8" opacity="0.08"/>
                        <line x1="17.9" y1="17.9" x2="27.8" y2="27.8" stroke="white" stroke-width="0.8" opacity="0.07"/>
                        <line x1="36.2" y1="36.2" x2="46.1" y2="46.1" stroke="white" stroke-width="0.8" opacity="0.07"/>
                        <line x1="46.1" y1="17.9" x2="36.2" y2="27.8" stroke="white" stroke-width="0.8" opacity="0.07"/>
                        <line x1="27.8" y1="36.2" x2="17.9" y2="46.1" stroke="white" stroke-width="0.8" opacity="0.07"/>
                    </svg>
                </div>

                {{-- ★ Stars scattered --}}
                <div class="islamic-deco islamic-deco-star1" style="top:10%; right:20%;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white" opacity="0.4">
                        <polygon points="12,2 14.9,9.2 22.6,9.2 16.3,13.8 18.6,21 12,16.7 5.4,21 7.7,13.8 1.4,9.2 9.1,9.2"/>
                    </svg>
                </div>
                <div class="islamic-deco islamic-deco-star2" style="top:30%; right:10%;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="white" opacity="0.3">
                        <polygon points="12,2 14.9,9.2 22.6,9.2 16.3,13.8 18.6,21 12,16.7 5.4,21 7.7,13.8 1.4,9.2 9.1,9.2"/>
                    </svg>
                </div>
                <div class="islamic-deco islamic-deco-star3" style="top:16%; left:6%;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="white" opacity="0.25">
                        <polygon points="12,2 14.9,9.2 22.6,9.2 16.3,13.8 18.6,21 12,16.7 5.4,21 7.7,13.8 1.4,9.2 9.1,9.2"/>
                    </svg>
                </div>
                <div class="islamic-deco islamic-deco-twinkle" style="top:38%; left:15%; position:absolute;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="white" opacity="0.2">
                        <polygon points="12,2 14.9,9.2 22.6,9.2 16.3,13.8 18.6,21 12,16.7 5.4,21 7.7,13.8 1.4,9.2 9.1,9.2"/>
                    </svg>
                </div>
                <div style="position:absolute; top:22%; right:30%;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="white" opacity="0.18">
                        <polygon points="12,2 14.9,9.2 22.6,9.2 16.3,13.8 18.6,21 12,16.7 5.4,21 7.7,13.8 1.4,9.2 9.1,9.2"/>
                    </svg>
                </div>

                {{-- ★ Vihara (Buddhist temple) silhouette bottom-left --}}
                <div class="islamic-deco islamic-deco-mosque" style="bottom:0; left:0;">
                    <svg width="200" height="120" viewBox="0 0 260 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Central spire (pranala) --}}
                        <path d="M66 20 Q60 35 55 50 Q66 40 66 40 Q66 40 77 50 Q72 35 66 20Z" fill="white" opacity="0.1"/>
                        <path d="M66 5 Q63 12 60 20 Q66 15 66 15 Q66 15 72 20 Q69 12 66 5Z" fill="white" opacity="0.12"/>
                        {{-- Finial on top --}}
                        <circle cx="66" cy="4" r="3" fill="white" opacity="0.12"/>
                        <line x1="66" y1="7" x2="66" y2="15" stroke="white" stroke-width="1.5" opacity="0.1"/>
                        {{-- Multi-tiered roof (3 tiers) --}}
                        <path d="M35 70 L66 48 L97 70Z" fill="white" opacity="0.08"/>
                        <path d="M28 90 L66 65 L104 90Z" fill="white" opacity="0.07"/>
                        <path d="M20 110 L66 82 L112 110Z" fill="white" opacity="0.06"/>
                        {{-- Main temple body --}}
                        <rect x="25" y="110" width="82" height="40" fill="white" opacity="0.06"/>
                        {{-- Entrance arch --}}
                        <path d="M54 150 L54 120 Q66 112 78 120 L78 150Z" fill="white" opacity="0.08"/>
                        {{-- Side pillars --}}
                        <rect x="30" y="110" width="5" height="40" fill="white" opacity="0.05"/>
                        <rect x="97" y="110" width="5" height="40" fill="white" opacity="0.05"/>
                        {{-- Ornamental windows --}}
                        <circle cx="42" cy="125" r="6" stroke="white" stroke-width="0.8" fill="none" opacity="0.06"/>
                        <circle cx="90" cy="125" r="6" stroke="white" stroke-width="0.8" fill="none" opacity="0.06"/>
                        {{-- Side structure --}}
                        <rect x="120" y="100" width="45" height="50" fill="white" opacity="0.04"/>
                        <path d="M120 100 L142 80 L165 100Z" fill="white" opacity="0.05"/>
                        {{-- Stepping stones --}}
                        <rect x="200" y="140" width="30" height="10" rx="2" fill="white" opacity="0.03"/>
                        <rect x="220" y="135" width="25" height="15" rx="2" fill="white" opacity="0.03"/>
                    </svg>
                </div>

                {{-- ★ Stupa silhouette bottom-right --}}
                <div class="islamic-deco islamic-deco-mosque2" style="bottom:0; right:0;">
                    <svg width="180" height="110" viewBox="0 0 220 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Stupa finial (chattra/umbrella) --}}
                        <line x1="110" y1="-5" x2="110" y2="10" stroke="white" stroke-width="1.2" opacity="0.1"/>
                        <rect x="104" y="-5" width="12" height="3" rx="1" fill="white" opacity="0.1"/>
                        <rect x="106" y="-1" width="8" height="2" rx="1" fill="white" opacity="0.08"/>
                        {{-- Stupa dome (anda) --}}
                        <path d="M80 65 Q80 20 110 10 Q140 20 140 65Z" fill="white" opacity="0.07"/>
                        {{-- Harmika (square part above dome) --}}
                        <rect x="98" y="8" width="24" height="12" fill="white" opacity="0.08"/>
                        {{-- Base tiers --}}
                        <rect x="70" y="65" width="80" height="12" rx="2" fill="white" opacity="0.06"/>
                        <rect x="60" y="77" width="100" height="12" rx="2" fill="white" opacity="0.06"/>
                        <rect x="50" y="89" width="120" height="12" rx="2" fill="white" opacity="0.05"/>
                        {{-- Ground platform --}}
                        <rect x="40" y="101" width="140" height="29" fill="white" opacity="0.05"/>
                        {{-- Medhi (circular drum) detail --}}
                        <ellipse cx="110" cy="65" rx="32" ry="4" fill="white" opacity="0.04"/>
                        {{-- Trees beside stupa --}}
                        <path d="M25 100 L32 70 L39 100Z" fill="white" opacity="0.05"/>
                        <path d="M28 110 L32 80 L36 110Z" fill="white" opacity="0.04"/>
                        <rect x="30" y="100" width="4" height="30" fill="white" opacity="0.04"/>
                        <path d="M190 95 L198 68 L206 95Z" fill="white" opacity="0.05"/>
                        <rect x="196" y="95" width="4" height="35" fill="white" opacity="0.04"/>
                    </svg>
                </div>

                {{-- ★ Endless knot mandala pattern top-left --}}
                <div class="islamic-deco islamic-deco-geo" style="top:-20px; left:-20px;">
                    <svg width="120" height="120" viewBox="0 0 100 100" fill="none" opacity="0.06">
                        {{-- Outer circle --}}
                        <circle cx="50" cy="50" r="40" stroke="white" stroke-width="1.5" fill="none"/>
                        <circle cx="50" cy="50" r="32" stroke="white" stroke-width="0.8" fill="none"/>
                        {{-- Endless knot (simplified) --}}
                        <path d="M30 35 Q30 25 40 25 L60 25 Q70 25 70 35 L70 45 Q70 55 60 55 L40 55 Q30 55 30 45Z" stroke="white" stroke-width="2" fill="none"/>
                        <path d="M35 30 Q35 40 45 40 L55 40 Q65 40 65 50 L65 60 Q65 70 55 70 L45 70 Q35 70 35 60Z" stroke="white" stroke-width="2" fill="none"/>
                        {{-- Central intersection detail --}}
                        <circle cx="50" cy="47" r="6" stroke="white" stroke-width="1.5" fill="none"/>
                        {{-- Corner lotus dots --}}
                        <circle cx="30" cy="25" r="3" fill="white" opacity="0.8"/>
                        <circle cx="70" cy="25" r="3" fill="white" opacity="0.8"/>
                        <circle cx="30" cy="70" r="3" fill="white" opacity="0.8"/>
                        <circle cx="70" cy="70" r="3" fill="white" opacity="0.8"/>
                    </svg>
                </div>

                {{-- ★ Lotus flower floating left (instead of dove) --}}
                <div class="islamic-deco islamic-deco-lantern" style="top:8%; left:20%;">
                    <svg width="48" height="40" viewBox="0 0 60 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Center petal --}}
                        <path d="M30 8 Q26 18 30 28 Q34 18 30 8Z" fill="white" opacity="0.12"/>
                        {{-- Left petals --}}
                        <path d="M20 14 Q18 24 26 30 Q22 20 20 14Z" fill="white" opacity="0.1"/>
                        <path d="M12 20 Q12 30 22 34 Q16 26 12 20Z" fill="white" opacity="0.08"/>
                        {{-- Right petals --}}
                        <path d="M40 14 Q42 24 34 30 Q38 20 40 14Z" fill="white" opacity="0.1"/>
                        <path d="M48 20 Q48 30 38 34 Q44 26 48 20Z" fill="white" opacity="0.08"/>
                        {{-- Base (calyx) --}}
                        <path d="M22 30 Q26 36 30 34 Q34 36 38 30 Q36 38 30 40 Q24 38 22 30Z" fill="white" opacity="0.1"/>
                        {{-- Stem --}}
                        <path d="M30 38 Q28 44 30 48" stroke="white" stroke-width="0.8" fill="none" opacity="0.1"/>
                        {{-- Water ripples --}}
                        <ellipse cx="30" cy="46" rx="12" ry="2" stroke="white" stroke-width="0.5" fill="none" opacity="0.06"/>
                        <ellipse cx="30" cy="48" rx="16" ry="2" stroke="white" stroke-width="0.4" fill="none" opacity="0.04"/>
                    </svg>
                </div>

                {{-- ★ Meditation Buddha silhouette floating right-center (instead of bible) --}}
                <div style="position:absolute; top:35%; right:6%; animation: float-slow 6s ease-in-out infinite;">
                    <svg width="44" height="44" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Halo / aura --}}
                        <circle cx="28" cy="16" r="12" stroke="white" stroke-width="0.6" fill="none" opacity="0.06"/>
                        {{-- Head --}}
                        <circle cx="28" cy="16" r="6" fill="white" opacity="0.1"/>
                        {{-- Ushnisha (top knot) --}}
                        <circle cx="28" cy="11" r="2.5" fill="white" opacity="0.08"/>
                        {{-- Body (torso) --}}
                        <path d="M22 22 Q22 30 28 32 Q34 30 34 22Z" fill="white" opacity="0.08"/>
                        {{-- Crossed legs (lotus position) --}}
                        <path d="M18 34 Q22 30 28 34 Q34 30 38 34 Q36 40 28 42 Q20 40 18 34Z" fill="white" opacity="0.07"/>
                        {{-- Hands in lap (dhyana mudra) --}}
                        <ellipse cx="28" cy="32" rx="5" ry="3" fill="white" opacity="0.06"/>
                        {{-- Base/platform --}}
                        <path d="M16 42 Q22 44 28 44 Q34 44 40 42 L42 46 L14 46Z" fill="white" opacity="0.06"/>
                        {{-- Lotus base petals --}}
                        <path d="M14 46 Q18 44 22 46" stroke="white" stroke-width="0.5" fill="none" opacity="0.05"/>
                        <path d="M22 46 Q26 44 28 46" stroke="white" stroke-width="0.5" fill="none" opacity="0.05"/>
                        <path d="M28 46 Q30 44 34 46" stroke="white" stroke-width="0.5" fill="none" opacity="0.05"/>
                        <path d="M34 46 Q38 44 42 46" stroke="white" stroke-width="0.5" fill="none" opacity="0.05"/>
                    </svg>
                </div>
                </div>

                {{-- ★ Bodhi tree leaf floating (instead of candle) --}}
                <div style="position:absolute; bottom:30%; left:8%; animation: float-slow 5s ease-in-out infinite; animation-delay: 1.5s;">
                    <svg width="28" height="44" viewBox="0 0 34 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Bodhi leaf shape (heart-shaped with elongated tip) --}}
                        <path d="M17 4 Q6 14 6 26 Q6 36 12 42 Q15 45 17 50 Q19 45 22 42 Q28 36 28 26 Q28 14 17 4Z" fill="white" opacity="0.08" stroke="white" stroke-width="0.8" opacity="0.12"/>
                        {{-- Central vein --}}
                        <line x1="17" y1="10" x2="17" y2="48" stroke="white" stroke-width="0.6" opacity="0.08"/>
                        {{-- Side veins --}}
                        <path d="M17 18 Q12 20 9 24" stroke="white" stroke-width="0.4" fill="none" opacity="0.06"/>
                        <path d="M17 18 Q22 20 25 24" stroke="white" stroke-width="0.4" fill="none" opacity="0.06"/>
                        <path d="M17 26 Q12 28 9 32" stroke="white" stroke-width="0.4" fill="none" opacity="0.06"/>
                        <path d="M17 26 Q22 28 25 32" stroke="white" stroke-width="0.4" fill="none" opacity="0.06"/>
                        <path d="M17 34 Q13 36 11 38" stroke="white" stroke-width="0.4" fill="none" opacity="0.06"/>
                        <path d="M17 34 Q21 36 23 38" stroke="white" stroke-width="0.4" fill="none" opacity="0.06"/>
                        {{-- Glow around leaf --}}
                        <circle cx="17" cy="26" r="14" fill="white" opacity="0.03"/>
                    </svg>
                </div>

                {{-- ★ Small lotus accent (instead of music notes) --}}
                <div style="position:absolute; top:25%; left:35%; animation: float-slow 7s ease-in-out infinite; animation-delay: 2s;">
                    <svg width="28" height="28" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Simple lotus top view --}}
                        <path d="M18 6 Q15 12 18 18 Q21 12 18 6Z" fill="white" opacity="0.1"/>
                        <path d="M10 10 Q12 16 18 18 Q12 14 10 10Z" fill="white" opacity="0.08"/>
                        <path d="M26 10 Q24 16 18 18 Q24 14 26 10Z" fill="white" opacity="0.08"/>
                        <path d="M7 18 Q10 20 18 20 Q10 22 7 18Z" fill="white" opacity="0.06"/>
                        <path d="M29 18 Q26 20 18 20 Q26 22 29 18Z" fill="white" opacity="0.06"/>
                        {{-- Center --}}
                        <circle cx="18" cy="18" r="2" fill="white" opacity="0.12"/>
                    </svg>
                </div>

                {{-- ★ Small dharma wheel accent mid-right --}}
                <div style="position:absolute; top:45%; right:16%; animation: float-slow 5.5s ease-in-out infinite; animation-delay: 0.8s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="0.8" fill="none" opacity="0.1"/>
                        <circle cx="12" cy="12" r="2" fill="white" opacity="0.12"/>
                        <line x1="12" y1="2" x2="12" y2="22" stroke="white" stroke-width="0.6" opacity="0.08"/>
                        <line x1="2" y1="12" x2="22" y2="12" stroke="white" stroke-width="0.6" opacity="0.08"/>
                        <line x1="4.9" y1="4.9" x2="19.1" y2="19.1" stroke="white" stroke-width="0.5" opacity="0.06"/>
                        <line x1="19.1" y1="4.9" x2="4.9" y2="19.1" stroke="white" stroke-width="0.5" opacity="0.06"/>
                    </svg>
                </div>

                {{-- ★ Om/Dharma symbol (peace) --}}
                <div style="position:absolute; top:50%; left:28%; animation: float-slow 6.5s ease-in-out infinite; animation-delay: 3s;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white" opacity="0.08">
                        {{-- Simplified lotus bud --}}
                        <path d="M12 2 Q8 8 8 14 Q8 18 12 20 Q16 18 16 14 Q16 8 12 2Z"/>
                    </svg>
                </div>

                {{-- ★ Twinkle dots --}}
                <div class="absolute top-12 right-16 w-2 h-2 bg-white/20 rounded-full islamic-deco-twinkle" style="animation-delay:0.3s"></div>
                <div class="absolute top-24 right-32 w-1.5 h-1.5 bg-white/15 rounded-full islamic-deco-twinkle" style="animation-delay:1.1s"></div>
                <div class="absolute bottom-32 left-1/4 w-1 h-1 bg-white/20 rounded-full islamic-deco-twinkle" style="animation-delay:0.7s"></div>
                <div class="absolute top-1/3 left-1/3 w-1.5 h-1.5 bg-white/10 rounded-full islamic-deco-twinkle" style="animation-delay:1.8s"></div>
                <div class="absolute top-1/2 right-1/4 w-1 h-1 bg-white/15 rounded-full islamic-deco-twinkle" style="animation-delay:2.2s"></div>
                <div class="absolute bottom-20 right-1/3 w-1.5 h-1.5 bg-white/12 rounded-full islamic-deco-twinkle" style="animation-delay:2.8s"></div>
                <div class="absolute top-16 left-1/2 w-1 h-1 bg-white/18 rounded-full islamic-deco-twinkle" style="animation-delay:0.5s"></div>

            <div class="hero-inner">
                {{-- Top bar: logo + date --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/logo_smk.png') }}" alt="SMKN 1 Ciamis" class="w-10 h-10 lg:w-12 lg:h-12">
                        <div>
                            <h1 class="text-white font-bold text-sm lg:text-lg leading-tight">Buku Kegiatan Positif</h1>
                            <p class="text-blue-100 text-[11px] lg:text-xs">SMKN 1 Ciamis</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-white text-[11px] lg:text-sm font-medium" x-text="gregorianDate"></p>
                        <p class="text-blue-100 text-[10px] lg:text-xs" x-text="'Hari ke-' + ramadhanDay + ' dari 30'"></p>
                    </div>
                </div>

                {{-- Clock centered --}}
                <div class="text-center flex-1 flex flex-col items-center justify-center gap-2">
                    <p class="greeting-text" x-text="greeting"></p>
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
                        <span x-text="motivationalBadge"></span>
                    </div>
                </div>

                {{-- Uposatha / meditation day reminder --}}
                <template x-if="isSunday">
                    <div class="prayer-row-section">
                        <div style="display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.75rem 1rem; background:rgba(255,255,255,0.15); border-radius:0.75rem; backdrop-filter:blur(8px);">
                            <svg style="width:1.25rem;height:1.25rem;color:#fbbf24;flex-shrink:0;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                            <span style="color:#fff; font-size:0.75rem; font-weight:600;">Hari Minggu — Waktunya bermeditasi dan berbuat kebajikan 🙏</span>
                        </div>
                    </div>
                </template>
                <template x-if="!isSunday">
                    <div class="prayer-row-section">
                        <div style="display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.75rem 1rem; background:rgba(255,255,255,0.1); border-radius:0.75rem;">
                            <span style="color:rgba(255,255,255,0.7); font-size:0.7rem;">Semangat menjalani kegiatan positif hari ini!</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ===== BODY ===== --}}
        <div class="ramadhan-body">
            <div class="ramadhan-content">

                {{-- Centered menu bar --}}
                <div class="center-menu-wrap">
                    <div class="center-menu">
                        {{-- Kalender --}}
                        <button @click="activeTab = 'calendar'" class="center-menu-btn" :class="activeTab === 'calendar' && 'active'">
                            <div class="center-menu-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            </div>
                            <span class="center-menu-label">Kalender</span>
                        </button>

                        {{-- Formulir (link to separate page) --}}
                        <a href="{{ \App\Filament\Siswa\Pages\NonMuslim\Buddha\FormulirHarian::getUrl() }}" class="center-menu-btn" style="text-decoration:none;" target="_blank" rel="noopener noreferrer">
                            <div class="center-menu-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                            </div>
                            <span class="center-menu-label">Formulir</span>
                        </a>

                        {{-- Doa --}}
                        <button @click="activeTab = 'dua'" class="center-menu-btn" :class="activeTab === 'dua' && 'active'">
                            <div class="center-menu-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            </div>
                            <span class="center-menu-label">Doa</span>
                        </button>

                        {{-- Akun --}}
                        <button @click="activeTab = 'account'" class="center-menu-btn" :class="activeTab === 'account' && 'active'">
                            <div class="center-menu-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="center-menu-label">Akun</span>
                        </button>
                    </div>
                </div>

                {{-- Tab content --}}
                <div class="content-area">

                    {{-- KALENDER KEGIATAN --}}
                    <div x-show="activeTab === 'calendar'" x-transition.opacity.duration.200ms>
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="text-white font-bold text-sm lg:text-base">Kalender Kegiatan Positif</h3>
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
                                        <p class="cal-alert-sub" x-text="calendarDays.filter(d => d.isPastUnfilled).length + ' hari lalu belum mengisi formulir'"></p>
                                    </div>
                                    <a href="{{ \App\Filament\Siswa\Pages\NonMuslim\Buddha\FormulirHarian::getUrl() }}" class="cal-alert-btn" style="text-decoration:none;">
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
                                                'cal-cell-done':      item.isCompleted && !item.isToday,
                                                'cal-cell-missed':    item.isPastUnfilled,
                                                'cal-cell-future':    !item.isToday && !item.isPast && item.hijriDay > 0,
                                                'cal-cell-empty':     item.hijriDay <= 0
                                            }">
                                            <template x-if="item.hijriDay > 0">
                                                <div class="cal-cell-inner">
                                                    <template x-if="item.isToday">
                                                        <span class="cal-today-label">Hari ini</span>
                                                    </template>
                                                    <template x-if="item.isCompleted && !item.isToday">
                                                        <div class="cal-check-icon">
                                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        </div>
                                                    </template>
                                                    <template x-if="item.isPastUnfilled">
                                                        <div class="cal-warn-icon">
                                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.007v.008H12v-.008zm9.303-3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.303-12.748c.866-1.5 3.032-1.5 3.898 0l7.303 12.748z"/></svg>
                                                        </div>
                                                    </template>
                                                    <span class="cal-day-num" x-text="item.masehiDay"></span>
                                                    <span class="cal-hijri-num" x-text="'H' + item.hijriDay"></span>
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
                                        <span class="cal-legend-text">Sudah diisi</span>
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

                        {{-- Progress --}}
                        <div class="progress-card">
                            <div class="progress-header">
                                <div class="progress-header-left">
                                    <div class="progress-icon-wrap">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="progress-title">Progress Kegiatan</h4>
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

                    {{-- DOA & MEDITASI BUDDHA --}}
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
                                            <h3 class="doa-header-title-text">Doa & Meditasi Buddha</h3>
                                            <p class="doa-header-sub">
                                                <span x-text="filteredDuas.length"></span> doa Buddhis
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
                                           placeholder="Cari doa... (contoh: metta, meditasi, paritta)"
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
                                <div x-show="!doasLoading && filteredDuas.length === 0" class="doa-empty">
                                    <svg class="doa-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                    </svg>
                                    <p class="doa-empty-title">Doa tidak ditemukan</p>
                                    <p class="doa-empty-sub">Coba kata kunci lain atau pilih kategori berbeda</p>
                                </div>

                                <template x-for="(dua, index) in paginatedDuas" :key="dua.id">
                                    <div class="doa-item" @click="toggleDoaExpand(dua.id)">
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

                                        <div x-show="expandedDoas.includes(dua.id)"
                                             x-collapse
                                             class="doa-item-content">
                                            {{-- Prayer text --}}
                                            <div class="doa-translation-wrap">
                                                <span class="doa-label">Doa</span>
                                                <p class="doa-translation" x-text="dua.text" style="white-space:pre-line;"></p>
                                            </div>
                                            {{-- Scripture reference --}}
                                            <div x-show="dua.verse" class="doa-latin-wrap">
                                                <span class="doa-label">Sumber Kitab Suci</span>
                                                <p class="doa-latin" x-text="dua.verse" style="font-style:italic;"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Pagination --}}
                            <div x-show="doaTotalPages > 1" class="doa-pagination">
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

                            {{-- Footer --}}
                            <div x-show="filteredDuas.length > 0" class="doa-footer">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-blue-400">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                                </svg>
                                <span>Ketuk doa untuk membuka detail lengkap</span>
                            </div>
                        </div>
                    </div>

                    {{-- PENGATURAN AKUN --}}
                    <div x-show="activeTab === 'account'" x-transition.opacity.duration.200ms>
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
                                                <span class="akun-agama-badge akun-agama-other">
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

                            <div class="akun-menu">
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

                                <div x-show="showChangePassword" x-collapse class="akun-pw-form">
                                    <div>
                                        <label class="akun-pw-label">Password Lama</label>
                                        <input type="password" class="akun-pw-input" placeholder="Masukkan password lama">
                                    </div>
                                    <div>
                                        <label class="akun-pw-label">Password Baru</label>
                                        <input type="password" class="akun-pw-input" placeholder="Masukkan password baru">
                                    </div>
                                    <div>
                                        <label class="akun-pw-label">Konfirmasi Password</label>
                                        <input type="password" class="akun-pw-input" placeholder="Ulangi password baru">
                                    </div>
                                    <button class="akun-pw-btn">Simpan Password</button>
                                </div>

                                <div class="akun-menu-item">
                                    <div class="akun-menu-icon akun-menu-icon-blue">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="akun-menu-title">Tentang Aplikasi</p>
                                        <p class="akun-menu-sub">Buku Kegiatan Positif v1.0 - SMKN 1 Ciamis</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('filament.siswa.auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="akun-menu-item akun-menu-logout w-full">
                                        <div class="akun-menu-icon akun-menu-icon-red">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <p class="akun-menu-title" style="color: #dc2626;">Keluar</p>
                                            <p class="akun-menu-sub">Logout dari akun</p>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- DHAMMAPADA / MOTIVASI --}}
                    <div class="verse-card">
                        <div class="verse-card-bg"></div>
                        <div class="verse-card-content">
                            <div class="verse-badge">
                                {{-- Dharma wheel icon --}}
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <line x1="12" y1="3" x2="12" y2="9" stroke-linecap="round"/>
                                    <line x1="12" y1="15" x2="12" y2="21" stroke-linecap="round"/>
                                    <line x1="3" y1="12" x2="9" y2="12" stroke-linecap="round"/>
                                    <line x1="15" y1="12" x2="21" y2="12" stroke-linecap="round"/>
                                </svg>
                                <span>Dhammapada Hari Ini</span>
                            </div>
                            <div class="verse-text" x-text="dailyVerse.text"></div>
                            <div class="verse-source" x-text="dailyVerse.source"></div>
                            <div class="verse-footer">
                                <button class="verse-refresh-btn" @click="refreshVerse()">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M2.985 19.644l3.182-3.182"/></svg>
                                    <span>Ayat Lain</span>
                                </button>
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
    <script src="{{ asset('themes/ramadhan/js/nonmuslim/buddha/dashboard.js') }}?v={{ time() }}"></script>
@endpush
