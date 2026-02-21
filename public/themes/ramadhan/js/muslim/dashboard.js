// @ts-nocheck
/**
 * Buku Ramadhan â€” Dashboard Alpine.js Component
 * File : public/themes/ramadhan/js/dashboard.js
 */

function ramadhanDashboard() {
    return {
        // ── State ──────────────────────────────────────────────────────────
        activeTab: "calendar",
        showChangePassword: false,
        prayerTimes: [],
        fullPrayerSchedule: [],
        calendarDays: [],
        duas: [],
        allDuas: [],
        filteredDuas: [],
        doaSearch: "",
        activeDoaCategory: "semua",
        expandedDoas: [],
        doasLoading: true,
        doaCategories: [],
        paginatedDuas: [],
        doaPage: 1,
        doaPerPage: 10,
        doaTotalPages: 1,
        doaPageNumbers: [],
        dailyVerse: {},
        currentPrayerLabel: "",
        currentPrayerTime: "--:--",
        clockMain: "--:--:--",
        clockWIB: "--:--",
        clockWITA: "--:--",
        clockWIT: "--:--",
        greeting: "",
        selectedTz: "WIB",
        countdown: "Menghitung...",
        hijriDate: "",
        gregorianDate: "",
        qiblaDirection: 295.0,
        qiblaStatus: "Mendeteksi lokasi...",
        locationText: "Mendeteksi...",
        locationCity: "Mendeteksi lokasi...",
        locationCoords: "",
        cityName: "Ciamis",
        // Compass / Device Orientation
        compassHeading: 0,
        compassActive: false,
        compassSupported: false,
        compassPermission: "unknown", // unknown | granted | denied | unsupported
        compassAccuracy: null,
        distanceToKaaba: 0,
        gpsAccuracy: null,
        gpsQuality: "detecting", // detecting | excellent | good | fair | poor | ip-based
        ramadhanDay: 1,
        calendarMonthLabel: "",
        imsakTime: "--:--",
        maghribTime: "--:--",
        userLat: -7.3305,
        userLng: 108.3508,
        nextPrayerName: "",
        nextPrayerMinutes: 0,
        showLocationPicker: false,
        locationSearch: "",
        filteredLocations: [],
        indonesiaLocations: [],
        locationsLoading: true,
        sidebarTabs: [
            {
                id: "calendar",
                label: "Kalender Ramadhan",
                mobileLabel: "Kalender",
            },
            { id: "schedule", label: "Jadwal Sholat", mobileLabel: "Jadwal" },
            { id: "qibla", label: "Arah Kiblat", mobileLabel: "Kiblat" },
            { id: "dua", label: "Doa Harian", mobileLabel: "Doa" },
            { id: "account", label: "Pengaturan Akun", mobileLabel: "Akun" },
        ],

        // ── Form State ─────────────────────────────────────────────────────
        formDay: 1,
        formSubmitted: false,
        formSaving: false,
        submittedDays: [],
        submissionStatuses: {},
        formData: {
            puasa: "",
            sholat_dzuhur_j: false,
            sholat_dzuhur_m: false,
            sholat_ashar_j: false,
            sholat_ashar_m: false,
            sholat_maghrib_j: false,
            sholat_maghrib_m: false,
            sholat_isya_j: false,
            sholat_isya_m: false,
            sholat_subuh_j: false,
            sholat_subuh_m: false,
            tarawih_j: false,
            tarawih_m: false,
            rowatib: "",
            tahajud: "",
            dhuha: "",
            tadarus_surat: "",
            tadarus_ayat: "",
            kegiatan: {
                dzikir_pagi: false,
                olahraga: false,
                membantu_ortu: false,
                membersihkan_kamar: false,
                membersihkan_rumah: false,
                membersihkan_halaman: false,
                merawat_lingkungan: false,
                dzikir_petang: false,
                sedekah: false,
                buka_keluarga: false,
                literasi: false,
                menabung: false,
                tidur_cepat: false,
                bangun_pagi: false,
            },
            ringkasan_ceramah: "",
        },

        // ── Lifecycle ──────────────────────────────────────────────────────
        init() {
            this.loadIndonesiaLocations();
            this.setDates();
            this.calculateRamadhanDay();
            this.formDay = this.ramadhanDay;
            this.loadSubmittedDays();
            this.checkFormSubmitted();
            this.setPrayerTimes();
            this.buildCalendar();
            this.syncFromServer();
            this.loadDoas();
            this.setDailyVerse();
            // Restore saved location first; only fall back to GPS if none
            if (!this.loadSavedLocation()) {
                this.getLocation();
            }
            this.startCountdown();
            this.startClock();
            this.initCompass();
        },

        // ── Location Data ──────────────────────────────────────────────────
        loadIndonesiaLocations() {
            this.locationsLoading = true;
            fetch("/themes/ramadhan/data/locations.json")
                .then((r) => r.json())
                .then((data) => {
                    this.indonesiaLocations = data;
                    this.filteredLocations = data;
                    this.locationsLoading = false;
                })
                .catch(() => {
                    console.warn("Gagal memuat data lokasi");
                    this.locationsLoading = false;
                });
        },

        // Find nearest location from loaded kecamatan data
        _findNearestLocation(lat, lng) {
            if (
                !this.indonesiaLocations ||
                this.indonesiaLocations.length === 0
            )
                return null;
            let nearest = null;
            let minDist = Infinity;
            for (const loc of this.indonesiaLocations) {
                const dLat = lat - loc.lat;
                const dLng = lng - loc.lng;
                const dist = dLat * dLat + dLng * dLng;
                if (dist < minDist) {
                    minDist = dist;
                    nearest = loc;
                }
            }
            // Only match if within ~20km (roughly 0.18° at equator)
            if (minDist > 0.032) return null;
            return nearest;
        },

        // ── Date & Calendar ────────────────────────────────────────────────
        setDates() {
            const now = new Date();
            this.gregorianDate = now.toLocaleDateString("id-ID", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            });
            const ramadhanStart = new Date(2026, 1, 19);
            const hijriDay = Math.max(
                1,
                Math.min(30, Math.floor((now - ramadhanStart) / 864e5) + 1),
            );
            this.hijriDate = hijriDay + " Ramadhan 1447 H";
        },

        calculateRamadhanDay() {
            const now = new Date();
            const ramadhanStart = new Date(2026, 1, 19);
            const diff = Math.floor((now - ramadhanStart) / 864e5);
            this.ramadhanDay = Math.max(1, Math.min(30, diff + 1));
        },

        buildCalendar() {
            const days = [];
            const ramadhanStart = new Date(2026, 1, 19); // 19 Feb 2026 = 1 Ramadhan 1447H
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Build 30 days with Masehi + Hijri dates
            for (let d = 0; d < 30; d++) {
                const date = new Date(ramadhanStart);
                date.setDate(ramadhanStart.getDate() + d);
                const hijriDay = d + 1;
                const masehiDay = date.getDate();
                const isToday = date.getTime() === today.getTime();
                const isCompleted = this.submittedDays.includes(hijriDay);
                const dayStatus = this.submissionStatuses[hijriDay];
                const statusStr = dayStatus ? dayStatus.status : "";
                const isVerified = isCompleted && statusStr === "verified";
                const isPending =
                    isCompleted &&
                    (statusStr === "pending" || statusStr === "");
                const isRejected = isCompleted && statusStr === "rejected";
                const isPast = date < today && !isToday;
                const isPastUnfilled = (isPast || isToday) && !isCompleted;

                days.push({
                    key: "d" + hijriDay,
                    hijriDay: hijriDay,
                    masehiDay: masehiDay,
                    month: date.getMonth(),
                    dayOfWeek: date.getDay(),
                    isToday: isToday,
                    isCompleted: isCompleted,
                    isVerified: isVerified,
                    isPending: isPending,
                    isRejected: isRejected,
                    isPast: isPast,
                    isPastUnfilled: isPastUnfilled,
                    dateObj: date,
                });
            }

            // Group by weeks (Mon-Sun grid)
            // First, find offset for the first day
            const firstDow = days[0].dayOfWeek; // 0=Sun..6=Sat
            // Convert to Mon=0 format: (dow + 6) % 7
            const monBasedDow = (firstDow + 6) % 7;

            const grid = [];
            // Empty cells before first day
            for (let i = 0; i < monBasedDow; i++) {
                grid.push({
                    key: "e" + i,
                    hijriDay: 0,
                    masehiDay: 0,
                    isToday: false,
                    isCompleted: false,
                    isPast: false,
                });
            }
            // Fill all 30 days
            for (const d of days) {
                grid.push(d);
            }
            this.calendarDays = grid;

            // Set month label
            const firstMonth = days[0].dateObj.toLocaleString("id-ID", {
                month: "long",
            });
            const lastMonth = days[29].dateObj.toLocaleString("id-ID", {
                month: "long",
            });
            const year = days[0].dateObj.getFullYear();
            this.calendarMonthLabel =
                firstMonth === lastMonth
                    ? firstMonth + " " + year
                    : firstMonth + " - " + lastMonth + " " + year;
        },

        // ── Prayer Times ───────────────────────────────────────────────────
        setPrayerTimes(times) {
            if (!times) {
                times = {
                    imsak: "04:13",
                    subuh: "04:23",
                    terbit: "05:42",
                    dhuha: "06:15",
                    dzuhur: "11:52",
                    ashar: "15:13",
                    maghrib: "17:55",
                    isya: "19:08",
                };
            }
            this.imsakTime = times.imsak;
            this.maghribTime = times.maghrib;
            const now = this.getNowInSelectedTz();
            const cm = now.getHours() * 60 + now.getMinutes();
            const tm = (t) => {
                const [h, m] = t.split(":").map(Number);
                return h * 60 + m;
            };
            const list = [
                { name: "Imsak", time: times.imsak },
                { name: "Subuh", time: times.subuh },
                { name: "Dzuhur", time: times.dzuhur },
                { name: "Ashar", time: times.ashar },
                { name: "Maghrib", time: times.maghrib },
                { name: "Isya", time: times.isya },
            ];
            // Find the next upcoming prayer (highlight it)
            let ni = 0; // next prayer index
            let found = false;
            for (let i = 0; i < list.length; i++) {
                if (cm < tm(list[i].time)) {
                    ni = i;
                    found = true;
                    break;
                }
            }
            if (!found) {
                // All prayers passed today, next is Imsak (tomorrow)
                ni = 0;
            }
            this.prayerTimes = list.map((p, i) => ({
                ...p,
                isActive: i === ni,
            }));
            this.nextPrayerName = list[ni].name;
            this.nextPrayerMinutes = tm(list[ni].time);
            // Current prayer is the one before next
            let ci = (ni - 1 + list.length) % list.length;
            this.currentPrayerLabel = list[ci].name;
            this.currentPrayerTime = list[ci].time;
            this.fullPrayerSchedule = [
                {
                    name: "Imsak",
                    arabic: "إمساك",
                    time: times.imsak,
                    isActive: false,
                },
                {
                    name: "Subuh",
                    arabic: "الفجر",
                    time: times.subuh,
                    isActive: false,
                },
                {
                    name: "Terbit",
                    arabic: "الشروق",
                    time: times.terbit,
                    isActive: false,
                },
                {
                    name: "Dhuha",
                    arabic: "الضحى",
                    time: times.dhuha,
                    isActive: false,
                },
                {
                    name: "Dzuhur",
                    arabic: "الظهر",
                    time: times.dzuhur,
                    isActive: false,
                },
                {
                    name: "Ashar",
                    arabic: "العصر",
                    time: times.ashar,
                    isActive: false,
                },
                {
                    name: "Maghrib",
                    arabic: "المغرب",
                    time: times.maghrib,
                    isActive: false,
                },
                {
                    name: "Isya",
                    arabic: "العشاء",
                    time: times.isya,
                    isActive: false,
                },
            ];
            // Highlight next upcoming prayer in full schedule too
            for (let i = 0; i < this.fullPrayerSchedule.length; i++) {
                if (cm < tm(this.fullPrayerSchedule[i].time)) {
                    this.fullPrayerSchedule[i].isActive = true;
                    break;
                }
            }
        },

        calculatePrayerTimes() {
            const lat = this.userLat || -7.3305;
            const lng = this.userLng || 108.3508;
            const now = this.getNowInSelectedTz();
            const dd = String(now.getDate()).padStart(2, "0");
            const mm = String(now.getMonth() + 1).padStart(2, "0");
            const yyyy = now.getFullYear();
            const dateStr = dd + "-" + mm + "-" + yyyy;
            // Method 20 = Kementerian Agama Republik Indonesia
            const url =
                "https://api.aladhan.com/v1/timings/" +
                dateStr +
                "?latitude=" +
                lat +
                "&longitude=" +
                lng +
                "&method=20";
            fetch(url)
                .then((r) => r.json())
                .then((data) => {
                    if (
                        data &&
                        data.code === 200 &&
                        data.data &&
                        data.data.timings
                    ) {
                        const t = data.data.timings;
                        // Imsak = 10 min before Fajr if not provided
                        const imsak = t.Imsak
                            ? t.Imsak.split(" ")[0]
                            : this._subtractMinutes(t.Fajr.split(" ")[0], 10);
                        const times = {
                            imsak: imsak,
                            subuh: t.Fajr.split(" ")[0],
                            terbit: t.Sunrise.split(" ")[0],
                            dhuha: this._addMinutes(
                                t.Sunrise.split(" ")[0],
                                15,
                            ),
                            dzuhur: t.Dhuhr.split(" ")[0],
                            ashar: t.Asr.split(" ")[0],
                            maghrib: t.Maghrib.split(" ")[0],
                            isya: t.Isha.split(" ")[0],
                        };
                        this.setPrayerTimes(times);
                        this.startCountdown();
                        console.log(
                            "[Jadwal Sholat] Data dari API Aladhan untuk",
                            this.cityName || "koordinat",
                            lat.toFixed(4) + "," + lng.toFixed(4),
                        );
                    } else {
                        console.warn(
                            "[Jadwal Sholat] Respons API tidak valid, pakai default",
                        );
                        this.setPrayerTimes(null);
                    }
                })
                .catch((err) => {
                    console.warn(
                        "[Jadwal Sholat] Gagal fetch API, pakai default:",
                        err,
                    );
                    this.setPrayerTimes(null);
                });
        },

        _addMinutes(timeStr, mins) {
            const [h, m] = timeStr.split(":").map(Number);
            const total = h * 60 + m + mins;
            return (
                String(Math.floor(total / 60)).padStart(2, "0") +
                ":" +
                String(total % 60).padStart(2, "0")
            );
        },

        _subtractMinutes(timeStr, mins) {
            const [h, m] = timeStr.split(":").map(Number);
            let total = h * 60 + m - mins;
            if (total < 0) total += 1440;
            return (
                String(Math.floor(total / 60)).padStart(2, "0") +
                ":" +
                String(total % 60).padStart(2, "0")
            );
        },

        getTimezoneForLng(lng) {
            if (lng < 115) return { tz: "WIB", iana: "Asia/Jakarta" };
            if (lng < 135) return { tz: "WITA", iana: "Asia/Makassar" };
            return { tz: "WIT", iana: "Asia/Jayapura" };
        },

        getSelectedIana() {
            if (this.selectedTz === "WITA") return "Asia/Makassar";
            if (this.selectedTz === "WIT") return "Asia/Jayapura";
            return "Asia/Jakarta";
        },

        getNowInSelectedTz() {
            return new Date(
                new Date().toLocaleString("en-US", {
                    timeZone: this.getSelectedIana(),
                }),
            );
        },

        updateGreeting(hour) {
            if (this.maghribTime && this.maghribTime !== "--:--") {
                const [mH, mM] = this.maghribTime.split(":").map(Number);
                const maghribMin = mH * 60 + mM;
                const nowMin = hour * 60 + new Date().getMinutes();
                if (nowMin >= maghribMin && nowMin <= maghribMin + 90) {
                    this.greeting = "\u{1F319} Selamat Berbuka Puasa";
                    return;
                }
            }
            if (hour >= 3 && hour < 11)
                this.greeting = "\u{2600}\u{FE0F} Selamat Pagi";
            else if (hour >= 11 && hour < 15)
                this.greeting = "\u{1F324}\u{FE0F} Selamat Siang";
            else if (hour >= 15 && hour < 18)
                this.greeting = "\u{1F305} Selamat Sore";
            else this.greeting = "\u{1F319} Selamat Malam";
        },

        startClock() {
            const pad = (n) => String(n).padStart(2, "0");
            const tick = () => {
                const now = new Date();
                const wib = new Date(
                    now.toLocaleString("en-US", { timeZone: "Asia/Jakarta" }),
                );
                const wita = new Date(
                    now.toLocaleString("en-US", { timeZone: "Asia/Makassar" }),
                );
                const wit = new Date(
                    now.toLocaleString("en-US", { timeZone: "Asia/Jayapura" }),
                );

                this.clockWIB =
                    pad(wib.getHours()) + ":" + pad(wib.getMinutes());
                this.clockWITA =
                    pad(wita.getHours()) + ":" + pad(wita.getMinutes());
                this.clockWIT =
                    pad(wit.getHours()) + ":" + pad(wit.getMinutes());

                let mainTime;
                if (this.selectedTz === "WITA") mainTime = wita;
                else if (this.selectedTz === "WIT") mainTime = wit;
                else mainTime = wib;

                this.clockMain =
                    pad(mainTime.getHours()) +
                    ":" +
                    pad(mainTime.getMinutes()) +
                    ":" +
                    pad(mainTime.getSeconds());
                this.updateGreeting(mainTime.getHours());
            };
            tick();
            setInterval(tick, 1000);
        },

        startCountdown() {
            if (this._countdownInterval) clearInterval(this._countdownInterval);
            const tick = () => {
                const now = this.getNowInSelectedTz();
                const cm = now.getHours() * 60 + now.getMinutes();
                let diff = this.nextPrayerMinutes - cm;
                if (diff < 0) diff += 1440;
                const h = Math.floor(diff / 60);
                const m = diff % 60;
                this.countdown =
                    h + " jam " + m + " menit menuju " + this.nextPrayerName;
            };
            tick();
            this._countdownInterval = setInterval(tick, 30000);
        },

        // ── Doa Collection ───────────────────────────────────────────────
        async loadDoas() {
            this.doasLoading = true;
            try {
                const res = await fetch("/themes/ramadhan/data/doas.json");
                if (!res.ok) throw new Error("Failed to load doas");
                this.allDuas = await res.json();
            } catch (e) {
                console.warn("Doa JSON load failed, using fallback:", e);
                this.allDuas = [
                    {
                        id: 1,
                        title: "Doa Niat Puasa",
                        category: "ramadhan",
                        arabic: "نَوَيْتُ صَوْمَ غَدٍ عَنْ أَدَاءِ فَرْضِ شَهْرِ رَمَضَانَ هٰذِهِ السَّنَةِ لِلّٰهِ تَعَالَى",
                        latin: "Nawaitu shauma ghadin 'an adaa-i fardhi syahri ramadhaana haadzihis sanati lillaahi ta'aalaa",
                        translation:
                            "Aku berniat puasa esok hari untuk menunaikan kewajiban di bulan Ramadhan tahun ini karena Allah Ta'ala.",
                        source: "Hadits",
                    },
                    {
                        id: 2,
                        title: "Doa Berbuka Puasa",
                        category: "ramadhan",
                        arabic: "اَللّٰهُمَّ لَكَ صُمْتُ وَبِكَ اٰمَنْتُ وَعَلَى رِزْقِكَ أَفْطَرْتُ",
                        latin: "Allahumma laka shumtu wa bika aamantu wa 'ala rizqika afthartu",
                        translation:
                            "Ya Allah, untuk-Mu aku berpuasa, kepada-Mu aku beriman, dan dengan rezeki-Mu aku berbuka.",
                        source: "HR. Abu Dawud",
                    },
                    {
                        id: 4,
                        title: "Doa Lailatul Qadr",
                        category: "ramadhan",
                        arabic: "اَللّٰهُمَّ إِنَّكَ عَفُوٌّ تُحِبُّ الْعَفْوَ فَاعْفُ عَنِّيْ",
                        latin: "Allahumma innaka 'afuwwun tuhibbul 'afwa fa'fu 'annii",
                        translation:
                            "Ya Allah, sesungguhnya Engkau Maha Pemaaf dan menyukai maaf, maka maafkanlah aku.",
                        source: "HR. Tirmidzi",
                    },
                ];
            }

            // Also keep duas for backward compatibility
            this.duas = this.allDuas.map((d) => ({
                title: d.title,
                arabic: d.arabic,
                latin: d.latin,
                meaning: d.translation,
            }));

            this._buildDoaCategories();
            this.filterDuas();
            this.doasLoading = false;
        },

        _buildDoaCategories() {
            const catDefs = [
                { id: "semua", label: "Semua", icon: "📖" },
                { id: "ramadhan", label: "Ramadhan", icon: "🌙" },
                { id: "quran", label: "Al-Quran", icon: "📗" },
                { id: "hadits", label: "Hadits", icon: "📜" },
                { id: "ibadah", label: "Ibadah", icon: "🕌" },
                { id: "harian", label: "Harian", icon: "☀️" },
            ];
            this.doaCategories = catDefs
                .map((c) => ({
                    ...c,
                    count:
                        c.id === "semua"
                            ? this.allDuas.length
                            : this.allDuas.filter((d) => d.category === c.id)
                                  .length,
                }))
                .filter((c) => c.count > 0 || c.id === "semua");
        },

        filterDuas() {
            let result = this.allDuas;

            // Category filter
            if (this.activeDoaCategory !== "semua") {
                result = result.filter(
                    (d) => d.category === this.activeDoaCategory,
                );
            }

            // Search filter
            if (this.doaSearch.trim()) {
                const q = this.doaSearch.trim().toLowerCase();
                result = result.filter(
                    (d) =>
                        d.title.toLowerCase().includes(q) ||
                        d.latin.toLowerCase().includes(q) ||
                        d.translation.toLowerCase().includes(q) ||
                        d.source.toLowerCase().includes(q),
                );
            }

            this.filteredDuas = result;
            this.doaPage = 1;
            this.paginateDuas();
        },

        paginateDuas() {
            this.doaTotalPages = Math.max(
                1,
                Math.ceil(this.filteredDuas.length / this.doaPerPage),
            );
            if (this.doaPage > this.doaTotalPages)
                this.doaPage = this.doaTotalPages;
            const start = (this.doaPage - 1) * this.doaPerPage;
            this.paginatedDuas = this.filteredDuas.slice(
                start,
                start + this.doaPerPage,
            );
            this._buildDoaPageNumbers();
        },

        _buildDoaPageNumbers() {
            const pages = [];
            const total = this.doaTotalPages;
            const cur = this.doaPage;
            if (total <= 7) {
                for (let i = 1; i <= total; i++) pages.push(i);
            } else {
                pages.push(1);
                if (cur > 3) pages.push("...");
                const start = Math.max(2, cur - 1);
                const end = Math.min(total - 1, cur + 1);
                for (let i = start; i <= end; i++) pages.push(i);
                if (cur < total - 2) pages.push("...");
                pages.push(total);
            }
            this.doaPageNumbers = pages;
        },

        toggleDoaExpand(id) {
            const idx = this.expandedDoas.indexOf(id);
            if (idx === -1) {
                this.expandedDoas.push(id);
            } else {
                this.expandedDoas.splice(idx, 1);
            }
        },

        getCategoryLabel(catId) {
            const map = {
                ramadhan: "Ramadhan",
                quran: "Al-Quran",
                hadits: "Hadits",
                ibadah: "Ibadah",
                harian: "Harian",
            };
            return map[catId] || catId;
        },

        setDailyVerse() {
            // Set initial placeholder then fetch from API
            this.dailyVerse = {
                text: "Memuat ayat...",
                arabic: "",
                source: "",
                contextLabel: "Ayat Hari Ini",
                loading: true,
            };
            this.fetchContextualVerse();
        },

        /**
         * Determines context based on prayer times / time of day:
         * - 03:00 â€“ Subuh   â†’ sahur
         * - Subuh â€“ 10:00   â†’ pagi (morning)
         * - 10:00 â€“ Ashar   â†’ siang (daytime)
         * - Ashar â€“ Maghrib â†’ sore (afternoon approaching iftar)
         * - Maghrib â€“ 21:00 â†’ berbuka (iftar)
         * - 21:00 â€“ 03:00  â†’ malam (night)
         */
        getVerseContext() {
            const now = new Date();
            const hh = now.getHours();
            const mm = now.getMinutes();
            const nowMin = hh * 60 + mm;

            // Try to get prayer times, fall back to reasonable defaults
            const parseTime = (label) => {
                const p = this.fullPrayerSchedule.find(
                    (x) => x.label === label,
                );
                if (!p || !p.time) return null;
                const [h, m] = p.time.split(":").map(Number);
                return h * 60 + m;
            };

            const subuhMin = parseTime("Subuh") || 4 * 60 + 30;
            const asharMin = parseTime("Ashar") || 15 * 60;
            const maghribMin = parseTime("Maghrib") || 18 * 60;

            if (nowMin >= 3 * 60 && nowMin < subuhMin) return "sahur";
            if (nowMin >= subuhMin && nowMin < 10 * 60) return "pagi";
            if (nowMin >= 10 * 60 && nowMin < asharMin) return "siang";
            if (nowMin >= asharMin && nowMin < maghribMin) return "sore";
            if (nowMin >= maghribMin && nowMin < 21 * 60) return "berbuka";
            return "malam";
        },

        /**
         * Curated verse lists per context.
         * Each has surah:ayah identifiers for the al-quran.cloud API.
         */
        getContextualVersePool() {
            return {
                sahur: {
                    label: "Waktu Sahur",
                    refs: [
                        // === Tema: Sahur & Makan Minum Sebelum Fajar ===
                        { s: 2, a: 187, note: "QS. Al-Baqarah: 187" }, // makan minum hingga fajar
                        { s: 97, a: 5, note: "QS. Al-Qadr: 5" }, // malam lailatul qadr hingga terbit fajar
                        // === Tema: Istighfar & Taubat ===
                        { s: 51, a: 18, note: "QS. Adz-Dzariyat: 18" }, // waktu sahur memohon ampun
                        { s: 3, a: 17, note: "QS. Ali Imran: 17" }, // yang memohon ampun di waktu sahur
                        { s: 3, a: 16, note: "QS. Ali Imran: 16" }, // ampunilah dosa-dosa kami
                        { s: 3, a: 135, note: "QS. Ali Imran: 135" }, // ingat Allah lalu memohon ampun
                        { s: 3, a: 147, note: "QS. Ali Imran: 147" }, // doa memohon ampunan
                        { s: 71, a: 10, note: "QS. Nuh: 10" }, // mohonlah ampun kepada Tuhanmu
                        { s: 71, a: 11, note: "QS. Nuh: 11" }, // Dia menurunkan hujan lebat
                        { s: 39, a: 53, note: "QS. Az-Zumar: 53" }, // jangan berputus asa dari rahmat Allah
                        { s: 66, a: 8, note: "QS. At-Tahrim: 8" }, // bertaubatlah dengan taubat yang murni
                        { s: 110, a: 3, note: "QS. An-Nasr: 3" }, // bertasbihlah dan mohon ampun
                        { s: 4, a: 110, note: "QS. An-Nisa: 110" }, // berbuat buruk lalu memohon ampun
                        { s: 11, a: 3, note: "QS. Hud: 3" }, // memohon ampun lalu bertaubat
                        { s: 11, a: 90, note: "QS. Hud: 90" }, // mohon ampun lalu bertaubat, Tuhanku Maha Penyayang
                        { s: 11, a: 114, note: "QS. Hud: 114" }, // kebaikan menghapus keburukan
                        { s: 4, a: 106, note: "QS. An-Nisa: 106" }, // mohonlah ampun kepada Allah
                        { s: 47, a: 19, note: "QS. Muhammad: 19" }, // mohonlah ampun atas dosamu
                        // === Tema: Bangun Malam & Qiyamul Lail ===
                        { s: 73, a: 2, note: "QS. Al-Muzzammil: 2" }, // shalat malam
                        { s: 73, a: 4, note: "QS. Al-Muzzammil: 4" }, // bacalah Al-Quran dengan tartil
                        { s: 73, a: 6, note: "QS. Al-Muzzammil: 6" }, // bangun malam lebih kuat
                        { s: 76, a: 26, note: "QS. Al-Insan: 26" }, // sujudlah dan bertasbihlah semalam
                        { s: 17, a: 79, note: "QS. Al-Isra: 79" }, // tahajjud sebagai tambahan
                        { s: 32, a: 16, note: "QS. As-Sajdah: 16" }, // lambung jauh dari tempat tidur
                        // === Tema: Tasbih Sebelum Fajar ===
                        { s: 52, a: 49, note: "QS. At-Tur: 49" }, // bertasbihlah di waktu malam
                        { s: 20, a: 130, note: "QS. Taha: 130" }, // bertasbihlah sebelum terbit matahari
                        { s: 50, a: 39, note: "QS. Qaf: 39" }, // bertasbihlah sebelum terbit matahari
                        { s: 50, a: 40, note: "QS. Qaf: 40" }, // bertasbihlah di waktu malam dan setelah sujud
                        { s: 52, a: 48, note: "QS. At-Tur: 48" }, // bertasbihlah ketika bangun
                        { s: 40, a: 55, note: "QS. Ghafir: 55" }, // bertasbihlah pagi dan petang
                        { s: 3, a: 41, note: "QS. Ali Imran: 41" }, // berdzikirlah dan bertasbihlah
                        // === Tema: Rahmat & Harapan Fajar ===
                        { s: 6, a: 54, note: "QS. Al-An'am: 54" }, // Tuhanmu telah menetapkan rahmat
                        { s: 39, a: 9, note: "QS. Az-Zumar: 9" }, // yang beribadah di waktu malam
                        { s: 25, a: 64, note: "QS. Al-Furqan: 64" }, // yang bermalam dengan bersujud
                        { s: 51, a: 17, note: "QS. Adz-Dzariyat: 17" }, // sedikit sekali tidur di waktu malam
                    ],
                },
                pagi: {
                    label: "Pagi Hari",
                    refs: [
                        // === Tema: Semangat & Kemudahan ===
                        { s: 94, a: 5, note: "QS. Al-Insyirah: 5" }, // bersama kesulitan ada kemudahan
                        { s: 94, a: 6, note: "QS. Al-Insyirah: 6" }, // bersama kesulitan ada kemudahan (2)
                        { s: 94, a: 7, note: "QS. Al-Insyirah: 7" }, // apabila engkau telah selesai, bersungguh-sungguhlah
                        { s: 93, a: 3, note: "QS. Ad-Dhuha: 3" }, // Tuhanmu tidak meninggalkanmu
                        { s: 93, a: 4, note: "QS. Ad-Dhuha: 4" }, // akhirat lebih baik dari permulaan
                        { s: 93, a: 5, note: "QS. Ad-Dhuha: 5" }, // kelak Tuhanmu pasti memberimu
                        { s: 93, a: 11, note: "QS. Ad-Dhuha: 11" }, // nikmat Tuhanmu ceritakanlah
                        // === Tema: Tawakkal & Ketergantungan pada Allah ===
                        { s: 65, a: 3, note: "QS. At-Talaq: 3" }, // barangsiapa bertawakal
                        { s: 3, a: 159, note: "QS. Ali Imran: 159" }, // bertawakkallah kepada Allah
                        { s: 8, a: 2, note: "QS. Al-Anfal: 2" }, // hati bergetar ketika disebut nama Allah
                        { s: 9, a: 51, note: "QS. At-Taubah: 51" }, // tidak menimpa kami kecuali yang Allah tetapkan
                        { s: 14, a: 12, note: "QS. Ibrahim: 12" }, // hanya kepada Allah kami bertawakkal
                        { s: 33, a: 3, note: "QS. Al-Ahzab: 3" }, // bertawakkallah kepada Allah
                        { s: 12, a: 67, note: "QS. Yusuf: 67" }, // hanya kepada Allah aku bertawakkal
                        // === Tema: Sabar & Keteguhan ===
                        { s: 2, a: 153, note: "QS. Al-Baqarah: 153" }, // minta tolong dengan sabar dan shalat
                        { s: 2, a: 286, note: "QS. Al-Baqarah: 286" }, // Allah tidak membebani
                        { s: 3, a: 139, note: "QS. Ali Imran: 139" }, // jangan bersedih
                        { s: 3, a: 200, note: "QS. Ali Imran: 200" }, // bersabarlah dan kuatkanlah
                        { s: 8, a: 46, note: "QS. Al-Anfal: 46" }, // bersabarlah, Allah bersama orang sabar
                        { s: 39, a: 10, note: "QS. Az-Zumar: 10" }, // orang sabar diberi pahala tanpa batas
                        { s: 16, a: 96, note: "QS. An-Nahl: 96" }, // Allah membalas yang sabar
                        { s: 2, a: 155, note: "QS. Al-Baqarah: 155" }, // Kami menguji dengan ketakutan dan kelaparan
                        { s: 2, a: 156, note: "QS. Al-Baqarah: 156" }, // yang mengatakan inna lillahi wa inna ilaihi raji'un
                        { s: 2, a: 157, note: "QS. Al-Baqarah: 157" }, // mereka mendapat shalawat dan rahmat
                        // === Tema: Dzikir & Ketenangan ===
                        { s: 2, a: 152, note: "QS. Al-Baqarah: 152" }, // ingatlah Aku niscaya Aku ingat kamu
                        { s: 13, a: 28, note: "QS. Ar-Ra'd: 28" }, // dzikrullah hati menjadi tenteram
                        { s: 33, a: 41, note: "QS. Al-Ahzab: 41" }, // berdzikirlah yang sebanyak-banyaknya
                        { s: 33, a: 42, note: "QS. Al-Ahzab: 42" }, // bertasbihlah pagi dan petang
                        { s: 57, a: 4, note: "QS. Al-Hadid: 4" }, // Dia bersama kamu di mana saja
                        { s: 10, a: 62, note: "QS. Yunus: 62" }, // wali Allah tidak ada rasa takut
                        { s: 10, a: 63, note: "QS. Yunus: 63" }, // bagi mereka kabar gembira
                        // === Tema: Berjuang di Jalan Allah ===
                        { s: 29, a: 69, note: "QS. Al-Ankabut: 69" }, // orang yang berjuang di jalan Kami
                        { s: 29, a: 2, note: "QS. Al-Ankabut: 2" }, // manusia berkata kami beriman tanpa diuji
                        { s: 29, a: 3, note: "QS. Al-Ankabut: 3" }, // Allah menguji orang-orang sebelum mereka
                        { s: 47, a: 31, note: "QS. Muhammad: 31" }, // Kami menguji untuk mengetahui yang berjuang
                        { s: 21, a: 35, note: "QS. Al-Anbiya: 35" }, // setiap jiwa akan merasakan mati
                    ],
                },
                siang: {
                    label: "Siang Hari",
                    refs: [
                        // === Tema: Puasa & Ramadhan ===
                        { s: 2, a: 183, note: "QS. Al-Baqarah: 183" }, // diwajibkan puasa
                        { s: 2, a: 184, note: "QS. Al-Baqarah: 184" }, // puasa hari-hari tertentu
                        { s: 2, a: 185, note: "QS. Al-Baqarah: 185" }, // bulan Ramadhan Al-Quran diturunkan
                        { s: 2, a: 197, note: "QS. Al-Baqarah: 197" }, // sebaik-baik bekal adalah takwa
                        // === Tema: Amal Saleh & Kebaikan ===
                        { s: 16, a: 97, note: "QS. An-Nahl: 97" }, // amal saleh hidup yang baik
                        { s: 2, a: 195, note: "QS. Al-Baqarah: 195" }, // berbuat baiklah
                        { s: 2, a: 267, note: "QS. Al-Baqarah: 267" }, // infakkan yang baik-baik
                        { s: 2, a: 261, note: "QS. Al-Baqarah: 261" }, // perumpamaan infak 700 kali lipat
                        { s: 2, a: 262, note: "QS. Al-Baqarah: 262" }, // berinfak tanpa menyebut-nyebut
                        { s: 2, a: 271, note: "QS. Al-Baqarah: 271" }, // sedekah secara tersembunyi
                        { s: 2, a: 274, note: "QS. Al-Baqarah: 274" }, // berinfak malam dan siang
                        { s: 3, a: 92, note: "QS. Ali Imran: 92" }, // tidak akan meraih kebaikan sampai menafkahkan
                        { s: 57, a: 18, note: "QS. Al-Hadid: 18" }, // bersedekah akan dilipatgandakan
                        { s: 73, a: 20, note: "QS. Al-Muzzammil: 20" }, // bacalah Al-Quran, shalat, zakat
                        { s: 76, a: 8, note: "QS. Al-Insan: 8" }, // memberi makan orang miskin, yatim, tawanan
                        { s: 76, a: 9, note: "QS. Al-Insan: 9" }, // memberi makan karena Allah semata
                        // === Tema: Akhlak & Persaudaraan ===
                        { s: 49, a: 10, note: "QS. Al-Hujurat: 10" }, // orang mukmin bersaudara
                        { s: 49, a: 11, note: "QS. Al-Hujurat: 11" }, // jangan mengolok-olok
                        { s: 49, a: 12, note: "QS. Al-Hujurat: 12" }, // jauhilah banyak prasangka buruk
                        { s: 49, a: 13, note: "QS. Al-Hujurat: 13" }, // paling mulia yang paling bertakwa
                        { s: 31, a: 17, note: "QS. Luqman: 17" }, // shalat, amar makruf, sabar
                        { s: 31, a: 18, note: "QS. Luqman: 18" }, // jangan sombong
                        { s: 31, a: 19, note: "QS. Luqman: 19" }, // sederhanalah dalam berjalan dan lunakkan suara
                        { s: 41, a: 34, note: "QS. Fussilat: 34" }, // balaslah keburukan dengan kebaikan
                        { s: 5, a: 2, note: "QS. Al-Ma'idah: 2" }, // tolong-menolong dalam kebaikan
                        { s: 5, a: 8, note: "QS. Al-Ma'idah: 8" }, // berlaku adil
                        { s: 60, a: 8, note: "QS. Al-Mumtahanah: 8" }, // berbuat baik kepada yang tidak memusuhi
                        { s: 16, a: 90, note: "QS. An-Nahl: 90" }, // berlaku adil dan berbuat ihsan
                        // === Tema: Ilmu & Menasihati ===
                        { s: 103, a: 2, note: "QS. Al-Asr: 2" }, // manusia dalam kerugian
                        { s: 103, a: 3, note: "QS. Al-Asr: 3" }, // saling menasihati kebenaran
                        { s: 13, a: 11, note: "QS. Ar-Ra'd: 11" }, // Allah tidak mengubah keadaan suatu kaum
                        { s: 58, a: 11, note: "QS. Al-Mujadalah: 11" }, // Allah mengangkat derajat orang berilmu
                        { s: 20, a: 114, note: "QS. Taha: 114" }, // Ya Tuhanku tambahkanlah ilmu kepadaku
                        { s: 39, a: 9, note: "QS. Az-Zumar: 9" }, // apakah sama yang berilmu dan tidak
                        { s: 96, a: 1, note: "QS. Al-Alaq: 1" }, // bacalah dengan nama Tuhanmu
                        { s: 96, a: 3, note: "QS. Al-Alaq: 3" }, // bacalah, Tuhanmu Maha Mulia
                        { s: 96, a: 4, note: "QS. Al-Alaq: 4" }, // yang mengajar dengan pena
                        { s: 96, a: 5, note: "QS. Al-Alaq: 5" }, // mengajarkan manusia apa yang tidak diketahui
                        // === Tema: Beriman & Bertakwa ===
                        { s: 23, a: 1, note: "QS. Al-Mu'minun: 1" }, // beruntunglah orang-orang beriman
                        { s: 23, a: 2, note: "QS. Al-Mu'minun: 2" }, // yang khusyuk dalam shalatnya
                        { s: 23, a: 3, note: "QS. Al-Mu'minun: 3" }, // yang menjauhkan diri dari perbuatan sia-sia
                        { s: 23, a: 4, note: "QS. Al-Mu'minun: 4" }, // yang menunaikan zakat
                        { s: 23, a: 8, note: "QS. Al-Mu'minun: 8" }, // yang memelihara amanat dan janjinya
                        { s: 8, a: 2, note: "QS. Al-Anfal: 2" }, // hati bergetar ketika disebut nama Allah
                        { s: 8, a: 3, note: "QS. Al-Anfal: 3" }, // yang menegakkan shalat dan berinfak
                        { s: 8, a: 4, note: "QS. Al-Anfal: 4" }, // itulah orang-orang beriman yang sebenarnya
                    ],
                },
                sore: {
                    label: "Menjelang Berbuka",
                    refs: [
                        // === Tema: Allah Dekat & Mengabulkan Doa ===
                        { s: 2, a: 186, note: "QS. Al-Baqarah: 186" }, // Aku dekat, mengabulkan doa
                        { s: 40, a: 60, note: "QS. Ghafir: 60" }, // berdoalah kepada-Ku
                        { s: 27, a: 62, note: "QS. An-Naml: 62" }, // mengabulkan doa orang yang terdesak
                        { s: 42, a: 26, note: "QS. Asy-Syura: 26" }, // memperkenankan doa orang beriman
                        { s: 7, a: 55, note: "QS. Al-A'raf: 55" }, // berdoalah dengan rendah hati
                        { s: 7, a: 56, note: "QS. Al-A'raf: 56" }, // berdoalah dengan rasa takut dan harap
                        { s: 2, a: 185, note: "QS. Al-Baqarah: 185" }, // bulan Ramadhan
                        { s: 13, a: 14, note: "QS. Ar-Ra'd: 14" }, // hanya kepada Allah doa yang benar
                        // === Tema: Doa-doa Penting dari Al-Quran ===
                        { s: 2, a: 201, note: "QS. Al-Baqarah: 201" }, // doa kebaikan dunia akhirat
                        { s: 2, a: 127, note: "QS. Al-Baqarah: 127" }, // doa Ibrahim: terimalah dari kami
                        { s: 2, a: 128, note: "QS. Al-Baqarah: 128" }, // doa Ibrahim: jadikan kami muslim
                        { s: 2, a: 250, note: "QS. Al-Baqarah: 250" }, // doa menghadapi musuh
                        { s: 2, a: 286, note: "QS. Al-Baqarah: 286" }, // jangan bebankan yang tidak sanggup
                        { s: 3, a: 8, note: "QS. Ali Imran: 8" }, // jangan palingkan hati kami
                        { s: 3, a: 9, note: "QS. Ali Imran: 9" }, // Engkau pengumpul manusia di hari kiamat
                        { s: 3, a: 16, note: "QS. Ali Imran: 16" }, // ampunilah dosa-dosa kami
                        { s: 3, a: 26, note: "QS. Ali Imran: 26" }, // Engkau yang memiliki kerajaan
                        { s: 3, a: 27, note: "QS. Ali Imran: 27" }, // memasukkan malam ke siang
                        { s: 3, a: 38, note: "QS. Ali Imran: 38" }, // doa Zakariya meminta keturunan
                        { s: 3, a: 53, note: "QS. Ali Imran: 53" }, // doa memohon ampunan
                        { s: 3, a: 147, note: "QS. Ali Imran: 147" }, // doa keteguhan di medan perang
                        { s: 3, a: 191, note: "QS. Ali Imran: 191" }, // mengingat Allah dalam segala keadaan
                        { s: 3, a: 192, note: "QS. Ali Imran: 192" }, // siapa yang Engkau masukkan ke neraka
                        { s: 3, a: 193, note: "QS. Ali Imran: 193" }, // kami mendengar seruan untuk beriman
                        { s: 3, a: 194, note: "QS. Ali Imran: 194" }, // berikanlah apa yang Engkau janjikan
                        // === Tema: Doa Para Nabi ===
                        { s: 21, a: 83, note: "QS. Al-Anbiya: 83" }, // doa Ayyub: aku ditimpa penyakit
                        { s: 21, a: 87, note: "QS. Al-Anbiya: 87" }, // doa Yunus: tidak ada Tuhan selain Engkau
                        { s: 21, a: 89, note: "QS. Al-Anbiya: 89" }, // doa Zakariya: jangan biarkan aku sendiri
                        { s: 14, a: 40, note: "QS. Ibrahim: 40" }, // doa Ibrahim menegakkan shalat
                        { s: 14, a: 41, note: "QS. Ibrahim: 41" }, // doa ampunan untuk orangtua
                        { s: 25, a: 74, note: "QS. Al-Furqan: 74" }, // doa hamba Rahman
                        { s: 28, a: 24, note: "QS. Al-Qasas: 24" }, // doa Musa: aku sangat membutuhkan kebaikan
                        { s: 20, a: 25, note: "QS. Taha: 25" }, // doa Musa: lapangkan dadaku
                        { s: 20, a: 26, note: "QS. Taha: 26" }, // mudahkanlah urusanku
                        { s: 20, a: 114, note: "QS. Taha: 114" }, // Ya Tuhanku tambahkan ilmu
                        { s: 46, a: 15, note: "QS. Al-Ahqaf: 15" }, // doa berbakti kepada orangtua
                        { s: 23, a: 97, note: "QS. Al-Mu'minun: 97" }, // berlindung dari godaan setan
                        { s: 23, a: 98, note: "QS. Al-Mu'minun: 98" }, // berlindung dari kehadiran setan
                        { s: 23, a: 118, note: "QS. Al-Mu'minun: 118" }, // Engkau Maha Pengasih
                        { s: 59, a: 10, note: "QS. Al-Hasyr: 10" }, // jangan biarkan dengki dalam hati
                        // === Tema: Doa Keselamatan & Perlindungan ===
                        { s: 1, a: 6, note: "QS. Al-Fatihah: 6" }, // tunjukilah kami jalan yang lurus
                        { s: 1, a: 7, note: "QS. Al-Fatihah: 7" }, // jalan orang yang Engkau beri nikmat
                        { s: 113, a: 1, note: "QS. Al-Falaq: 1" }, // aku berlindung kepada Tuhan yang menguasai subuh
                        { s: 114, a: 1, note: "QS. An-Nas: 1" }, // aku berlindung kepada Tuhannya manusia
                        { s: 10, a: 85, note: "QS. Yunus: 85" }, // kepada Allah kami bertawakkal
                        { s: 10, a: 86, note: "QS. Yunus: 86" }, // selamatkanlah kami dari kaum yang zalim
                    ],
                },
                berbuka: {
                    label: "Waktu Berbuka",
                    refs: [
                        // === Tema: Ramadhan & Puasa ===
                        { s: 2, a: 185, note: "QS. Al-Baqarah: 185" }, // Al-Quran diturunkan di Ramadhan
                        { s: 2, a: 187, note: "QS. Al-Baqarah: 187" }, // makan minum sampai fajar
                        // === Tema: Makan & Minum yang Halal ===
                        { s: 2, a: 168, note: "QS. Al-Baqarah: 168" }, // makanlah yang halal dari bumi
                        { s: 2, a: 172, note: "QS. Al-Baqarah: 172" }, // makanlah yang baik-baik
                        { s: 5, a: 4, note: "QS. Al-Ma'idah: 4" }, // dihalalkan makanan yang baik
                        { s: 5, a: 6, note: "QS. Al-Ma'idah: 6" }, // menyempurnakan nikmat
                        { s: 5, a: 88, note: "QS. Al-Ma'idah: 88" }, // makanlah yang halal lagi baik
                        { s: 6, a: 141, note: "QS. Al-An'am: 141" }, // makan buahnya dan tunaikan haknya
                        { s: 6, a: 142, note: "QS. Al-An'am: 142" }, // makanlah dari rezeki Allah
                        { s: 7, a: 31, note: "QS. Al-A'raf: 31" }, // makan minum jangan berlebihan
                        { s: 7, a: 32, note: "QS. Al-A'raf: 32" }, // perhiasan dan makanan yang baik
                        { s: 16, a: 114, note: "QS. An-Nahl: 114" }, // makanlah rezeki yang halal
                        { s: 23, a: 51, note: "QS. Al-Mu'minun: 51" }, // makanlah yang baik dan beramal salehlah
                        { s: 67, a: 15, note: "QS. Al-Mulk: 15" }, // berjalan dan makan dari rezekinya
                        // === Tema: Syukur Nikmat ===
                        { s: 14, a: 7, note: "QS. Ibrahim: 7" }, // bersyukur niscaya ditambah
                        { s: 14, a: 34, note: "QS. Ibrahim: 34" }, // memberi segala yang kamu minta
                        { s: 16, a: 18, note: "QS. An-Nahl: 18" }, // jika kamu menghitung nikmat Allah
                        { s: 16, a: 53, note: "QS. An-Nahl: 53" }, // segala nikmat dari Allah
                        { s: 16, a: 78, note: "QS. An-Nahl: 78" }, // Allah memberi pendengaran, penglihatan, hati
                        { s: 27, a: 40, note: "QS. An-Naml: 40" }, // ujian bersyukur atau kufur
                        { s: 28, a: 73, note: "QS. Al-Qasas: 73" }, // rahmat-Nya malam dan siang
                        { s: 29, a: 17, note: "QS. Al-Ankabut: 17" }, // mintalah rezeki dari Allah
                        { s: 31, a: 12, note: "QS. Luqman: 12" }, // bersyukurlah kepada Allah
                        { s: 34, a: 15, note: "QS. Saba: 15" }, // negeri yang baik dan Tuhan yang pengampun
                        { s: 35, a: 3, note: "QS. Fatir: 3" }, // ingatlah nikmat Allah kepadamu
                        { s: 40, a: 64, note: "QS. Ghafir: 64" }, // bumi sebagai tempat menetap
                        { s: 45, a: 12, note: "QS. Al-Jasiyah: 12" }, // Allah menundukkan laut untukmu
                        { s: 45, a: 13, note: "QS. Al-Jasiyah: 13" }, // menundukkan langit dan bumi untukmu
                        // === Tema: Nikmat Ar-Rahman ===
                        { s: 55, a: 1, note: "QS. Ar-Rahman: 1" }, // Ar-Rahman
                        { s: 55, a: 2, note: "QS. Ar-Rahman: 2" }, // yang mengajarkan Al-Quran
                        { s: 55, a: 3, note: "QS. Ar-Rahman: 3" }, // menciptakan manusia
                        { s: 55, a: 4, note: "QS. Ar-Rahman: 4" }, // mengajarkan pandai berbicara
                        { s: 55, a: 13, note: "QS. Ar-Rahman: 13" }, // nikmat Tuhan mana yang kau dustakan
                        { s: 55, a: 60, note: "QS. Ar-Rahman: 60" }, // balasan kebaikan adalah kebaikan
                        { s: 56, a: 68, note: "QS. Al-Waqi'ah: 68" }, // pernahkah kamu memperhatikan air yang kamu minum
                        { s: 56, a: 69, note: "QS. Al-Waqi'ah: 69" }, // kamukah yang menurunkan dari awan
                        { s: 80, a: 24, note: "QS. Abasa: 24" }, // hendaklah manusia memperhatikan makanannya
                    ],
                },
                malam: {
                    label: "Malam Hari",
                    refs: [
                        // === Tema: Lailatul Qadr ===
                        { s: 97, a: 1, note: "QS. Al-Qadr: 1" }, // lailatul qadr
                        { s: 97, a: 2, note: "QS. Al-Qadr: 2" }, // tahukah kamu apa lailatul qadr
                        { s: 97, a: 3, note: "QS. Al-Qadr: 3" }, // lebih baik dari seribu bulan
                        { s: 97, a: 4, note: "QS. Al-Qadr: 4" }, // malaikat turun
                        { s: 97, a: 5, note: "QS. Al-Qadr: 5" }, // sejahtera hingga terbit fajar
                        { s: 44, a: 3, note: "QS. Ad-Dukhan: 3" }, // Kami menurunkannya pada malam yang diberkahi
                        { s: 44, a: 4, note: "QS. Ad-Dukhan: 4" }, // pada malam itu dijelaskan segala urusan
                        // === Tema: Qiyamul Lail & Tahajjud ===
                        { s: 17, a: 79, note: "QS. Al-Isra: 79" }, // tahajjud
                        { s: 17, a: 78, note: "QS. Al-Isra: 78" }, // shalat dari matahari tergelincir
                        { s: 73, a: 1, note: "QS. Al-Muzzammil: 1" }, // wahai orang yang berselimut
                        { s: 73, a: 2, note: "QS. Al-Muzzammil: 2" }, // bangunlah di malam hari
                        { s: 73, a: 3, note: "QS. Al-Muzzammil: 3" }, // separuhnya atau kurangi sedikit
                        { s: 73, a: 4, note: "QS. Al-Muzzammil: 4" }, // bacalah Al-Quran dengan tartil
                        { s: 73, a: 6, note: "QS. Al-Muzzammil: 6" }, // bangun malam lebih kuat
                        { s: 73, a: 20, note: "QS. Al-Muzzammil: 20" }, // bacalah Al-Quran
                        { s: 39, a: 9, note: "QS. Az-Zumar: 9" }, // yang beribadah di waktu malam
                        { s: 25, a: 64, note: "QS. Al-Furqan: 64" }, // yang bermalam dengan bersujud
                        { s: 25, a: 63, note: "QS. Al-Furqan: 63" }, // hamba yang berjalan rendah hati
                        { s: 32, a: 16, note: "QS. As-Sajdah: 16" }, // lambung jauh dari tempat tidur
                        { s: 32, a: 17, note: "QS. As-Sajdah: 17" }, // tidak seorangpun tahu apa yang disembunyikan
                        { s: 51, a: 17, note: "QS. Adz-Dzariyat: 17" }, // sedikit sekali tidur di waktu malam
                        { s: 51, a: 18, note: "QS. Adz-Dzariyat: 18" }, // memohon ampun di waktu sahur
                        // === Tema: Tasbih & Dzikir Malam ===
                        { s: 76, a: 25, note: "QS. Al-Insan: 25" }, // sebutlah nama Tuhanmu pagi dan petang
                        { s: 76, a: 26, note: "QS. Al-Insan: 26" }, // sujudlah dan bertasbihlah
                        { s: 52, a: 49, note: "QS. At-Tur: 49" }, // bertasbihlah di waktu malam
                        { s: 50, a: 40, note: "QS. Qaf: 40" }, // bertasbihlah setelah sujud
                        { s: 87, a: 1, note: "QS. Al-A'la: 1" }, // sucikanlah nama Tuhanmu
                        { s: 87, a: 14, note: "QS. Al-A'la: 14" }, // beruntung orang yang menyucikan diri
                        { s: 87, a: 15, note: "QS. Al-A'la: 15" }, // menyebut nama Tuhannya lalu shalat
                        // === Tema: Keagungan Allah & Alam Semesta ===
                        { s: 2, a: 255, note: "QS. Al-Baqarah: 255" }, // Ayat Kursi
                        { s: 59, a: 22, note: "QS. Al-Hasyr: 22" }, // mengetahui yang ghaib dan nyata
                        { s: 59, a: 23, note: "QS. Al-Hasyr: 23" }, // tiada Tuhan selain Dia
                        { s: 59, a: 24, note: "QS. Al-Hasyr: 24" }, // Dialah Allah, Pencipta
                        { s: 6, a: 162, note: "QS. Al-An'am: 162" }, // shalatku, ibadahku, hidupku, matiku
                        { s: 6, a: 163, note: "QS. Al-An'am: 163" }, // tidak ada sekutu bagi-Nya
                        { s: 3, a: 113, note: "QS. Ali Imran: 113" }, // membaca ayat di waktu malam
                        { s: 3, a: 114, note: "QS. Ali Imran: 114" }, // beriman dan berlomba kebaikan
                        { s: 3, a: 190, note: "QS. Ali Imran: 190" }, // penciptaan langit dan bumi
                        { s: 3, a: 191, note: "QS. Ali Imran: 191" }, // mengingat Allah dalam segala keadaan
                        { s: 67, a: 1, note: "QS. Al-Mulk: 1" }, // Maha Suci yang memiliki kerajaan
                        { s: 67, a: 2, note: "QS. Al-Mulk: 2" }, // menciptakan mati dan hidup untuk menguji
                        { s: 67, a: 3, note: "QS. Al-Mulk: 3" }, // menciptakan tujuh langit berlapis
                        { s: 36, a: 36, note: "QS. Yasin: 36" }, // Maha Suci yang menciptakan berpasang-pasangan
                        { s: 36, a: 40, note: "QS. Yasin: 40" }, // matahari tidak bisa mendahului bulan
                        { s: 112, a: 1, note: "QS. Al-Ikhlas: 1" }, // katakanlah Dialah Allah Yang Maha Esa
                        { s: 112, a: 2, note: "QS. Al-Ikhlas: 2" }, // Allahush-Shamad
                    ],
                },
            };
        },

        async fetchContextualVerse() {
            const context = this.getVerseContext();
            const pool = this.getContextualVersePool();
            const group = pool[context] || pool.siang;

            // Pick a random verse from the pool
            const pick =
                group.refs[Math.floor(Math.random() * group.refs.length)];

            this.dailyVerse = {
                text: "Memuat ayat...",
                arabic: "",
                source: pick.note,
                contextLabel: group.label,
                loading: true,
            };

            try {
                // Fetch both Arabic and Indonesian translations in parallel
                const [arRes, idRes] = await Promise.all([
                    fetch(
                        `https://api.alquran.cloud/v1/ayah/${pick.s}:${pick.a}/ar.alafasy`,
                    ),
                    fetch(
                        `https://api.alquran.cloud/v1/ayah/${pick.s}:${pick.a}/id.indonesian`,
                    ),
                ]);

                const arData = await arRes.json();
                const idData = await idRes.json();

                if (arData.code === 200 && idData.code === 200) {
                    this.dailyVerse = {
                        arabic: arData.data.text,
                        text: '"' + idData.data.text + '"',
                        source: pick.note,
                        contextLabel: group.label,
                        loading: false,
                    };
                } else {
                    this._fallbackVerse(group.label);
                }
            } catch (err) {
                console.warn("Quran API failed, using fallback", err);
                this._fallbackVerse(group.label);
            }
        },

        _fallbackVerse(contextLabel) {
            const fallbacks = [
                {
                    text: '"Sesungguhnya bersama kesulitan ada kemudahan."',
                    source: "QS. Al-Insyirah: 5-6",
                },
                {
                    text: '"Hai orang-orang yang beriman, diwajibkan atas kamu berpuasa."',
                    source: "QS. Al-Baqarah: 183",
                },
                {
                    text: '"Dan apabila hamba-hamba-Ku bertanya tentang Aku, maka sesungguhnya Aku dekat."',
                    source: "QS. Al-Baqarah: 186",
                },
                {
                    text: '"Bulan Ramadhan adalah bulan yang di dalamnya diturunkan Al-Quran."',
                    source: "QS. Al-Baqarah: 185",
                },
                {
                    text: '"Sesungguhnya Allah tidak akan mengubah nasib suatu kaum hingga mereka mengubah diri mereka sendiri."',
                    source: "QS. Ar-Ra'd: 11",
                },
            ];
            const pick =
                fallbacks[Math.floor(Math.random() * fallbacks.length)];
            this.dailyVerse = {
                arabic: "",
                text: pick.text,
                source: pick.source,
                contextLabel: contextLabel || "Ayat Hari Ini",
                loading: false,
            };
        },

        _gpsWatchId: null,

        // ── Location ───────────────────────────────────────────────────────
        getLocation() {
            this.locationText = "Mendeteksi lokasi...";
            this.locationCity = "Mendeteksi lokasi...";
            this.locationCoords = "";
            if (!navigator.geolocation) {
                this.setDefaultLocation();
                return;
            }
            // Clear any previous watch
            if (this._gpsWatchId !== null) {
                navigator.geolocation.clearWatch(this._gpsWatchId);
                this._gpsWatchId = null;
            }
            // Use getCurrentPosition (fires once) to avoid name flickering
            // as watchPosition keeps re-triggering with improving accuracy
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.userLat = pos.coords.latitude;
                    this.userLng = pos.coords.longitude;
                    this.gpsAccuracy = pos.coords.accuracy
                        ? Math.round(pos.coords.accuracy)
                        : null;
                    this._updateGpsQuality();
                    this.locationCoords =
                        this.userLat.toFixed(4) +
                        ", " +
                        this.userLng.toFixed(4);
                    this.locationText =
                        "Lat: " +
                        this.userLat.toFixed(4) +
                        ", Lng: " +
                        this.userLng.toFixed(4);
                    const tzInfo = this.getTimezoneForLng(this.userLng);
                    this.selectedTz = tzInfo.tz;
                    this.calculateQibla();
                    this.calculatePrayerTimes();
                    // Always use Nominatim reverse geocoding for accurate
                    // administrative boundary names (avoids nearest-centroid errors)
                    fetch(
                        "https://nominatim.openstreetmap.org/reverse?lat=" +
                            this.userLat +
                            "&lon=" +
                            this.userLng +
                            "&format=json&accept-language=id&zoom=10",
                    )
                        .then((r) => r.json())
                        .then((d) => {
                            const addr = d.address || {};
                            const kecamatan =
                                addr.suburb ||
                                addr.city_district ||
                                addr.village ||
                                addr.town ||
                                "";
                            const kabupaten =
                                addr.county ||
                                addr.city ||
                                addr.state_district ||
                                addr.town ||
                                addr.state ||
                                "Lokasi Anda";
                            const provinsi = addr.state || "";
                            const cleanKab = kabupaten
                                .replace(/^Kabupaten\s+/i, "Kab. ")
                                .replace(/^Kota\s+/i, "Kota ");
                            if (kecamatan) {
                                this.locationCity = kecamatan + ", " + cleanKab;
                                this.cityName = kecamatan;
                            } else {
                                this.locationCity = provinsi
                                    ? cleanKab + ", " + provinsi
                                    : cleanKab;
                                this.cityName = cleanKab;
                            }
                            this.locationText =
                                this.locationCity +
                                (provinsi ? ", " + provinsi : "");
                            this.saveLocation();
                        })
                        .catch(() => {
                            // Fallback: nearest from JSON if Nominatim fails
                            const nearest = this._findNearestLocation(
                                this.userLat,
                                this.userLng,
                            );
                            if (nearest) {
                                const displayKec =
                                    nearest.kecamatan || nearest.kabupaten;
                                this.locationCity =
                                    displayKec + ", " + nearest.kabupaten;
                                this.cityName = displayKec;
                                this.locationText =
                                    displayKec + ", " + nearest.provinsi;
                            } else {
                                this.locationCity = this.locationCoords;
                            }
                            this.saveLocation();
                        });
                },
                () => {
                    this.setDefaultLocation();
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 },
            );
        },

        setDefaultLocation() {
            this.locationText = "Ciamis, Jawa Barat (default)";
            this.locationCity = "Kab. Ciamis, Jawa Barat";
            this.locationCoords = "-7.3305, 108.3508";
            this.userLat = -7.3305;
            this.userLng = 108.3508;
            this.cityName = "Kab. Ciamis";
            this.selectedTz = "WIB";
            this.calculateQibla();
            this.calculatePrayerTimes();
            this.saveLocation();
        },

        useGPS() {
            this.showLocationPicker = false;
            // Clear saved location so fresh GPS result is persisted
            localStorage.removeItem("ramadhan_location");
            this.getLocation();
        },

        openLocationPicker() {
            this.locationSearch = "";
            this.filteredLocations = this.indonesiaLocations;
            this.showLocationPicker = true;
            this.$nextTick(() => {
                const inp = document.querySelector(
                    ".location-dropdown-search input",
                );
                if (inp) inp.focus();
            });
        },

        filterLocations() {
            const q = this.locationSearch.toLowerCase();
            this.filteredLocations = q
                ? this.indonesiaLocations.filter(
                      (l) =>
                          (l.kecamatan &&
                              l.kecamatan.toLowerCase().includes(q)) ||
                          l.kabupaten.toLowerCase().includes(q) ||
                          l.provinsi.toLowerCase().includes(q),
                  )
                : this.indonesiaLocations;
        },

        selectLocation(loc) {
            this.userLat = loc.lat;
            this.userLng = loc.lng;
            this.locationCoords =
                loc.lat.toFixed(4) + ", " + loc.lng.toFixed(4);
            const displayName = loc.kecamatan
                ? loc.kecamatan + ", " + loc.kabupaten
                : loc.kabupaten;
            this.locationCity = displayName + ", " + loc.provinsi;
            this.cityName = loc.kecamatan ? loc.kecamatan : loc.kabupaten;
            this.locationText = displayName + ", " + loc.provinsi;
            const tzInfo = this.getTimezoneForLng(loc.lng);
            this.selectedTz = tzInfo.tz;
            this.calculateQibla();
            this.calculatePrayerTimes();
            this.saveLocation();
            this.showLocationPicker = false;
            this.locationSearch = "";
            this.filteredLocations = this.indonesiaLocations;
        },

        // ── Location persistence ────────────────────────────────────────
        saveLocation() {
            try {
                localStorage.setItem(
                    "ramadhan_location",
                    JSON.stringify({
                        lat: this.userLat,
                        lng: this.userLng,
                        city: this.locationCity,
                        coords: this.locationCoords,
                        name: this.cityName,
                        text: this.locationText,
                        tz: this.selectedTz,
                    }),
                );
            } catch (e) {}
        },

        // Returns true if a saved location was successfully restored
        loadSavedLocation() {
            try {
                const raw = localStorage.getItem("ramadhan_location");
                if (!raw) return false;
                const s = JSON.parse(raw);
                if (!s || !s.lat || !s.lng) return false;
                this.userLat = s.lat;
                this.userLng = s.lng;
                this.locationCity = s.city || "";
                this.locationCoords = s.coords || "";
                this.cityName = s.name || "";
                this.locationText = s.text || "";
                this.selectedTz = s.tz || "WIB";
                this.calculateQibla();
                this.calculatePrayerTimes();
                return true;
            } catch (e) {
                return false;
            }
        },

        // ── Qibla ──────────────────────────────────────────────────────────
        calculateQibla() {
            const kLat = 21.4225,
                kLng = 39.8262;
            const lat1 = (this.userLat * Math.PI) / 180;
            const lat2 = (kLat * Math.PI) / 180;
            const dLng = ((kLng - this.userLng) * Math.PI) / 180;
            const y = Math.sin(dLng) * Math.cos(lat2);
            const x =
                Math.cos(lat1) * Math.sin(lat2) -
                Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLng);
            this.qiblaDirection =
                ((Math.atan2(y, x) * 180) / Math.PI + 360) % 360;
            this.qiblaStatus =
                "Arah " + this.qiblaDirection.toFixed(1) + "° dari utara";
            // Calculate distance to Kaaba (Haversine)
            const R = 6371;
            const dLat = ((kLat - this.userLat) * Math.PI) / 180;
            const dLon = ((kLng - this.userLng) * Math.PI) / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1) *
                    Math.cos(lat2) *
                    Math.sin(dLon / 2) *
                    Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            this.distanceToKaaba = Math.round(R * c);
        },

        // ── Compass / Device Orientation ────────────────────────────────────
        _compassHandler: null,
        _compassAbsHandler: null,
        _hasAbsolute: false,

        initCompass() {
            // Try absolute orientation first (Android Chrome)
            if ("ondeviceorientationabsolute" in window) {
                this.compassSupported = true;
                this.compassPermission = "granted";
                this._startAbsoluteCompassListener();
                return;
            }
            // Check support
            if (window.DeviceOrientationEvent) {
                this.compassSupported = true;
                // iOS 13+ requires permission
                if (
                    typeof DeviceOrientationEvent.requestPermission ===
                    "function"
                ) {
                    this.compassPermission = "unknown";
                } else {
                    // Android / other â€” auto-granted
                    this.compassPermission = "granted";
                    this._startCompassListener();
                }
            } else {
                this.compassSupported = false;
                this.compassPermission = "unsupported";
            }
        },

        async requestCompassPermission() {
            if (
                typeof DeviceOrientationEvent.requestPermission === "function"
            ) {
                try {
                    const perm =
                        await DeviceOrientationEvent.requestPermission();
                    if (perm === "granted") {
                        this.compassPermission = "granted";
                        this._startCompassListener();
                    } else {
                        this.compassPermission = "denied";
                    }
                } catch (e) {
                    this.compassPermission = "denied";
                }
            }
        },

        _startAbsoluteCompassListener() {
            this._compassAbsHandler = (e) => {
                if (e.alpha !== null) {
                    this._hasAbsolute = true;
                    const heading = (360 - e.alpha) % 360;
                    this._applyHeading(heading);
                }
            };
            window.addEventListener(
                "deviceorientationabsolute",
                this._compassAbsHandler,
                true,
            );
        },

        _startCompassListener() {
            this._compassHandler = (e) => {
                // Skip if we already have absolute readings
                if (this._hasAbsolute) return;
                let heading = null;
                // iOS: webkitCompassHeading is degrees from magnetic north
                if (e.webkitCompassHeading !== undefined) {
                    heading = e.webkitCompassHeading;
                    this.compassAccuracy = e.webkitCompassAccuracy || null;
                }
                // Android: alpha = rotation around z-axis (0-360)
                else if (e.alpha !== null) {
                    heading = (360 - e.alpha) % 360;
                }
                if (heading !== null) {
                    this._applyHeading(heading);
                }
            };
            window.addEventListener(
                "deviceorientation",
                this._compassHandler,
                true,
            );
        },

        _applyHeading(heading) {
            // Smooth the compass reading with weighted average
            const diff = heading - this.compassHeading;
            const shortDiff = ((diff + 540) % 360) - 180;
            this.compassHeading =
                (this.compassHeading + shortDiff * 0.2 + 360) % 360;
            this.compassActive = true;
        },

        stopCompass() {
            if (this._compassHandler) {
                window.removeEventListener(
                    "deviceorientation",
                    this._compassHandler,
                    true,
                );
                this._compassHandler = null;
            }
            if (this._compassAbsHandler) {
                window.removeEventListener(
                    "deviceorientationabsolute",
                    this._compassAbsHandler,
                    true,
                );
                this._compassAbsHandler = null;
            }
            this.compassActive = false;
        },

        _updateGpsQuality() {
            if (!this.gpsAccuracy) {
                this.gpsQuality = "detecting";
                return;
            }
            if (this.gpsAccuracy <= 10) this.gpsQuality = "excellent";
            else if (this.gpsAccuracy <= 50) this.gpsQuality = "good";
            else if (this.gpsAccuracy <= 200) this.gpsQuality = "fair";
            else if (this.gpsAccuracy <= 5000) this.gpsQuality = "poor";
            else this.gpsQuality = "ip-based";
        },

        get gpsQualityLabel() {
            const labels = {
                detecting: "Mendeteksi...",
                excellent: "Sangat akurat",
                good: "Akurat",
                fair: "Cukup akurat",
                poor: "Kurang akurat",
                "ip-based": "Perkiraan (non-GPS)",
            };
            return labels[this.gpsQuality] || "Mendeteksi...";
        },

        get gpsQualityColor() {
            const colors = {
                detecting: "#94a3b8",
                excellent: "#16a34a",
                good: "#22c55e",
                fair: "#eab308",
                poor: "#f97316",
                "ip-based": "#ef4444",
            };
            return colors[this.gpsQuality] || "#94a3b8";
        },

        // The angle to rotate the compass dial (so North points to real north)
        get compassRotation() {
            return -this.compassHeading;
        },

        // The angle at which to show the Kaaba indicator on the compass
        // When compass is rotating, Kaaba appears at qiblaDirection - compassHeading
        get qiblaOnCompass() {
            return this.qiblaDirection - this.compassHeading;
        },

        // Cardinal direction label
        get compassCardinal() {
            const dirs = ["U", "TL", "T", "TG", "S", "BD", "B", "BL"];
            const idx = Math.round(this.compassHeading / 45) % 8;
            return dirs[idx];
        },

        get qiblaCardinal() {
            const dirs = [
                "Utara",
                "Timur Laut",
                "Timur",
                "Tenggara",
                "Selatan",
                "Barat Daya",
                "Barat",
                "Barat Laut",
            ];
            const idx = Math.round(this.qiblaDirection / 45) % 8;
            return dirs[idx];
        },

        // ── Form Methods ───────────────────────────────────────────────────
        loadSubmittedDays() {
            try {
                const saved = localStorage.getItem("ramadhan_submitted_days");
                this.submittedDays = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.submittedDays = [];
            }
        },

        checkFormSubmitted() {
            this.formSubmitted = this.submittedDays.includes(this.formDay);
            // Load saved form data for current day
            try {
                const savedForm = localStorage.getItem(
                    "ramadhan_form_day_" + this.formDay,
                );
                if (savedForm) {
                    this.formData = JSON.parse(savedForm);
                }
            } catch (e) {}
        },

        resetFormData() {
            this.formData = {
                puasa: "",
                sholat_dzuhur_j: false,
                sholat_dzuhur_m: false,
                sholat_ashar_j: false,
                sholat_ashar_m: false,
                sholat_maghrib_j: false,
                sholat_maghrib_m: false,
                sholat_isya_j: false,
                sholat_isya_m: false,
                sholat_subuh_j: false,
                sholat_subuh_m: false,
                tarawih_j: false,
                tarawih_m: false,
                rowatib: "",
                tahajud: "",
                dhuha: "",
                tadarus_surat: "",
                tadarus_ayat: "",
                kegiatan: {
                    dzikir_pagi: false,
                    olahraga: false,
                    membantu_ortu: false,
                    membersihkan_kamar: false,
                    membersihkan_rumah: false,
                    membersihkan_halaman: false,
                    merawat_lingkungan: false,
                    dzikir_petang: false,
                    sedekah: false,
                    buka_keluarga: false,
                    literasi: false,
                    menabung: false,
                    tidur_cepat: false,
                    bangun_pagi: false,
                },
                ringkasan_ceramah: "",
            };
        },

        submitForm() {
            this.formSaving = true;
            // Save form data locally
            localStorage.setItem(
                "ramadhan_form_day_" + this.formDay,
                JSON.stringify(this.formData),
            );
            // Mark day as submitted
            if (!this.submittedDays.includes(this.formDay)) {
                this.submittedDays.push(this.formDay);
                localStorage.setItem(
                    "ramadhan_submitted_days",
                    JSON.stringify(this.submittedDays),
                );
            }
            this.formSubmitted = true;
            this.buildCalendar(); // refresh calendar progress
            setTimeout(() => {
                this.formSaving = false;
            }, 500);
        },

        editForm() {
            this.formSubmitted = false;
        },

        syncFromServer() {
            var self = this;
            fetch("/api/formulir", { headers: { Accept: "application/json" } })
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    if (data.success && data.submitted_days) {
                        data.submitted_days.forEach(function (day) {
                            if (!self.submittedDays.includes(day)) {
                                self.submittedDays.push(day);
                            }
                        });
                        localStorage.setItem(
                            "ramadhan_submitted_days",
                            JSON.stringify(self.submittedDays),
                        );
                        if (data.submissions) {
                            data.submissions.forEach(function (sub) {
                                self.submissionStatuses[sub.hari_ke] = {
                                    status: sub.status || "pending",
                                    catatan_guru: sub.catatan_guru || "",
                                };
                            });
                        }
                        self.buildCalendar();
                    }
                })
                .catch(function () {});
        },

        getProgressPercent() {
            return Math.round((this.submittedDays.length / 30) * 100);
        },
    };
}
