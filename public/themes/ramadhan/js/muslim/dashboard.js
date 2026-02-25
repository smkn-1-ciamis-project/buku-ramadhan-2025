// ── API Throttle Helper ─────────────────────────────────────────────
var _apiLastCall = {};
function _throttledFetch(key, url, options, cooldownMs) {
    cooldownMs = cooldownMs || 5000;
    var now = Date.now();
    if (_apiLastCall[key] && now - _apiLastCall[key] < cooldownMs) {
        return Promise.reject({ throttled: true });
    }
    _apiLastCall[key] = now;
    return fetch(url, options).then(function (r) {
        if (r.status === 429) {
            return r.json().then(function (d) {
                return Promise.reject({
                    rateLimited: true,
                    message:
                        d.message ||
                        "Terlalu banyak permintaan. Tunggu sebentar.",
                });
            });
        }
        return r;
    });
}

function ramadhanDashboard() {
    return {
        activeTab: "calendar",
        showChangePassword: false,
        showLogoutConfirm: false,
        pwOld: "",
        pwNew: "",
        pwConfirm: "",
        pwLoading: false,
        pwMessage: "",
        pwSuccess: false,
        prayerTimes: [],
        fullPrayerSchedule: [],
        _rawPrayerTimes: null,
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
        compassHeading: 0,
        compassActive: false,
        compassSupported: false,
        compassPermission: "unknown",
        compassAccuracy: null,
        distanceToKaaba: 0,
        gpsAccuracy: null,
        gpsQuality: "detecting",
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
            { id: "shalat", label: "Check-in Shalat", mobileLabel: "Shalat" },
            { id: "schedule", label: "Jadwal Sholat", mobileLabel: "Jadwal" },
            { id: "qibla", label: "Arah Kiblat", mobileLabel: "Kiblat" },
            { id: "dua", label: "Doa Harian", mobileLabel: "Doa" },
            { id: "account", label: "Pengaturan Akun", mobileLabel: "Akun" },
        ],
        checkinData: {},
        checkinDate: "",
        checkinHariKe: 1,
        checkinAllFilled: false,
        checkinDateLabel: "",
        checkinLoading: true,
        showCheckinModal: false,
        checkinModalPrayer: null,
        checkinSaving: false,
        checkinWajib: [
            { id: "subuh", name: "Subuh", tipe: "wajib" },
            { id: "dzuhur", name: "Dzuhur", tipe: "wajib" },
            { id: "ashar", name: "Ashar", tipe: "wajib" },
            { id: "maghrib", name: "Maghrib", tipe: "wajib" },
            { id: "isya", name: "Isya", tipe: "wajib" },
            { id: "tarawih", name: "Tarawih", tipe: "wajib" },
        ],
        checkinSunnah: [
            { id: "rowatib", name: "Rowatib", tipe: "sunnah" },
            { id: "tahajud", name: "Tahajud", tipe: "sunnah" },
            { id: "dhuha", name: "Dhuha", tipe: "sunnah" },
        ],
        showNotifModal: false,
        notifTitle: "",
        notifMessage: "",
        notifRedirectUrl: "",
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
            tadarus_entries: [{ surat: "", ayat: "" }],
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
        init() {
            this.setDates();
            this.calculateRamadhanDay();
            this.formDay = this.ramadhanDay;
            this.loadSubmittedDays();
            this.checkFormSubmitted();
            this.setPrayerTimes();
            this.buildCalendar();
            this.syncFromServer();
            this.setDailyVerse();
            if (!this.loadSavedLocation()) {
                this.getLocation();
            }
            this.startCountdown();
            this.startClock();
            this.initCompass();
            this.loadCheckins();
        },
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
            if (minDist > 0.032) return null;
            return nearest;
        },
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
            const ramadhanStart = new Date(2026, 1, 19);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            for (let d = 0; d < 30; d++) {
                const date = new Date(ramadhanStart);
                date.setDate(ramadhanStart.getDate() + d);
                const hijriDay = d + 1;
                const masehiDay = date.getDate();
                const isToday = date.getTime() === today.getTime();
                const isCompleted = this.submittedDays.includes(hijriDay);
                const dayStatus = this.submissionStatuses[hijriDay];
                const statusStr = dayStatus ? dayStatus.status : "";
                const kesiswaanStr = dayStatus
                    ? dayStatus.kesiswaan_status || ""
                    : "";
                const isValidated =
                    isCompleted &&
                    statusStr === "verified" &&
                    kesiswaanStr === "validated";
                const isRejected =
                    isCompleted &&
                    (statusStr === "rejected" || kesiswaanStr === "rejected");
                const isVerified =
                    isCompleted &&
                    statusStr === "verified" &&
                    !isValidated &&
                    !isRejected;
                const isPending =
                    isCompleted &&
                    (statusStr === "pending" || statusStr === "");
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
                    isValidated: isValidated,
                    isVerified: isVerified,
                    isPending: isPending,
                    isRejected: isRejected,
                    isPast: isPast,
                    isPastUnfilled: isPastUnfilled,
                    dateObj: date,
                });
            }
            const firstDow = days[0].dayOfWeek;
            const monBasedDow = (firstDow + 6) % 7;
            const grid = [];
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
            for (const d of days) {
                grid.push(d);
            }
            this.calendarDays = grid;
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
            // Store raw times for check-in time validation
            this._rawPrayerTimes = times;
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
            let ni = 0;
            let found = false;
            for (let i = 0; i < list.length; i++) {
                if (cm < tm(list[i].time)) {
                    ni = i;
                    found = true;
                    break;
                }
            }
            if (!found) {
                ni = 0;
            }
            this.prayerTimes = list.map((p, i) => ({
                ...p,
                isActive: i === ni,
            }));
            this.nextPrayerName = list[ni].name;
            this.nextPrayerMinutes = tm(list[ni].time);
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
            const cacheKey =
                "prayer_" +
                dateStr +
                "_" +
                lat.toFixed(2) +
                "_" +
                lng.toFixed(2);
            const cached = localStorage.getItem(cacheKey);
            if (cached) {
                try {
                    const times = JSON.parse(cached);
                    this.setPrayerTimes(times);
                    this.startCountdown();
                    console.log("[Jadwal Sholat] Dari cache localStorage");
                    return;
                } catch (e) {}
            }
            const url =
                "https://api.aladhan.com/v1/timings/" +
                dateStr +
                "?latitude=" +
                lat +
                "&longitude=" +
                lng +
                "&method=20";
            fetch(url, {
                signal: AbortSignal.timeout
                    ? AbortSignal.timeout(5000)
                    : undefined,
            })
                .then((r) => r.json())
                .then((data) => {
                    if (
                        data &&
                        data.code === 200 &&
                        data.data &&
                        data.data.timings
                    ) {
                        const t = data.data.timings;
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
                        try {
                            localStorage.setItem(
                                cacheKey,
                                JSON.stringify(times),
                            );
                        } catch (e) {}
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
            if (this.activeDoaCategory !== "semua") {
                result = result.filter(
                    (d) => d.category === this.activeDoaCategory,
                );
            }
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
            this.dailyVerse = {
                text: "Memuat ayat...",
                arabic: "",
                source: "",
                contextLabel: "Ayat Hari Ini",
                loading: true,
            };
            this.fetchContextualVerse();
        },
        getVerseContext() {
            const now = new Date();
            const hh = now.getHours();
            const mm = now.getMinutes();
            const nowMin = hh * 60 + mm;
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
        getContextualVersePool() {
            return {
                sahur: {
                    label: "Waktu Sahur",
                    refs: [
                        { s: 2, a: 187, note: "QS. Al-Baqarah: 187" },
                        { s: 97, a: 5, note: "QS. Al-Qadr: 5" },
                        { s: 51, a: 18, note: "QS. Adz-Dzariyat: 18" },
                        { s: 3, a: 17, note: "QS. Ali Imran: 17" },
                        { s: 3, a: 16, note: "QS. Ali Imran: 16" },
                        { s: 3, a: 135, note: "QS. Ali Imran: 135" },
                        { s: 3, a: 147, note: "QS. Ali Imran: 147" },
                        { s: 71, a: 10, note: "QS. Nuh: 10" },
                        { s: 71, a: 11, note: "QS. Nuh: 11" },
                        { s: 39, a: 53, note: "QS. Az-Zumar: 53" },
                        { s: 66, a: 8, note: "QS. At-Tahrim: 8" },
                        { s: 110, a: 3, note: "QS. An-Nasr: 3" },
                        { s: 4, a: 110, note: "QS. An-Nisa: 110" },
                        { s: 11, a: 3, note: "QS. Hud: 3" },
                        { s: 11, a: 90, note: "QS. Hud: 90" },
                        { s: 11, a: 114, note: "QS. Hud: 114" },
                        { s: 4, a: 106, note: "QS. An-Nisa: 106" },
                        { s: 47, a: 19, note: "QS. Muhammad: 19" },
                        { s: 73, a: 2, note: "QS. Al-Muzzammil: 2" },
                        { s: 73, a: 4, note: "QS. Al-Muzzammil: 4" },
                        { s: 73, a: 6, note: "QS. Al-Muzzammil: 6" },
                        { s: 76, a: 26, note: "QS. Al-Insan: 26" },
                        { s: 17, a: 79, note: "QS. Al-Isra: 79" },
                        { s: 32, a: 16, note: "QS. As-Sajdah: 16" },
                        { s: 52, a: 49, note: "QS. At-Tur: 49" },
                        { s: 20, a: 130, note: "QS. Taha: 130" },
                        { s: 50, a: 39, note: "QS. Qaf: 39" },
                        { s: 50, a: 40, note: "QS. Qaf: 40" },
                        { s: 52, a: 48, note: "QS. At-Tur: 48" },
                        { s: 40, a: 55, note: "QS. Ghafir: 55" },
                        { s: 3, a: 41, note: "QS. Ali Imran: 41" },
                        { s: 6, a: 54, note: "QS. Al-An'am: 54" },
                        { s: 39, a: 9, note: "QS. Az-Zumar: 9" },
                        { s: 25, a: 64, note: "QS. Al-Furqan: 64" },
                        { s: 51, a: 17, note: "QS. Adz-Dzariyat: 17" },
                    ],
                },
                pagi: {
                    label: "Pagi Hari",
                    refs: [
                        { s: 94, a: 5, note: "QS. Al-Insyirah: 5" },
                        { s: 94, a: 6, note: "QS. Al-Insyirah: 6" },
                        { s: 94, a: 7, note: "QS. Al-Insyirah: 7" },
                        { s: 93, a: 3, note: "QS. Ad-Dhuha: 3" },
                        { s: 93, a: 4, note: "QS. Ad-Dhuha: 4" },
                        { s: 93, a: 5, note: "QS. Ad-Dhuha: 5" },
                        { s: 93, a: 11, note: "QS. Ad-Dhuha: 11" },
                        { s: 65, a: 3, note: "QS. At-Talaq: 3" },
                        { s: 3, a: 159, note: "QS. Ali Imran: 159" },
                        { s: 8, a: 2, note: "QS. Al-Anfal: 2" },
                        { s: 9, a: 51, note: "QS. At-Taubah: 51" },
                        { s: 14, a: 12, note: "QS. Ibrahim: 12" },
                        { s: 33, a: 3, note: "QS. Al-Ahzab: 3" },
                        { s: 12, a: 67, note: "QS. Yusuf: 67" },
                        { s: 2, a: 153, note: "QS. Al-Baqarah: 153" },
                        { s: 2, a: 286, note: "QS. Al-Baqarah: 286" },
                        { s: 3, a: 139, note: "QS. Ali Imran: 139" },
                        { s: 3, a: 200, note: "QS. Ali Imran: 200" },
                        { s: 8, a: 46, note: "QS. Al-Anfal: 46" },
                        { s: 39, a: 10, note: "QS. Az-Zumar: 10" },
                        { s: 16, a: 96, note: "QS. An-Nahl: 96" },
                        { s: 2, a: 155, note: "QS. Al-Baqarah: 155" },
                        { s: 2, a: 156, note: "QS. Al-Baqarah: 156" },
                        { s: 2, a: 157, note: "QS. Al-Baqarah: 157" },
                        { s: 2, a: 152, note: "QS. Al-Baqarah: 152" },
                        { s: 13, a: 28, note: "QS. Ar-Ra'd: 28" },
                        { s: 33, a: 41, note: "QS. Al-Ahzab: 41" },
                        { s: 33, a: 42, note: "QS. Al-Ahzab: 42" },
                        { s: 57, a: 4, note: "QS. Al-Hadid: 4" },
                        { s: 10, a: 62, note: "QS. Yunus: 62" },
                        { s: 10, a: 63, note: "QS. Yunus: 63" },
                        { s: 29, a: 69, note: "QS. Al-Ankabut: 69" },
                        { s: 29, a: 2, note: "QS. Al-Ankabut: 2" },
                        { s: 29, a: 3, note: "QS. Al-Ankabut: 3" },
                        { s: 47, a: 31, note: "QS. Muhammad: 31" },
                        { s: 21, a: 35, note: "QS. Al-Anbiya: 35" },
                    ],
                },
                siang: {
                    label: "Siang Hari",
                    refs: [
                        { s: 2, a: 183, note: "QS. Al-Baqarah: 183" },
                        { s: 2, a: 184, note: "QS. Al-Baqarah: 184" },
                        { s: 2, a: 185, note: "QS. Al-Baqarah: 185" },
                        { s: 2, a: 197, note: "QS. Al-Baqarah: 197" },
                        { s: 16, a: 97, note: "QS. An-Nahl: 97" },
                        { s: 2, a: 195, note: "QS. Al-Baqarah: 195" },
                        { s: 2, a: 267, note: "QS. Al-Baqarah: 267" },
                        { s: 2, a: 261, note: "QS. Al-Baqarah: 261" },
                        { s: 2, a: 262, note: "QS. Al-Baqarah: 262" },
                        { s: 2, a: 271, note: "QS. Al-Baqarah: 271" },
                        { s: 2, a: 274, note: "QS. Al-Baqarah: 274" },
                        { s: 3, a: 92, note: "QS. Ali Imran: 92" },
                        { s: 57, a: 18, note: "QS. Al-Hadid: 18" },
                        { s: 73, a: 20, note: "QS. Al-Muzzammil: 20" },
                        { s: 76, a: 8, note: "QS. Al-Insan: 8" },
                        { s: 76, a: 9, note: "QS. Al-Insan: 9" },
                        { s: 49, a: 10, note: "QS. Al-Hujurat: 10" },
                        { s: 49, a: 11, note: "QS. Al-Hujurat: 11" },
                        { s: 49, a: 12, note: "QS. Al-Hujurat: 12" },
                        { s: 49, a: 13, note: "QS. Al-Hujurat: 13" },
                        { s: 31, a: 17, note: "QS. Luqman: 17" },
                        { s: 31, a: 18, note: "QS. Luqman: 18" },
                        { s: 31, a: 19, note: "QS. Luqman: 19" },
                        { s: 41, a: 34, note: "QS. Fussilat: 34" },
                        { s: 5, a: 2, note: "QS. Al-Ma'idah: 2" },
                        { s: 5, a: 8, note: "QS. Al-Ma'idah: 8" },
                        { s: 60, a: 8, note: "QS. Al-Mumtahanah: 8" },
                        { s: 16, a: 90, note: "QS. An-Nahl: 90" },
                        { s: 103, a: 2, note: "QS. Al-Asr: 2" },
                        { s: 103, a: 3, note: "QS. Al-Asr: 3" },
                        { s: 13, a: 11, note: "QS. Ar-Ra'd: 11" },
                        { s: 58, a: 11, note: "QS. Al-Mujadalah: 11" },
                        { s: 20, a: 114, note: "QS. Taha: 114" },
                        { s: 39, a: 9, note: "QS. Az-Zumar: 9" },
                        { s: 96, a: 1, note: "QS. Al-Alaq: 1" },
                        { s: 96, a: 3, note: "QS. Al-Alaq: 3" },
                        { s: 96, a: 4, note: "QS. Al-Alaq: 4" },
                        { s: 96, a: 5, note: "QS. Al-Alaq: 5" },
                        { s: 23, a: 1, note: "QS. Al-Mu'minun: 1" },
                        { s: 23, a: 2, note: "QS. Al-Mu'minun: 2" },
                        { s: 23, a: 3, note: "QS. Al-Mu'minun: 3" },
                        { s: 23, a: 4, note: "QS. Al-Mu'minun: 4" },
                        { s: 23, a: 8, note: "QS. Al-Mu'minun: 8" },
                        { s: 8, a: 2, note: "QS. Al-Anfal: 2" },
                        { s: 8, a: 3, note: "QS. Al-Anfal: 3" },
                        { s: 8, a: 4, note: "QS. Al-Anfal: 4" },
                    ],
                },
                sore: {
                    label: "Menjelang Berbuka",
                    refs: [
                        { s: 2, a: 186, note: "QS. Al-Baqarah: 186" },
                        { s: 40, a: 60, note: "QS. Ghafir: 60" },
                        { s: 27, a: 62, note: "QS. An-Naml: 62" },
                        { s: 42, a: 26, note: "QS. Asy-Syura: 26" },
                        { s: 7, a: 55, note: "QS. Al-A'raf: 55" },
                        { s: 7, a: 56, note: "QS. Al-A'raf: 56" },
                        { s: 2, a: 185, note: "QS. Al-Baqarah: 185" },
                        { s: 13, a: 14, note: "QS. Ar-Ra'd: 14" },
                        { s: 2, a: 201, note: "QS. Al-Baqarah: 201" },
                        { s: 2, a: 127, note: "QS. Al-Baqarah: 127" },
                        { s: 2, a: 128, note: "QS. Al-Baqarah: 128" },
                        { s: 2, a: 250, note: "QS. Al-Baqarah: 250" },
                        { s: 2, a: 286, note: "QS. Al-Baqarah: 286" },
                        { s: 3, a: 8, note: "QS. Ali Imran: 8" },
                        { s: 3, a: 9, note: "QS. Ali Imran: 9" },
                        { s: 3, a: 16, note: "QS. Ali Imran: 16" },
                        { s: 3, a: 26, note: "QS. Ali Imran: 26" },
                        { s: 3, a: 27, note: "QS. Ali Imran: 27" },
                        { s: 3, a: 38, note: "QS. Ali Imran: 38" },
                        { s: 3, a: 53, note: "QS. Ali Imran: 53" },
                        { s: 3, a: 147, note: "QS. Ali Imran: 147" },
                        { s: 3, a: 191, note: "QS. Ali Imran: 191" },
                        { s: 3, a: 192, note: "QS. Ali Imran: 192" },
                        { s: 3, a: 193, note: "QS. Ali Imran: 193" },
                        { s: 3, a: 194, note: "QS. Ali Imran: 194" },
                        { s: 21, a: 83, note: "QS. Al-Anbiya: 83" },
                        { s: 21, a: 87, note: "QS. Al-Anbiya: 87" },
                        { s: 21, a: 89, note: "QS. Al-Anbiya: 89" },
                        { s: 14, a: 40, note: "QS. Ibrahim: 40" },
                        { s: 14, a: 41, note: "QS. Ibrahim: 41" },
                        { s: 25, a: 74, note: "QS. Al-Furqan: 74" },
                        { s: 28, a: 24, note: "QS. Al-Qasas: 24" },
                        { s: 20, a: 25, note: "QS. Taha: 25" },
                        { s: 20, a: 26, note: "QS. Taha: 26" },
                        { s: 20, a: 114, note: "QS. Taha: 114" },
                        { s: 46, a: 15, note: "QS. Al-Ahqaf: 15" },
                        { s: 23, a: 97, note: "QS. Al-Mu'minun: 97" },
                        { s: 23, a: 98, note: "QS. Al-Mu'minun: 98" },
                        { s: 23, a: 118, note: "QS. Al-Mu'minun: 118" },
                        { s: 59, a: 10, note: "QS. Al-Hasyr: 10" },
                        { s: 1, a: 6, note: "QS. Al-Fatihah: 6" },
                        { s: 1, a: 7, note: "QS. Al-Fatihah: 7" },
                        { s: 113, a: 1, note: "QS. Al-Falaq: 1" },
                        { s: 114, a: 1, note: "QS. An-Nas: 1" },
                        { s: 10, a: 85, note: "QS. Yunus: 85" },
                        { s: 10, a: 86, note: "QS. Yunus: 86" },
                    ],
                },
                berbuka: {
                    label: "Waktu Berbuka",
                    refs: [
                        { s: 2, a: 185, note: "QS. Al-Baqarah: 185" },
                        { s: 2, a: 187, note: "QS. Al-Baqarah: 187" },
                        { s: 2, a: 168, note: "QS. Al-Baqarah: 168" },
                        { s: 2, a: 172, note: "QS. Al-Baqarah: 172" },
                        { s: 5, a: 4, note: "QS. Al-Ma'idah: 4" },
                        { s: 5, a: 6, note: "QS. Al-Ma'idah: 6" },
                        { s: 5, a: 88, note: "QS. Al-Ma'idah: 88" },
                        { s: 6, a: 141, note: "QS. Al-An'am: 141" },
                        { s: 6, a: 142, note: "QS. Al-An'am: 142" },
                        { s: 7, a: 31, note: "QS. Al-A'raf: 31" },
                        { s: 7, a: 32, note: "QS. Al-A'raf: 32" },
                        { s: 16, a: 114, note: "QS. An-Nahl: 114" },
                        { s: 23, a: 51, note: "QS. Al-Mu'minun: 51" },
                        { s: 67, a: 15, note: "QS. Al-Mulk: 15" },
                        { s: 14, a: 7, note: "QS. Ibrahim: 7" },
                        { s: 14, a: 34, note: "QS. Ibrahim: 34" },
                        { s: 16, a: 18, note: "QS. An-Nahl: 18" },
                        { s: 16, a: 53, note: "QS. An-Nahl: 53" },
                        { s: 16, a: 78, note: "QS. An-Nahl: 78" },
                        { s: 27, a: 40, note: "QS. An-Naml: 40" },
                        { s: 28, a: 73, note: "QS. Al-Qasas: 73" },
                        { s: 29, a: 17, note: "QS. Al-Ankabut: 17" },
                        { s: 31, a: 12, note: "QS. Luqman: 12" },
                        { s: 34, a: 15, note: "QS. Saba: 15" },
                        { s: 35, a: 3, note: "QS. Fatir: 3" },
                        { s: 40, a: 64, note: "QS. Ghafir: 64" },
                        { s: 45, a: 12, note: "QS. Al-Jasiyah: 12" },
                        { s: 45, a: 13, note: "QS. Al-Jasiyah: 13" },
                        { s: 55, a: 1, note: "QS. Ar-Rahman: 1" },
                        { s: 55, a: 2, note: "QS. Ar-Rahman: 2" },
                        { s: 55, a: 3, note: "QS. Ar-Rahman: 3" },
                        { s: 55, a: 4, note: "QS. Ar-Rahman: 4" },
                        { s: 55, a: 13, note: "QS. Ar-Rahman: 13" },
                        { s: 55, a: 60, note: "QS. Ar-Rahman: 60" },
                        { s: 56, a: 68, note: "QS. Al-Waqi'ah: 68" },
                        { s: 56, a: 69, note: "QS. Al-Waqi'ah: 69" },
                        { s: 80, a: 24, note: "QS. Abasa: 24" },
                    ],
                },
                malam: {
                    label: "Malam Hari",
                    refs: [
                        { s: 97, a: 1, note: "QS. Al-Qadr: 1" },
                        { s: 97, a: 2, note: "QS. Al-Qadr: 2" },
                        { s: 97, a: 3, note: "QS. Al-Qadr: 3" },
                        { s: 97, a: 4, note: "QS. Al-Qadr: 4" },
                        { s: 97, a: 5, note: "QS. Al-Qadr: 5" },
                        { s: 44, a: 3, note: "QS. Ad-Dukhan: 3" },
                        { s: 44, a: 4, note: "QS. Ad-Dukhan: 4" },
                        { s: 17, a: 79, note: "QS. Al-Isra: 79" },
                        { s: 17, a: 78, note: "QS. Al-Isra: 78" },
                        { s: 73, a: 1, note: "QS. Al-Muzzammil: 1" },
                        { s: 73, a: 2, note: "QS. Al-Muzzammil: 2" },
                        { s: 73, a: 3, note: "QS. Al-Muzzammil: 3" },
                        { s: 73, a: 4, note: "QS. Al-Muzzammil: 4" },
                        { s: 73, a: 6, note: "QS. Al-Muzzammil: 6" },
                        { s: 73, a: 20, note: "QS. Al-Muzzammil: 20" },
                        { s: 39, a: 9, note: "QS. Az-Zumar: 9" },
                        { s: 25, a: 64, note: "QS. Al-Furqan: 64" },
                        { s: 25, a: 63, note: "QS. Al-Furqan: 63" },
                        { s: 32, a: 16, note: "QS. As-Sajdah: 16" },
                        { s: 32, a: 17, note: "QS. As-Sajdah: 17" },
                        { s: 51, a: 17, note: "QS. Adz-Dzariyat: 17" },
                        { s: 51, a: 18, note: "QS. Adz-Dzariyat: 18" },
                        { s: 76, a: 25, note: "QS. Al-Insan: 25" },
                        { s: 76, a: 26, note: "QS. Al-Insan: 26" },
                        { s: 52, a: 49, note: "QS. At-Tur: 49" },
                        { s: 50, a: 40, note: "QS. Qaf: 40" },
                        { s: 87, a: 1, note: "QS. Al-A'la: 1" },
                        { s: 87, a: 14, note: "QS. Al-A'la: 14" },
                        { s: 87, a: 15, note: "QS. Al-A'la: 15" },
                        { s: 2, a: 255, note: "QS. Al-Baqarah: 255" },
                        { s: 59, a: 22, note: "QS. Al-Hasyr: 22" },
                        { s: 59, a: 23, note: "QS. Al-Hasyr: 23" },
                        { s: 59, a: 24, note: "QS. Al-Hasyr: 24" },
                        { s: 6, a: 162, note: "QS. Al-An'am: 162" },
                        { s: 6, a: 163, note: "QS. Al-An'am: 163" },
                        { s: 3, a: 113, note: "QS. Ali Imran: 113" },
                        { s: 3, a: 114, note: "QS. Ali Imran: 114" },
                        { s: 3, a: 190, note: "QS. Ali Imran: 190" },
                        { s: 3, a: 191, note: "QS. Ali Imran: 191" },
                        { s: 67, a: 1, note: "QS. Al-Mulk: 1" },
                        { s: 67, a: 2, note: "QS. Al-Mulk: 2" },
                        { s: 67, a: 3, note: "QS. Al-Mulk: 3" },
                        { s: 36, a: 36, note: "QS. Yasin: 36" },
                        { s: 36, a: 40, note: "QS. Yasin: 40" },
                        { s: 112, a: 1, note: "QS. Al-Ikhlas: 1" },
                        { s: 112, a: 2, note: "QS. Al-Ikhlas: 2" },
                    ],
                },
            };
        },
        async fetchContextualVerse() {
            const context = this.getVerseContext();
            const pool = this.getContextualVersePool();
            const group = pool[context] || pool.siang;
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
        getLocation() {
            this.locationText = "Mendeteksi lokasi...";
            this.locationCity = "Mendeteksi lokasi...";
            this.locationCoords = "";
            if (!navigator.geolocation) {
                this.setDefaultLocation();
                return;
            }
            if (this._gpsWatchId !== null) {
                navigator.geolocation.clearWatch(this._gpsWatchId);
                this._gpsWatchId = null;
            }
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
            localStorage.removeItem("ramadhan_location");
            this.getLocation();
        },
        openLocationPicker() {
            this.locationSearch = "";
            this.showLocationPicker = true;
            if (this.indonesiaLocations.length === 0) {
                this.loadIndonesiaLocations();
            } else {
                this.filteredLocations = this.indonesiaLocations;
            }
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
        _compassHandler: null,
        _compassAbsHandler: null,
        _hasAbsolute: false,
        initCompass() {
            if ("ondeviceorientationabsolute" in window) {
                this.compassSupported = true;
                this.compassPermission = "granted";
                this._startAbsoluteCompassListener();
                return;
            }
            if (window.DeviceOrientationEvent) {
                this.compassSupported = true;
                if (
                    typeof DeviceOrientationEvent.requestPermission ===
                    "function"
                ) {
                    this.compassPermission = "unknown";
                } else {
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
                if (this._hasAbsolute) return;
                let heading = null;
                if (e.webkitCompassHeading !== undefined) {
                    heading = e.webkitCompassHeading;
                    this.compassAccuracy = e.webkitCompassAccuracy || null;
                } else if (e.alpha !== null) {
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
        get compassRotation() {
            return -this.compassHeading;
        },
        get qiblaOnCompass() {
            return this.qiblaDirection - this.compassHeading;
        },
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
        _lsKey(base) {
            var uid = window.__siswaUserId || "unknown";
            return base + "_" + uid;
        },
        _clearOldUserData(prefix) {
            var toRemove = [];
            for (var i = 0; i < localStorage.length; i++) {
                var k = localStorage.key(i);
                if (k && k.indexOf(prefix) === 0) toRemove.push(k);
            }
            toRemove.forEach(function (k) {
                localStorage.removeItem(k);
            });
        },
        loadSubmittedDays() {
            try {
                var lastUser = localStorage.getItem("ramadhan_last_user");
                var currentUser = window.__siswaUserId || "unknown";
                if (lastUser && lastUser !== currentUser) {
                    this._clearOldUserData("ramadhan_submitted_days_");
                    this._clearOldUserData("ramadhan_form_day_");
                }
                localStorage.setItem("ramadhan_last_user", currentUser);
                const saved = localStorage.getItem(
                    this._lsKey("ramadhan_submitted_days"),
                );
                this.submittedDays = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.submittedDays = [];
            }
        },
        checkFormSubmitted() {
            this.formSubmitted = this.submittedDays.includes(this.formDay);
            try {
                const savedForm = localStorage.getItem(
                    this._lsKey("ramadhan_form_day_" + this.formDay),
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
                tadarus_entries: [{ surat: "", ayat: "" }],
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
            localStorage.setItem(
                this._lsKey("ramadhan_form_day_" + this.formDay),
                JSON.stringify(this.formData),
            );
            if (!this.submittedDays.includes(this.formDay)) {
                this.submittedDays.push(this.formDay);
                localStorage.setItem(
                    this._lsKey("ramadhan_submitted_days"),
                    JSON.stringify(this.submittedDays),
                );
            }
            this.formSubmitted = true;
            this.buildCalendar();
            setTimeout(() => {
                this.formSaving = false;
            }, 500);
        },
        editForm() {
            this.formSubmitted = false;
        },
        syncFromServer() {
            var self = this;
            _throttledFetch(
                "sync",
                "/api/formulir",
                { headers: { Accept: "application/json" } },
                10000,
            )
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    if (data.success && data.submitted_days) {
                        self.submittedDays = data.submitted_days.slice();
                        localStorage.setItem(
                            self._lsKey("ramadhan_submitted_days"),
                            JSON.stringify(self.submittedDays),
                        );
                        if (data.submissions) {
                            data.submissions.forEach(function (sub) {
                                self.submissionStatuses[sub.hari_ke] = {
                                    status: sub.status || "pending",
                                    catatan_guru: sub.catatan_guru || "",
                                    kesiswaan_status:
                                        sub.kesiswaan_status || "pending",
                                    catatan_kesiswaan:
                                        sub.catatan_kesiswaan || "",
                                };
                            });
                        }
                        self.buildCalendar();
                    }
                })
                .catch(function (e) {
                    if (e && e.rateLimited) console.warn(e.message);
                });
        },
        getProgressPercent() {
            return Math.round(
                ((this.getVerifiedCount() + this.getValidatedCount()) / 30) *
                    100,
            );
        },
        getValidatedCount() {
            var count = 0;
            for (var key in this.submissionStatuses) {
                if (
                    this.submissionStatuses[key].status === "verified" &&
                    this.submissionStatuses[key].kesiswaan_status ===
                        "validated"
                )
                    count++;
            }
            return count;
        },
        getVerifiedCount() {
            var count = 0;
            for (var key in this.submissionStatuses) {
                if (
                    this.submissionStatuses[key].status === "verified" &&
                    this.submissionStatuses[key].kesiswaan_status !==
                        "validated"
                )
                    count++;
            }
            return count;
        },
        getPendingCount() {
            var count = 0;
            for (var key in this.submissionStatuses) {
                var s = this.submissionStatuses[key].status;
                if (s === "pending" || s === "") count++;
            }
            return count;
        },
        getRejectedCount() {
            var count = 0;
            for (var key in this.submissionStatuses) {
                if (this.submissionStatuses[key].status === "rejected") count++;
            }
            return count;
        },
        getVerifiedPercent() {
            return Math.round((this.getVerifiedCount() / 30) * 100);
        },
        getValidatedPercent() {
            return Math.round((this.getValidatedCount() / 30) * 100);
        },
        getPendingPercent() {
            return Math.round((this.getPendingCount() / 30) * 100);
        },
        getRejectedPercent() {
            return Math.round((this.getRejectedCount() / 30) * 100);
        },
        navigateToFormulir(item) {
            if (item.hijriDay <= 0) return;
            var day = item.hijriDay;
            var formulirUrl = document.querySelector("[data-formulir-url]");
            var baseUrl = formulirUrl
                ? formulirUrl.dataset.formulirUrl
                : "/siswa/formulir-harian";
            if (!item.isPast && !item.isToday) {
                return;
            }
            if (item.isCompleted) {
                window.location.href = baseUrl + "?hari=" + day;
                return;
            }
            var firstUnfilled = null;
            for (var d = 1; d <= this.ramadhanDay; d++) {
                if (!this.submittedDays.includes(d)) {
                    firstUnfilled = d;
                    break;
                }
            }
            if (firstUnfilled && firstUnfilled < day) {
                this.notifTitle = "Isi Formulir Secara Berurutan";
                this.notifMessage =
                    "Kamu harus mengisi Hari ke-" +
                    firstUnfilled +
                    " terlebih dahulu sebelum mengisi Hari ke-" +
                    day +
                    ".";
                this.notifRedirectUrl = baseUrl + "?hari=" + firstUnfilled;
                this.showNotifModal = true;
            } else {
                window.location.href = baseUrl + "?hari=" + day;
            }
        },
        closeNotifModal(redirect) {
            this.showNotifModal = false;
            if (redirect && this.notifRedirectUrl) {
                window.location.href = this.notifRedirectUrl;
            }
            this.notifTitle = "";
            this.notifMessage = "";
            this.notifRedirectUrl = "";
        },
        changePassword() {
            var self = this;
            self.pwMessage = "";
            self.pwSuccess = false;
            if (!self.pwOld || !self.pwNew || !self.pwConfirm) {
                self.pwMessage = "Semua field harus diisi.";
                return;
            }
            if (self.pwNew.length < 8) {
                self.pwMessage = "Password baru minimal 8 karakter.";
                return;
            }
            if (self.pwNew !== self.pwConfirm) {
                self.pwMessage = "Konfirmasi password tidak cocok.";
                return;
            }
            self.pwLoading = true;
            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            _throttledFetch(
                "changePw",
                "/api/change-password",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken
                            ? csrfToken.getAttribute("content")
                            : "",
                    },
                    body: JSON.stringify({
                        current_password: self.pwOld,
                        new_password: self.pwNew,
                        new_password_confirmation: self.pwConfirm,
                    }),
                },
                3000,
            )
                .then(function (r) {
                    return r.json().then(function (d) {
                        return { ok: r.ok, data: d };
                    });
                })
                .then(function (res) {
                    self.pwLoading = false;
                    if (res.ok && res.data.success) {
                        self.pwSuccess = true;
                        self.pwMessage =
                            res.data.message || "Password berhasil diubah.";
                        self.pwOld = "";
                        self.pwNew = "";
                        self.pwConfirm = "";
                        setTimeout(function () {
                            self.showChangePassword = false;
                            self.pwMessage = "";
                            self.pwSuccess = false;
                        }, 2000);
                    } else {
                        self.pwMessage =
                            res.data.message || "Gagal mengubah password.";
                    }
                })
                .catch(function (e) {
                    self.pwLoading = false;
                    if (e && e.throttled) return;
                    self.pwMessage =
                        e && e.rateLimited
                            ? e.message
                            : "Terjadi kesalahan. Coba lagi.";
                });
        },
        loadCheckins() {
            var self = this;
            self.checkinLoading = true;
            _throttledFetch(
                "firstUnfilled",
                "/api/prayer-checkins/first-unfilled",
                {
                    headers: { Accept: "application/json" },
                },
                10000,
            )
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    if (data && data.success) {
                        self.checkinDate = data.tanggal;
                        self.checkinHariKe = data.hari_ke;
                        self.checkinAllFilled = data.all_filled || false;
                        self._updateCheckinDateLabel();
                        self._loadCheckinsForDate(data.tanggal);
                    } else {
                        self.checkinLoading = false;
                    }
                })
                .catch(function (e) {
                    self.checkinLoading = false;
                    if (e && (e.throttled || e.rateLimited)) return;
                    console.warn("Gagal memuat data check-in shalat");
                });
        },
        _loadCheckinsForDate(tanggal) {
            var self = this;
            self.checkinLoading = true;
            _throttledFetch(
                "checkinsDate_" + tanggal,
                "/api/prayer-checkins/date/" + tanggal,
                {
                    headers: { Accept: "application/json" },
                },
                10000,
            )
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    self.checkinData =
                        data && data.checkins ? data.checkins : {};
                    self.checkinLoading = false;
                })
                .catch(function (e) {
                    self.checkinLoading = false;
                    if (e && (e.throttled || e.rateLimited)) return;
                    console.warn("Gagal memuat data check-in shalat");
                });
        },
        _updateCheckinDateLabel() {
            if (!this.checkinDate) {
                this.checkinDateLabel = "";
                return;
            }
            var d = new Date(this.checkinDate + "T00:00:00");
            var days = [
                "Minggu",
                "Senin",
                "Selasa",
                "Rabu",
                "Kamis",
                "Jumat",
                "Sabtu",
            ];
            var months = [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember",
            ];
            this.checkinDateLabel =
                days[d.getDay()] +
                ", " +
                d.getDate() +
                " " +
                months[d.getMonth()] +
                " " +
                d.getFullYear();
        },
        checkinPrevDay() {
            if (!this.checkinDate || this.checkinHariKe <= 1) return;
            var d = new Date(this.checkinDate + "T00:00:00");
            d.setDate(d.getDate() - 1);
            var ramStart = new Date(2026, 1, 19);
            if (d < ramStart) return;
            this.checkinHariKe--;
            this.checkinDate =
                d.getFullYear() +
                "-" +
                String(d.getMonth() + 1).padStart(2, "0") +
                "-" +
                String(d.getDate()).padStart(2, "0");
            this._updateCheckinDateLabel();
            this._loadCheckinsForDate(this.checkinDate);
        },
        checkinNextDay() {
            if (!this.checkinDate) return;
            var d = new Date(this.checkinDate + "T00:00:00");
            d.setDate(d.getDate() + 1);
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var ramEnd = new Date(2026, 2, 20);
            if (d > today || d > ramEnd) return;
            this.checkinHariKe++;
            this.checkinDate =
                d.getFullYear() +
                "-" +
                String(d.getMonth() + 1).padStart(2, "0") +
                "-" +
                String(d.getDate()).padStart(2, "0");
            this._updateCheckinDateLabel();
            this._loadCheckinsForDate(this.checkinDate);
        },
        canCheckinPrev() {
            return this.checkinHariKe > 1;
        },
        canCheckinNext() {
            if (!this.checkinDate) return false;
            var d = new Date(this.checkinDate + "T00:00:00");
            d.setDate(d.getDate() + 1);
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var ramEnd = new Date(2026, 2, 20);
            return d <= today && d <= ramEnd;
        },
        getCheckinStatus(id) {
            if (this.checkinData && this.checkinData[id]) {
                return this.checkinData[id].status;
            }
            return null;
        },
        getCheckinLabel(id) {
            var s = this.getCheckinStatus(id);
            if (!s) return "Belum check-in";
            var labels = {
                jamaah: "Jamaah",
                munfarid: "Munfarid",
                ya: "Sudah",
                tidak: "Tidak",
            };
            return labels[s] || s;
        },
        getCheckinColor(id) {
            var s = this.getCheckinStatus(id);
            if (!s) return "#94a3b8";
            var map = {
                jamaah: "#16a34a",
                munfarid: "#ca8a04",
                ya: "#16a34a",
                tidak: "#dc2626",
            };
            return map[s] || "#94a3b8";
        },
        getCheckinTime(id) {
            if (
                this.checkinData &&
                this.checkinData[id] &&
                this.checkinData[id].waktu_checkin
            ) {
                return this.checkinData[id].waktu_checkin;
            }
            return "";
        },
        getCheckinClass(id) {
            var s = this.getCheckinStatus(id);
            if (!s) return "";
            return "checkin-" + s;
        },
        getCheckinIconClass(id) {
            var s = this.getCheckinStatus(id);
            if (!s) return "";
            return "checkin-icon-" + s;
        },
        getCheckinCount() {
            if (!this.checkinData) return 0;
            var count = 0;
            var keys = Object.keys(this.checkinData);
            for (var i = 0; i < keys.length; i++) {
                if (
                    this.checkinData[keys[i]] &&
                    this.checkinData[keys[i]].status
                ) {
                    count++;
                }
            }
            return count;
        },
        // Urutan shalat wajib: harus berurutan dari subuh sampai tarawih
        _wajibOrder: ["subuh", "dzuhur", "ashar", "maghrib", "isya", "tarawih"],
        isWajibLocked(id) {
            // Sudah diisi = tidak terkunci
            if (this.getCheckinStatus(id)) return false;
            var idx = this._wajibOrder.indexOf(id);
            if (idx < 0) return false;
            // Cek urutan: shalat sebelumnya harus sudah diisi (kecuali subuh)
            if (idx > 0) {
                var prevId = this._wajibOrder[idx - 1];
                if (!this.getCheckinStatus(prevId)) return true;
            }
            // Hari sebelumnya: tidak ada lock waktu
            if (this.checkinHariKe < this.ramadhanDay) return false;
            // Hari ini: cek apakah waktu sholat sudah tiba
            if (
                this.checkinHariKe === this.ramadhanDay &&
                this._rawPrayerTimes
            ) {
                var timeKey = id === "tarawih" ? "isya" : id;
                var timeStr = this._rawPrayerTimes[timeKey];
                if (timeStr) {
                    var parts = timeStr.split(":").map(Number);
                    var prayerMinutes = parts[0] * 60 + parts[1];
                    var now = new Date();
                    var currentMinutes = now.getHours() * 60 + now.getMinutes();
                    if (currentMinutes < prayerMinutes) return true;
                }
            }
            return false;
        },
        getWajibLockReason(id) {
            if (this.getCheckinStatus(id)) return "";
            var idx = this._wajibOrder.indexOf(id);
            if (idx > 0) {
                var prevId = this._wajibOrder[idx - 1];
                if (!this.getCheckinStatus(prevId)) return "sequence";
            }
            if (
                this.checkinHariKe === this.ramadhanDay &&
                this._rawPrayerTimes
            ) {
                var timeKey = id === "tarawih" ? "isya" : id;
                var timeStr = this._rawPrayerTimes[timeKey];
                if (timeStr) {
                    var parts = timeStr.split(":").map(Number);
                    var prayerMinutes = parts[0] * 60 + parts[1];
                    var now = new Date();
                    var currentMinutes = now.getHours() * 60 + now.getMinutes();
                    if (currentMinutes < prayerMinutes) return "time";
                }
            }
            return "";
        },
        getWajibUnlockTime(id) {
            if (!this._rawPrayerTimes) return "";
            var timeKey = id === "tarawih" ? "isya" : id;
            return this._rawPrayerTimes[timeKey] || "";
        },
        openCheckinModal(prayer) {
            // Blokir jika shalat wajib terkunci (belum isi yang sebelumnya)
            if (prayer.tipe === "wajib" && this.isWajibLocked(prayer.id))
                return;
            this.checkinModalPrayer = prayer;
            this.showCheckinModal = true;
        },
        submitCheckin(status) {
            var self = this;
            if (!self.checkinModalPrayer || self.checkinSaving) return;
            self.checkinSaving = true;
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfMeta ? csrfMeta.getAttribute("content") : "";
            var payload = {
                shalat: self.checkinModalPrayer.id,
                status: status,
            };
            if (self.checkinDate) {
                payload.tanggal = self.checkinDate;
            }
            _throttledFetch(
                "submitCheckin",
                "/api/prayer-checkins",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify(payload),
                },
                3000,
            )
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    self.checkinSaving = false;
                    if (data && data.checkin) {
                        self.checkinData[self.checkinModalPrayer.id] = {
                            status: data.checkin.status,
                            tipe: data.checkin.tipe,
                            waktu_checkin: data.checkin.waktu_checkin,
                        };
                    }
                    self.showCheckinModal = false;
                })
                .catch(function (e) {
                    self.checkinSaving = false;
                    if (e && e.throttled) return;
                    alert(
                        e && e.rateLimited
                            ? e.message
                            : "Gagal menyimpan check-in. Silakan coba lagi.",
                    );
                });
        },
    };
}
