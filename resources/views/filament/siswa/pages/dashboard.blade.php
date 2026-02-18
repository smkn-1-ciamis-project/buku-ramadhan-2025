<x-filament-panels::page>
    <div x-data="ramadhanDashboard()" x-init="init()" class="ramadhan-app">

        {{-- ===== HERO HEADER ===== --}}
        <div class="hero-header">
            {{-- Islamic Pattern --}}
            <div class="absolute inset-0 opacity-[0.07] pointer-events-none">
                <svg class="w-full h-full" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice">
                    <defs>
                        <pattern id="islamic-pattern" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse">
                            <path d="M25 0 L50 25 L25 50 L0 25Z" fill="none" stroke="white" stroke-width="0.5"/>
                            <circle cx="25" cy="25" r="8" fill="none" stroke="white" stroke-width="0.4"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#islamic-pattern)"/>
                </svg>
            </div>

            {{-- Moon & Stars --}}
            <div class="absolute top-6 right-8 opacity-20">
                <svg width="64" height="64" viewBox="0 0 100 100"><path d="M50 5C30 5 15 25 15 50c0 25 15 45 35 45-15-10-25-27-25-45S35 15 50 5z" fill="white"/></svg>
            </div>
            <div class="absolute top-10 right-24 w-1.5 h-1.5 bg-white/30 rounded-full"></div>
            <div class="absolute top-20 right-16 w-1 h-1 bg-white/20 rounded-full"></div>

            <div class="relative z-10 px-5 pt-5 pb-6">
                {{-- Top Bar --}}
                <div class="flex items-center gap-3 mb-5">
                    <img src="{{ asset('img/logo_smk.png') }}" alt="SMKN 1 Ciamis" class="w-11 h-11 rounded-full bg-white/20 p-0.5 ring-2 ring-white/20">
                    <div class="min-w-0 flex-1">
                        <h1 class="text-white font-bold text-base leading-tight truncate">Buku Ramadhan</h1>
                        <p class="text-blue-200 text-[11px]">SMKN 1 Ciamis</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-white/90 text-[11px] font-medium" x-text="hijriDate"></p>
                        <p class="text-blue-200/70 text-[10px]" x-text="gregorianDate"></p>
                    </div>
                </div>

                {{-- Current Prayer Big Display --}}
                <div class="text-center mb-6">
                    <p class="text-blue-200/80 text-xs font-medium uppercase tracking-widest mb-1" x-text="currentPrayerLabel"></p>
                    <p class="text-white text-[3.2rem] font-extrabold leading-none tracking-tight" x-text="currentPrayerTime"></p>
                    <div class="mt-3 inline-flex items-center gap-1.5 bg-white/10 backdrop-blur rounded-full px-4 py-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.828a1 1 0 101.415-1.414L11 9.586V6z" clip-rule="evenodd"/></svg>
                        <span class="text-amber-200 text-xs font-medium" x-text="countdown"></span>
                    </div>
                </div>

                {{-- Prayer Time Row --}}
                <div class="prayer-row">
                    <template x-for="prayer in prayerTimes" :key="prayer.name">
                        <div class="prayer-slot" :class="prayer.isActive && 'active'">
                            <span class="prayer-slot-name" x-text="prayer.name"></span>
                            <span class="prayer-slot-time" x-text="prayer.time"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ===== QUICK MENU ===== --}}
        <div class="px-5 -mt-1">
            <div class="flex justify-between gap-2">
                <template x-for="tab in [
                    { id: 'calendar', icon: 'calendar', label: 'Kalender' },
                    { id: 'qibla', icon: 'compass', label: 'Kiblat' },
                    { id: 'schedule', icon: 'clock', label: 'Jadwal' },
                    { id: 'dua', icon: 'book', label: 'Doa' }
                ]" :key="tab.id">
                    <button @click="activeTab = tab.id" class="menu-btn" :class="activeTab === tab.id && 'active'">
                        <svg x-show="tab.icon === 'calendar'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                        <svg x-show="tab.icon === 'compass'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/>
                        </svg>
                        <svg x-show="tab.icon === 'clock'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg x-show="tab.icon === 'book'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        <span class="text-[10px] font-semibold mt-0.5" x-text="tab.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- ===== CONTENT ===== --}}
        <div class="px-5 pt-4 pb-8 space-y-4">

            {{-- KALENDER RAMADHAN --}}
            <div x-show="activeTab === 'calendar'" x-transition.opacity.duration.200ms>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="text-white font-bold text-sm">📅 Kalender Ramadhan 1446 H</h3>
                            <p class="text-blue-200/70 text-[11px] mt-0.5">Maret - April 2025</p>
                        </div>
                        <span class="bg-white/20 text-white text-[10px] font-bold px-2.5 py-1 rounded-md" x-text="'Hari ke-' + ramadhanDay"></span>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-7 gap-0.5 mb-1.5">
                            <template x-for="d in ['Sen','Sel','Rab','Kam','Jum','Sab','Min']">
                                <div class="text-center text-[10px] font-bold text-gray-400 uppercase py-1" x-text="d"></div>
                            </template>
                        </div>
                        <div class="grid grid-cols-7 gap-0.5">
                            <template x-for="item in calendarDays" :key="item.key">
                                <div class="aspect-square flex items-center justify-center rounded-lg text-xs font-semibold transition-all"
                                    :class="{
                                        'bg-blue-600 text-white shadow-md shadow-blue-600/30 ring-2 ring-blue-400/50': item.isToday,
                                        'bg-emerald-50 text-emerald-600': item.isCompleted && !item.isToday,
                                        'text-gray-600 hover:bg-gray-50': !item.isToday && !item.isCompleted && item.day > 0,
                                        'text-transparent pointer-events-none': item.day <= 0
                                    }">
                                    <span x-text="item.day > 0 ? item.day : ''"></span>
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center justify-center gap-5 mt-4 pt-3 border-t border-gray-100">
                            <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div><span class="text-[10px] text-gray-400">Hari ini</span></div>
                            <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div><span class="text-[10px] text-gray-400">Sudah lewat</span></div>
                            <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-gray-200"></div><span class="text-[10px] text-gray-400">Akan datang</span></div>
                        </div>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="card mt-3 p-4">
                    <div class="flex items-center justify-between mb-2.5">
                        <h4 class="text-xs font-bold text-gray-600">Progress Ramadhan</h4>
                        <span class="text-blue-600 font-bold text-xs" x-text="Math.round((ramadhanDay/30)*100) + '%'"></span>
                    </div>
                    <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-blue-700 transition-all duration-1000" :style="'width:'+Math.round((ramadhanDay/30)*100)+'%'"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1.5" x-text="'Hari ke-' + ramadhanDay + ' dari 30 hari'"></p>
                </div>
            </div>

            {{-- ARAH KIBLAT --}}
            <div x-show="activeTab === 'qibla'" x-transition.opacity.duration.200ms>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="text-white font-bold text-sm">🧭 Arah Kiblat</h3>
                            <p class="text-blue-200/70 text-[11px] mt-0.5">Menggunakan GPS perangkat Anda</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-center mb-5">
                            <div class="relative w-56 h-56">
                                <div class="w-full h-full rounded-full border-[3px] border-blue-100 bg-gradient-to-b from-slate-50 to-white shadow-[inset_0_2px_8px_rgba(0,0,0,0.06)] relative">
                                    <span class="absolute top-2 left-1/2 -translate-x-1/2 text-xs font-bold text-red-500">U</span>
                                    <span class="absolute bottom-2 left-1/2 -translate-x-1/2 text-xs font-bold text-gray-300">S</span>
                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-300">B</span>
                                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-300">T</span>
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
                                    <div class="absolute inset-0 flex items-center justify-center transition-transform duration-700 ease-out" :style="'transform:rotate('+qiblaDirection+'deg)'">
                                        <div class="absolute -top-0.5"><span class="text-xl">🕋</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-4">
                            <p class="text-2xl font-extrabold text-blue-700" x-text="qiblaDirection.toFixed(1) + '°'"></p>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="qiblaStatus"></p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3.5">
                            <div class="flex items-center gap-2 mb-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                <span class="text-xs font-semibold text-blue-700">Lokasi Anda</span>
                            </div>
                            <p class="text-[11px] text-blue-600/80" x-text="locationText"></p>
                            <button @click="getLocation()" class="mt-2 text-[10px] font-semibold bg-blue-600 text-white px-3.5 py-1.5 rounded-lg hover:bg-blue-700 active:scale-95 transition-all">
                                🔄 Perbarui Lokasi
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
                            <h3 class="text-white font-bold text-sm">🕌 Jadwal Sholat Hari Ini</h3>
                            <p class="text-blue-200/70 text-[11px] mt-0.5" x-text="gregorianDate"></p>
                        </div>
                        <span class="bg-white/20 text-white text-[10px] font-medium px-2.5 py-1 rounded-md" x-text="cityName"></span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <template x-for="prayer in fullPrayerSchedule" :key="prayer.name">
                            <div class="flex items-center px-4 py-3 transition-colors" :class="prayer.isActive ? 'bg-blue-50/80' : ''">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center mr-3.5 text-base"
                                    :class="prayer.isActive ? 'bg-blue-600 shadow-md shadow-blue-600/20' : 'bg-gray-50'">
                                    <span x-text="prayer.icon"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold" :class="prayer.isActive ? 'text-blue-700' : 'text-gray-700'" x-text="prayer.name"></p>
                                    <p class="text-[10px] text-gray-400" x-text="prayer.arabic"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold tabular-nums" :class="prayer.isActive ? 'text-blue-700' : 'text-gray-600'" x-text="prayer.time"></p>
                                    <template x-if="prayer.isActive">
                                        <span class="inline-block text-[9px] bg-blue-600 text-white px-1.5 py-0.5 rounded font-semibold mt-0.5">NOW</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div class="rounded-2xl p-4 text-white relative overflow-hidden" style="background:linear-gradient(135deg,#0f2852,#2563eb);">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-white/5 rounded-full -translate-y-4 translate-x-4"></div>
                        <span class="text-xl block mb-1">🌙</span>
                        <p class="text-blue-200/70 text-[10px] font-medium uppercase tracking-wider">Imsak</p>
                        <p class="text-2xl font-extrabold mt-0.5" x-text="imsakTime"></p>
                    </div>
                    <div class="rounded-2xl p-4 text-white relative overflow-hidden" style="background:linear-gradient(135deg,#d97706,#ea580c);">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-white/5 rounded-full -translate-y-4 translate-x-4"></div>
                        <span class="text-xl block mb-1">🌅</span>
                        <p class="text-amber-100/70 text-[10px] font-medium uppercase tracking-wider">Berbuka</p>
                        <p class="text-2xl font-extrabold mt-0.5" x-text="maghribTime"></p>
                    </div>
                </div>
            </div>

            {{-- DOA HARIAN --}}
            <div x-show="activeTab === 'dua'" x-transition.opacity.duration.200ms>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="text-white font-bold text-sm">📖 Doa Harian Ramadhan</h3>
                            <p class="text-blue-200/70 text-[11px] mt-0.5">Doa-doa penting di bulan Ramadhan</p>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <template x-for="dua in duas" :key="dua.title">
                            <div class="px-4 py-4">
                                <div class="flex items-center gap-2 mb-2.5">
                                    <span class="text-base" x-text="dua.icon"></span>
                                    <h4 class="font-bold text-xs text-gray-700 uppercase tracking-wide" x-text="dua.title"></h4>
                                </div>
                                <p class="text-right text-lg leading-[2.2] text-gray-800 font-arabic mb-2" x-text="dua.arabic" dir="rtl"></p>
                                <p class="text-[11px] text-gray-400 italic mb-1" x-text="dua.latin"></p>
                                <p class="text-[11px] text-gray-500 leading-relaxed" x-text="dua.meaning"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- AYAT HARI INI --}}
            <div class="card p-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <span class="text-base">✨</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-amber-600 font-bold uppercase tracking-widest mb-1">Ayat Hari Ini</p>
                        <p class="text-xs text-gray-600 italic leading-relaxed" x-text="dailyVerse.text"></p>
                        <p class="text-[10px] text-gray-400 mt-1.5 font-medium" x-text="dailyVerse.source"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== JAVASCRIPT ===== --}}
    <script>
    function ramadhanDashboard() {
        return {
            activeTab: 'calendar',
            prayerTimes: [],
            fullPrayerSchedule: [],
            calendarDays: [],
            duas: [],
            dailyVerse: {},
            currentPrayerLabel: 'Memuat...',
            currentPrayerTime: '--:--',
            countdown: 'Menghitung...',
            hijriDate: '',
            gregorianDate: '',
            qiblaDirection: 295.0,
            qiblaStatus: 'Mendeteksi lokasi...',
            locationText: 'Mendeteksi...',
            cityName: 'Ciamis',
            ramadhanDay: 1,
            imsakTime: '--:--',
            maghribTime: '--:--',
            userLat: -7.3305,
            userLng: 108.3508,
            nextPrayerName: '',
            nextPrayerMinutes: 0,

            init() {
                this.setDates();
                this.calculateRamadhanDay();
                this.setPrayerTimes();
                this.buildCalendar();
                this.setDuas();
                this.setDailyVerse();
                this.getLocation();
                this.startCountdown();
            },

            setDates() {
                const now = new Date();
                this.gregorianDate = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
                const ramadhanStart = new Date(2025, 2, 1);
                const hijriDay = Math.max(1, Math.min(30, Math.floor((now - ramadhanStart) / 864e5) + 1));
                this.hijriDate = hijriDay + ' Ramadhan 1446 H';
            },

            calculateRamadhanDay() {
                const diff = Math.floor((new Date() - new Date(2025, 2, 1)) / 864e5);
                this.ramadhanDay = Math.max(1, Math.min(30, diff + 1));
            },

            setPrayerTimes() {
                const times = { imsak:'04:13', subuh:'04:23', terbit:'05:42', dhuha:'06:15', dzuhur:'11:52', ashar:'15:13', maghrib:'17:55', isya:'19:08' };
                this.imsakTime = times.imsak;
                this.maghribTime = times.maghrib;

                const now = new Date();
                const cm = now.getHours() * 60 + now.getMinutes();
                const tm = t => { const [h,m] = t.split(':').map(Number); return h*60+m; };

                const list = [
                    { name:'Imsak', time:times.imsak },
                    { name:'Subuh', time:times.subuh },
                    { name:'Dzuhur', time:times.dzuhur },
                    { name:'Ashar', time:times.ashar },
                    { name:'Maghrib', time:times.maghrib },
                    { name:'Isya', time:times.isya },
                ];

                let ai = 0;
                for (let i = list.length-1; i >= 0; i--) { if (cm >= tm(list[i].time)) { ai = i; break; } }

                this.prayerTimes = list.map((p,i) => ({ ...p, isActive: i === ai }));

                let ni = ai + 1;
                if (ni >= list.length) ni = 0;
                this.currentPrayerLabel = list[ai].name;
                this.currentPrayerTime = list[ai].time;
                this.nextPrayerName = list[ni].name;
                this.nextPrayerMinutes = tm(list[ni].time);

                this.fullPrayerSchedule = [
                    { name:'Imsak', arabic:'إمساك', time:times.imsak, icon:'🌙', isActive:false },
                    { name:'Subuh', arabic:'الفجر', time:times.subuh, icon:'🌄', isActive:false },
                    { name:'Terbit', arabic:'الشروق', time:times.terbit, icon:'☀️', isActive:false },
                    { name:'Dhuha', arabic:'الضحى', time:times.dhuha, icon:'🌤', isActive:false },
                    { name:'Dzuhur', arabic:'الظهر', time:times.dzuhur, icon:'🌞', isActive:false },
                    { name:'Ashar', arabic:'العصر', time:times.ashar, icon:'🌇', isActive:false },
                    { name:'Maghrib', arabic:'المغرب', time:times.maghrib, icon:'🌅', isActive:false },
                    { name:'Isya', arabic:'العشاء', time:times.isya, icon:'🌃', isActive:false },
                ];
                for (let i = this.fullPrayerSchedule.length-1; i >= 0; i--) {
                    if (cm >= tm(this.fullPrayerSchedule[i].time)) { this.fullPrayerSchedule[i].isActive = true; break; }
                }
            },

            startCountdown() {
                const tick = () => {
                    const now = new Date();
                    const cm = now.getHours()*60 + now.getMinutes();
                    let diff = this.nextPrayerMinutes - cm;
                    if (diff < 0) diff += 1440;
                    this.countdown = Math.floor(diff/60) + 'j ' + (diff%60) + 'm menuju ' + this.nextPrayerName;
                };
                tick();
                setInterval(tick, 30000);
            },

            buildCalendar() {
                const days = [];
                for (let i = 0; i < 5; i++) days.push({ key:'e'+i, day:0, isToday:false, isCompleted:false });
                for (let d = 1; d <= 30; d++) days.push({ key:'d'+d, day:d, isToday: d===this.ramadhanDay, isCompleted: d<this.ramadhanDay });
                this.calendarDays = days;
            },

            setDuas() {
                this.duas = [
                    { icon:'🌙', title:'Doa Niat Puasa', arabic:'نَوَيْتُ صَوْمَ غَدٍ عَنْ أَدَاءِ فَرْضِ شَهْرِ رَمَضَانَ هٰذِهِ السَّنَةِ لِلّٰهِ تَعَالَى', latin:"Nawaitu shauma ghadin 'an adaa-i fardhi syahri ramadhaana haadzihis sanati lillaahi ta'aalaa", meaning:"Aku berniat puasa esok hari untuk menunaikan kewajiban di bulan Ramadhan tahun ini karena Allah Ta'ala." },
                    { icon:'🌅', title:'Doa Berbuka Puasa', arabic:'اَللّٰهُمَّ لَكَ صُمْتُ وَبِكَ اٰمَنْتُ وَعَلَى رِزْقِكَ أَفْطَرْتُ', latin:"Allahumma laka shumtu wa bika aamantu wa 'ala rizqika afthartu", meaning:"Ya Allah, untuk-Mu aku berpuasa, kepada-Mu aku beriman, dan dengan rezeki-Mu aku berbuka." },
                    { icon:'🤲', title:'Doa Setelah Adzan', arabic:'اَللّٰهُمَّ رَبَّ هٰذِهِ الدَّعْوَةِ التَّامَّةِ وَالصَّلاَةِ الْقَائِمَةِ اٰتِ مُحَمَّدًا الْوَسِيْلَةَ وَالْفَضِيْلَةَ', latin:"Allahumma rabba haadzihid da'watit taammah, wash sholaatil qoo-imah, aati muhammadanil wasiilata wal fadhiilah", meaning:"Ya Allah, Tuhan pemilik seruan yang sempurna ini dan sholat yang akan ditegakkan, berikanlah kepada Muhammad wasilah dan keutamaan." },
                    { icon:'✨', title:'Doa Lailatul Qadr', arabic:'اَللّٰهُمَّ إِنَّكَ عَفُوٌّ تُحِبُّ الْعَفْوَ فَاعْفُ عَنِّي', latin:"Allahumma innaka 'afuwwun tuhibbul 'afwa fa'fu 'annii", meaning:"Ya Allah, sesungguhnya Engkau Maha Pemaaf dan menyukai maaf, maka maafkanlah aku." }
                ];
            },

            setDailyVerse() {
                const v = [
                    { text:'"Sesungguhnya bersama kesulitan ada kemudahan."', source:'QS. Al-Insyirah: 6' },
                    { text:'"Hai orang-orang yang beriman, diwajibkan atas kamu berpuasa sebagaimana diwajibkan atas orang-orang sebelum kamu agar kamu bertakwa."', source:'QS. Al-Baqarah: 183' },
                    { text:'"Dan apabila hamba-hamba-Ku bertanya kepadamu tentang Aku, maka sesungguhnya Aku dekat."', source:'QS. Al-Baqarah: 186' },
                    { text:'"Bulan Ramadhan adalah bulan yang di dalamnya diturunkan Al-Quran, sebagai petunjuk bagi manusia."', source:'QS. Al-Baqarah: 185' },
                    { text:'"Sesungguhnya Allah tidak akan mengubah nasib suatu kaum hingga mereka mengubah diri mereka sendiri."', source:"QS. Ar-Ra'd: 11" },
                ];
                this.dailyVerse = v[new Date().getDate() % v.length];
            },

            getLocation() {
                this.locationText = 'Mendeteksi lokasi...';
                if (!navigator.geolocation) { this.locationText = 'GPS tidak tersedia — default Ciamis'; this.calculateQibla(); return; }
                navigator.geolocation.getCurrentPosition(
                    p => { this.userLat=p.coords.latitude; this.userLng=p.coords.longitude; this.locationText='Lat: '+this.userLat.toFixed(4)+', Lng: '+this.userLng.toFixed(4); this.calculateQibla(); },
                    () => { this.locationText='Ciamis, Jawa Barat (default)'; this.calculateQibla(); },
                    { enableHighAccuracy:true, timeout:10000 }
                );
            },

            calculateQibla() {
                const kLat=21.4225, kLng=39.8262;
                const lat1=this.userLat*Math.PI/180, lat2=kLat*Math.PI/180, dLng=(kLng-this.userLng)*Math.PI/180;
                const y=Math.sin(dLng)*Math.cos(lat2), x=Math.cos(lat1)*Math.sin(lat2)-Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLng);
                this.qiblaDirection = (Math.atan2(y,x)*180/Math.PI+360)%360;
                this.qiblaStatus = 'Arah ' + this.qiblaDirection.toFixed(1) + '° dari utara (Barat Laut)';
            }
        };
    }
    </script>

    <style>
    /* ===== App Shell ===== */
    .ramadhan-app {
        max-width: 480px;
        margin: -2rem auto 0;
        min-height: 100vh;
    }

    /* Hero */
    .hero-header {
        position: relative;
        overflow: hidden;
        border-radius: 0 0 1.5rem 1.5rem;
        background: linear-gradient(160deg, #0c1e3d 0%, #1a3f7a 40%, #2563eb 100%);
    }

    /* Prayer Row */
    .prayer-row {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 4px;
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 1rem;
        padding: 5px;
    }
    .prayer-slot {
        text-align: center;
        border-radius: 0.7rem;
        padding: 8px 2px;
        transition: all 0.3s cubic-bezier(.4,0,.2,1);
        cursor: default;
    }
    .prayer-slot.active {
        background: white;
        box-shadow: 0 4px 16px rgba(37,99,235,0.3);
        transform: scale(1.02);
    }
    .prayer-slot-name {
        display: block;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 3px;
        color: rgba(191,219,254,0.7);
    }
    .prayer-slot.active .prayer-slot-name { color: #3b82f6; }
    .prayer-slot-time {
        display: block;
        font-size: 13px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        color: white;
    }
    .prayer-slot.active .prayer-slot-time { color: #1e3a8a; }

    /* Menu Buttons */
    .menu-btn {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1px;
        padding: 10px 4px;
        border-radius: 1rem;
        transition: all 0.2s cubic-bezier(.4,0,.2,1);
        background: white;
        color: #9ca3af;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
    }
    .menu-btn.active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        box-shadow: 0 4px 16px rgba(37,99,235,0.35);
        border-color: transparent;
        transform: translateY(-1px);
    }
    .menu-btn:not(.active):hover { box-shadow: 0 3px 10px rgba(0,0,0,0.06); transform: translateY(-1px); }

    /* Cards */
    .card {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03), 0 0 0 1px rgba(0,0,0,0.02);
    }
    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: linear-gradient(135deg, #0c1e3d, #2563eb);
    }

    /* Arabic Font */
    .font-arabic { font-family: 'Traditional Arabic', 'Scheherazade New', 'Amiri', serif; }

    /* ===== Override ALL Filament Chrome ===== */
    .fi-page-header,
    .fi-topbar,
    .fi-sidebar-close-overlay,
    .fi-sidebar { display: none !important; }

    .fi-main-ctn,
    .fi-main { padding: 0 !important; max-width: 100% !important; }

    .fi-page > div:first-child { padding: 0 !important; gap: 0 !important; }

    .fi-body { background: #f8fafc !important; }
    </style>
</x-filament-panels::page>
