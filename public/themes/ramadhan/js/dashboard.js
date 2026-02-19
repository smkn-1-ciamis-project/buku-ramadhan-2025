/**
 * Buku Ramadhan — Dashboard Alpine.js Component
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
            this.setDuas();
            this.setDailyVerse();
            this.getLocation();
            this.startCountdown();
            this.startClock();
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

        _legacyLocations_UNUSED() {
            // Legacy inline data removed — now loaded from /themes/ramadhan/data/locations.json
            this.indonesiaLocations = [
                {
                    id: 1,
                    kabupaten: "Kab. Bogor",
                    provinsi: "Jawa Barat",
                    lat: -6.5964,
                    lng: 106.8062,
                },
                {
                    id: 2,
                    kabupaten: "Kota Bogor",
                    provinsi: "Jawa Barat",
                    lat: -6.5971,
                    lng: 106.806,
                },
                {
                    id: 3,
                    kabupaten: "Kab. Sukabumi",
                    provinsi: "Jawa Barat",
                    lat: -6.9295,
                    lng: 106.9289,
                },
                {
                    id: 4,
                    kabupaten: "Kota Sukabumi",
                    provinsi: "Jawa Barat",
                    lat: -6.9221,
                    lng: 106.927,
                },
                {
                    id: 5,
                    kabupaten: "Kab. Cianjur",
                    provinsi: "Jawa Barat",
                    lat: -6.8217,
                    lng: 107.1389,
                },
                {
                    id: 6,
                    kabupaten: "Kab. Bandung",
                    provinsi: "Jawa Barat",
                    lat: -7.0417,
                    lng: 107.5996,
                },
                {
                    id: 7,
                    kabupaten: "Kota Bandung",
                    provinsi: "Jawa Barat",
                    lat: -6.9175,
                    lng: 107.6191,
                },
                {
                    id: 8,
                    kabupaten: "Kab. Bandung Barat",
                    provinsi: "Jawa Barat",
                    lat: -6.8447,
                    lng: 107.5048,
                },
                {
                    id: 9,
                    kabupaten: "Kab. Garut",
                    provinsi: "Jawa Barat",
                    lat: -7.2239,
                    lng: 107.901,
                },
                {
                    id: 10,
                    kabupaten: "Kab. Tasikmalaya",
                    provinsi: "Jawa Barat",
                    lat: -7.3525,
                    lng: 108.12,
                },
                {
                    id: 11,
                    kabupaten: "Kota Tasikmalaya",
                    provinsi: "Jawa Barat",
                    lat: -7.3275,
                    lng: 108.2186,
                },
                {
                    id: 12,
                    kabupaten: "Kab. Ciamis",
                    provinsi: "Jawa Barat",
                    lat: -7.3305,
                    lng: 108.3508,
                },
                {
                    id: 13,
                    kabupaten: "Kota Banjar",
                    provinsi: "Jawa Barat",
                    lat: -7.3666,
                    lng: 108.5412,
                },
                {
                    id: 14,
                    kabupaten: "Kab. Pangandaran",
                    provinsi: "Jawa Barat",
                    lat: -7.6753,
                    lng: 108.4964,
                },
                {
                    id: 15,
                    kabupaten: "Kab. Kuningan",
                    provinsi: "Jawa Barat",
                    lat: -6.9762,
                    lng: 108.4833,
                },
                {
                    id: 16,
                    kabupaten: "Kab. Cirebon",
                    provinsi: "Jawa Barat",
                    lat: -6.7594,
                    lng: 108.4929,
                },
                {
                    id: 17,
                    kabupaten: "Kota Cirebon",
                    provinsi: "Jawa Barat",
                    lat: -6.732,
                    lng: 108.5523,
                },
                {
                    id: 18,
                    kabupaten: "Kab. Majalengka",
                    provinsi: "Jawa Barat",
                    lat: -6.8363,
                    lng: 108.2279,
                },
                {
                    id: 19,
                    kabupaten: "Kab. Sumedang",
                    provinsi: "Jawa Barat",
                    lat: -6.8573,
                    lng: 107.9239,
                },
                {
                    id: 20,
                    kabupaten: "Kab. Indramayu",
                    provinsi: "Jawa Barat",
                    lat: -6.3279,
                    lng: 108.3196,
                },
                {
                    id: 21,
                    kabupaten: "Kab. Subang",
                    provinsi: "Jawa Barat",
                    lat: -6.571,
                    lng: 107.7597,
                },
                {
                    id: 22,
                    kabupaten: "Kab. Purwakarta",
                    provinsi: "Jawa Barat",
                    lat: -6.5562,
                    lng: 107.4386,
                },
                {
                    id: 23,
                    kabupaten: "Kab. Karawang",
                    provinsi: "Jawa Barat",
                    lat: -6.3266,
                    lng: 107.3381,
                },
                {
                    id: 24,
                    kabupaten: "Kota Bekasi",
                    provinsi: "Jawa Barat",
                    lat: -6.2383,
                    lng: 106.9756,
                },
                {
                    id: 25,
                    kabupaten: "Kab. Bekasi",
                    provinsi: "Jawa Barat",
                    lat: -6.3148,
                    lng: 107.1548,
                },
                {
                    id: 26,
                    kabupaten: "Kota Depok",
                    provinsi: "Jawa Barat",
                    lat: -6.4025,
                    lng: 106.7942,
                },
                // BANTEN
                {
                    id: 27,
                    kabupaten: "Kota Serang",
                    provinsi: "Banten",
                    lat: -6.1203,
                    lng: 106.1503,
                },
                {
                    id: 28,
                    kabupaten: "Kab. Serang",
                    provinsi: "Banten",
                    lat: -6.1831,
                    lng: 106.1522,
                },
                {
                    id: 29,
                    kabupaten: "Kota Tangerang",
                    provinsi: "Banten",
                    lat: -6.1754,
                    lng: 106.6297,
                },
                {
                    id: 30,
                    kabupaten: "Kab. Tangerang",
                    provinsi: "Banten",
                    lat: -6.1882,
                    lng: 106.532,
                },
                {
                    id: 31,
                    kabupaten: "Kota Tangerang Selatan",
                    provinsi: "Banten",
                    lat: -6.2883,
                    lng: 106.7136,
                },
                {
                    id: 32,
                    kabupaten: "Kab. Lebak",
                    provinsi: "Banten",
                    lat: -6.5602,
                    lng: 106.2527,
                },
                {
                    id: 33,
                    kabupaten: "Kab. Pandeglang",
                    provinsi: "Banten",
                    lat: -6.307,
                    lng: 106.1066,
                },
                {
                    id: 34,
                    kabupaten: "Kota Cilegon",
                    provinsi: "Banten",
                    lat: -6.002,
                    lng: 106.0044,
                },
                // DKI JAKARTA
                {
                    id: 35,
                    kabupaten: "Jakarta Pusat",
                    provinsi: "DKI Jakarta",
                    lat: -6.1862,
                    lng: 106.8063,
                },
                {
                    id: 36,
                    kabupaten: "Jakarta Utara",
                    provinsi: "DKI Jakarta",
                    lat: -6.1344,
                    lng: 106.8446,
                },
                {
                    id: 37,
                    kabupaten: "Jakarta Barat",
                    provinsi: "DKI Jakarta",
                    lat: -6.1681,
                    lng: 106.7631,
                },
                {
                    id: 38,
                    kabupaten: "Jakarta Selatan",
                    provinsi: "DKI Jakarta",
                    lat: -6.2615,
                    lng: 106.8106,
                },
                {
                    id: 39,
                    kabupaten: "Jakarta Timur",
                    provinsi: "DKI Jakarta",
                    lat: -6.225,
                    lng: 106.9004,
                },
                // JAWA TENGAH
                {
                    id: 40,
                    kabupaten: "Kota Semarang",
                    provinsi: "Jawa Tengah",
                    lat: -6.9932,
                    lng: 110.4203,
                },
                {
                    id: 41,
                    kabupaten: "Kab. Semarang",
                    provinsi: "Jawa Tengah",
                    lat: -7.2208,
                    lng: 110.4032,
                },
                {
                    id: 42,
                    kabupaten: "Kab. Kendal",
                    provinsi: "Jawa Tengah",
                    lat: -6.9235,
                    lng: 110.1966,
                },
                {
                    id: 43,
                    kabupaten: "Kab. Demak",
                    provinsi: "Jawa Tengah",
                    lat: -6.8942,
                    lng: 110.6385,
                },
                {
                    id: 44,
                    kabupaten: "Kab. Grobogan",
                    provinsi: "Jawa Tengah",
                    lat: -7.0076,
                    lng: 110.9213,
                },
                {
                    id: 45,
                    kabupaten: "Kab. Pati",
                    provinsi: "Jawa Tengah",
                    lat: -6.7463,
                    lng: 111.035,
                },
                {
                    id: 46,
                    kabupaten: "Kab. Kudus",
                    provinsi: "Jawa Tengah",
                    lat: -6.8051,
                    lng: 110.8385,
                },
                {
                    id: 47,
                    kabupaten: "Kab. Jepara",
                    provinsi: "Jawa Tengah",
                    lat: -6.5941,
                    lng: 110.668,
                },
                {
                    id: 48,
                    kabupaten: "Kab. Rembang",
                    provinsi: "Jawa Tengah",
                    lat: -6.7049,
                    lng: 111.343,
                },
                {
                    id: 49,
                    kabupaten: "Kab. Blora",
                    provinsi: "Jawa Tengah",
                    lat: -6.9756,
                    lng: 111.422,
                },
                {
                    id: 50,
                    kabupaten: "Kab. Boyolali",
                    provinsi: "Jawa Tengah",
                    lat: -7.5258,
                    lng: 110.5999,
                },
                {
                    id: 51,
                    kabupaten: "Kota Solo",
                    provinsi: "Jawa Tengah",
                    lat: -7.5755,
                    lng: 110.8243,
                },
                {
                    id: 52,
                    kabupaten: "Kab. Klaten",
                    provinsi: "Jawa Tengah",
                    lat: -7.7059,
                    lng: 110.5958,
                },
                {
                    id: 53,
                    kabupaten: "Kab. Sukoharjo",
                    provinsi: "Jawa Tengah",
                    lat: -7.6817,
                    lng: 110.8385,
                },
                {
                    id: 54,
                    kabupaten: "Kab. Wonogiri",
                    provinsi: "Jawa Tengah",
                    lat: -7.8132,
                    lng: 110.9216,
                },
                {
                    id: 55,
                    kabupaten: "Kab. Karanganyar",
                    provinsi: "Jawa Tengah",
                    lat: -7.5963,
                    lng: 111.0263,
                },
                {
                    id: 56,
                    kabupaten: "Kab. Sragen",
                    provinsi: "Jawa Tengah",
                    lat: -7.4252,
                    lng: 111.026,
                },
                {
                    id: 57,
                    kabupaten: "Kab. Purworejo",
                    provinsi: "Jawa Tengah",
                    lat: -7.7143,
                    lng: 110.0234,
                },
                {
                    id: 58,
                    kabupaten: "Kab. Kebumen",
                    provinsi: "Jawa Tengah",
                    lat: -7.6673,
                    lng: 109.653,
                },
                {
                    id: 59,
                    kabupaten: "Kab. Magelang",
                    provinsi: "Jawa Tengah",
                    lat: -7.4797,
                    lng: 110.2177,
                },
                {
                    id: 60,
                    kabupaten: "Kota Magelang",
                    provinsi: "Jawa Tengah",
                    lat: -7.4705,
                    lng: 110.2177,
                },
                {
                    id: 61,
                    kabupaten: "Kab. Temanggung",
                    provinsi: "Jawa Tengah",
                    lat: -7.3165,
                    lng: 110.1714,
                },
                {
                    id: 62,
                    kabupaten: "Kab. Wonosobo",
                    provinsi: "Jawa Tengah",
                    lat: -7.3602,
                    lng: 109.9086,
                },
                {
                    id: 63,
                    kabupaten: "Kab. Banjarnegara",
                    provinsi: "Jawa Tengah",
                    lat: -7.3885,
                    lng: 109.6956,
                },
                {
                    id: 64,
                    kabupaten: "Kab. Purbalingga",
                    provinsi: "Jawa Tengah",
                    lat: -7.3902,
                    lng: 109.3649,
                },
                {
                    id: 65,
                    kabupaten: "Kab. Banyumas",
                    provinsi: "Jawa Tengah",
                    lat: -7.4304,
                    lng: 109.2318,
                },
                {
                    id: 66,
                    kabupaten: "Kota Purwokerto",
                    provinsi: "Jawa Tengah",
                    lat: -7.4211,
                    lng: 109.2368,
                },
                {
                    id: 67,
                    kabupaten: "Kab. Cilacap",
                    provinsi: "Jawa Tengah",
                    lat: -7.7302,
                    lng: 109.0152,
                },
                {
                    id: 68,
                    kabupaten: "Kab. Brebes",
                    provinsi: "Jawa Tengah",
                    lat: -6.8728,
                    lng: 108.8682,
                },
                {
                    id: 69,
                    kabupaten: "Kab. Tegal",
                    provinsi: "Jawa Tengah",
                    lat: -6.9781,
                    lng: 109.1411,
                },
                {
                    id: 70,
                    kabupaten: "Kota Tegal",
                    provinsi: "Jawa Tengah",
                    lat: -6.8697,
                    lng: 109.1402,
                },
                {
                    id: 71,
                    kabupaten: "Kab. Pemalang",
                    provinsi: "Jawa Tengah",
                    lat: -6.8922,
                    lng: 109.3792,
                },
                {
                    id: 72,
                    kabupaten: "Kab. Pekalongan",
                    provinsi: "Jawa Tengah",
                    lat: -7.0224,
                    lng: 109.6745,
                },
                {
                    id: 73,
                    kabupaten: "Kota Pekalongan",
                    provinsi: "Jawa Tengah",
                    lat: -6.8897,
                    lng: 109.6753,
                },
                {
                    id: 74,
                    kabupaten: "Kab. Batang",
                    provinsi: "Jawa Tengah",
                    lat: -6.9115,
                    lng: 109.7294,
                },
                // DI YOGYAKARTA
                {
                    id: 75,
                    kabupaten: "Kota Yogyakarta",
                    provinsi: "DI Yogyakarta",
                    lat: -7.7974,
                    lng: 110.3657,
                },
                {
                    id: 76,
                    kabupaten: "Kab. Sleman",
                    provinsi: "DI Yogyakarta",
                    lat: -7.7164,
                    lng: 110.3557,
                },
                {
                    id: 77,
                    kabupaten: "Kab. Bantul",
                    provinsi: "DI Yogyakarta",
                    lat: -7.8894,
                    lng: 110.3288,
                },
                {
                    id: 78,
                    kabupaten: "Kab. Kulon Progo",
                    provinsi: "DI Yogyakarta",
                    lat: -7.8327,
                    lng: 110.1627,
                },
                {
                    id: 79,
                    kabupaten: "Kab. Gunung Kidul",
                    provinsi: "DI Yogyakarta",
                    lat: -7.9631,
                    lng: 110.5975,
                },
                // JAWA TIMUR
                {
                    id: 80,
                    kabupaten: "Kota Surabaya",
                    provinsi: "Jawa Timur",
                    lat: -7.2575,
                    lng: 112.7521,
                },
                {
                    id: 81,
                    kabupaten: "Kab. Sidoarjo",
                    provinsi: "Jawa Timur",
                    lat: -7.4472,
                    lng: 112.7185,
                },
                {
                    id: 82,
                    kabupaten: "Kab. Gresik",
                    provinsi: "Jawa Timur",
                    lat: -7.1571,
                    lng: 112.6502,
                },
                {
                    id: 83,
                    kabupaten: "Kab. Mojokerto",
                    provinsi: "Jawa Timur",
                    lat: -7.4692,
                    lng: 112.4348,
                },
                {
                    id: 84,
                    kabupaten: "Kota Mojokerto",
                    provinsi: "Jawa Timur",
                    lat: -7.4726,
                    lng: 112.4343,
                },
                {
                    id: 85,
                    kabupaten: "Kab. Jombang",
                    provinsi: "Jawa Timur",
                    lat: -7.5465,
                    lng: 112.2419,
                },
                {
                    id: 86,
                    kabupaten: "Kab. Lamongan",
                    provinsi: "Jawa Timur",
                    lat: -7.1137,
                    lng: 112.4153,
                },
                {
                    id: 87,
                    kabupaten: "Kab. Tuban",
                    provinsi: "Jawa Timur",
                    lat: -6.8993,
                    lng: 112.0497,
                },
                {
                    id: 88,
                    kabupaten: "Kab. Bojonegoro",
                    provinsi: "Jawa Timur",
                    lat: -7.1503,
                    lng: 111.8818,
                },
                {
                    id: 89,
                    kabupaten: "Kab. Ngawi",
                    provinsi: "Jawa Timur",
                    lat: -7.4025,
                    lng: 111.4488,
                },
                {
                    id: 90,
                    kabupaten: "Kab. Madiun",
                    provinsi: "Jawa Timur",
                    lat: -7.6298,
                    lng: 111.5232,
                },
                {
                    id: 91,
                    kabupaten: "Kota Madiun",
                    provinsi: "Jawa Timur",
                    lat: -7.6298,
                    lng: 111.5232,
                },
                {
                    id: 92,
                    kabupaten: "Kab. Nganjuk",
                    provinsi: "Jawa Timur",
                    lat: -7.6045,
                    lng: 111.9044,
                },
                {
                    id: 93,
                    kabupaten: "Kab. Kediri",
                    provinsi: "Jawa Timur",
                    lat: -7.8275,
                    lng: 112.0087,
                },
                {
                    id: 94,
                    kabupaten: "Kota Kediri",
                    provinsi: "Jawa Timur",
                    lat: -7.8479,
                    lng: 112.0171,
                },
                {
                    id: 95,
                    kabupaten: "Kab. Blitar",
                    provinsi: "Jawa Timur",
                    lat: -8.0955,
                    lng: 112.1683,
                },
                {
                    id: 96,
                    kabupaten: "Kota Blitar",
                    provinsi: "Jawa Timur",
                    lat: -8.0955,
                    lng: 112.1683,
                },
                {
                    id: 97,
                    kabupaten: "Kab. Tulungagung",
                    provinsi: "Jawa Timur",
                    lat: -8.0651,
                    lng: 111.9038,
                },
                {
                    id: 98,
                    kabupaten: "Kab. Trenggalek",
                    provinsi: "Jawa Timur",
                    lat: -8.054,
                    lng: 111.7097,
                },
                {
                    id: 99,
                    kabupaten: "Kab. Ponorogo",
                    provinsi: "Jawa Timur",
                    lat: -7.8672,
                    lng: 111.4668,
                },
                {
                    id: 100,
                    kabupaten: "Kab. Pacitan",
                    provinsi: "Jawa Timur",
                    lat: -8.2,
                    lng: 111.101,
                },
                {
                    id: 101,
                    kabupaten: "Kab. Malang",
                    provinsi: "Jawa Timur",
                    lat: -8.0652,
                    lng: 112.4286,
                },
                {
                    id: 102,
                    kabupaten: "Kota Malang",
                    provinsi: "Jawa Timur",
                    lat: -7.9666,
                    lng: 112.6326,
                },
                {
                    id: 103,
                    kabupaten: "Kota Batu",
                    provinsi: "Jawa Timur",
                    lat: -7.8707,
                    lng: 112.5285,
                },
                {
                    id: 104,
                    kabupaten: "Kab. Pasuruan",
                    provinsi: "Jawa Timur",
                    lat: -7.6455,
                    lng: 112.9076,
                },
                {
                    id: 105,
                    kabupaten: "Kota Pasuruan",
                    provinsi: "Jawa Timur",
                    lat: -7.6451,
                    lng: 112.9055,
                },
                {
                    id: 106,
                    kabupaten: "Kab. Probolinggo",
                    provinsi: "Jawa Timur",
                    lat: -7.7543,
                    lng: 113.2159,
                },
                {
                    id: 107,
                    kabupaten: "Kota Probolinggo",
                    provinsi: "Jawa Timur",
                    lat: -7.7543,
                    lng: 113.2159,
                },
                {
                    id: 108,
                    kabupaten: "Kab. Lumajang",
                    provinsi: "Jawa Timur",
                    lat: -8.1324,
                    lng: 113.2227,
                },
                {
                    id: 109,
                    kabupaten: "Kab. Jember",
                    provinsi: "Jawa Timur",
                    lat: -8.1724,
                    lng: 113.7022,
                },
                {
                    id: 110,
                    kabupaten: "Kab. Banyuwangi",
                    provinsi: "Jawa Timur",
                    lat: -8.2192,
                    lng: 114.3691,
                },
                {
                    id: 111,
                    kabupaten: "Kab. Bondowoso",
                    provinsi: "Jawa Timur",
                    lat: -7.9083,
                    lng: 113.8222,
                },
                {
                    id: 112,
                    kabupaten: "Kab. Situbondo",
                    provinsi: "Jawa Timur",
                    lat: -7.7068,
                    lng: 114.0088,
                },
                {
                    id: 113,
                    kabupaten: "Kab. Sampang",
                    provinsi: "Jawa Timur",
                    lat: -7.1965,
                    lng: 113.2439,
                },
                {
                    id: 114,
                    kabupaten: "Kab. Pamekasan",
                    provinsi: "Jawa Timur",
                    lat: -7.1575,
                    lng: 113.4765,
                },
                {
                    id: 115,
                    kabupaten: "Kab. Sumenep",
                    provinsi: "Jawa Timur",
                    lat: -7.0168,
                    lng: 113.8599,
                },
                {
                    id: 116,
                    kabupaten: "Kab. Bangkalan",
                    provinsi: "Jawa Timur",
                    lat: -7.0395,
                    lng: 112.7303,
                },
                // SUMATERA UTARA
                {
                    id: 117,
                    kabupaten: "Kota Medan",
                    provinsi: "Sumatera Utara",
                    lat: 3.5952,
                    lng: 98.6722,
                },
                {
                    id: 118,
                    kabupaten: "Kab. Deli Serdang",
                    provinsi: "Sumatera Utara",
                    lat: 3.5289,
                    lng: 98.8428,
                },
                {
                    id: 119,
                    kabupaten: "Kab. Langkat",
                    provinsi: "Sumatera Utara",
                    lat: 3.7925,
                    lng: 98.3046,
                },
                {
                    id: 120,
                    kabupaten: "Kab. Serdang Bedagai",
                    provinsi: "Sumatera Utara",
                    lat: 3.3629,
                    lng: 99.0273,
                },
                {
                    id: 121,
                    kabupaten: "Kota Binjai",
                    provinsi: "Sumatera Utara",
                    lat: 3.6013,
                    lng: 98.4851,
                },
                {
                    id: 122,
                    kabupaten: "Kab. Karo",
                    provinsi: "Sumatera Utara",
                    lat: 3.129,
                    lng: 98.3834,
                },
                {
                    id: 123,
                    kabupaten: "Kab. Simalungun",
                    provinsi: "Sumatera Utara",
                    lat: 2.9594,
                    lng: 99.0576,
                },
                {
                    id: 124,
                    kabupaten: "Kota Pematangsiantar",
                    provinsi: "Sumatera Utara",
                    lat: 2.9594,
                    lng: 99.0576,
                },
                {
                    id: 125,
                    kabupaten: "Kab. Toba",
                    provinsi: "Sumatera Utara",
                    lat: 2.3524,
                    lng: 99.0892,
                },
                {
                    id: 126,
                    kabupaten: "Kota Sibolga",
                    provinsi: "Sumatera Utara",
                    lat: 1.742,
                    lng: 98.7793,
                },
                {
                    id: 127,
                    kabupaten: "Kab. Tapanuli Utara",
                    provinsi: "Sumatera Utara",
                    lat: 2.088,
                    lng: 98.9882,
                },
                {
                    id: 128,
                    kabupaten: "Kab. Tapanuli Tengah",
                    provinsi: "Sumatera Utara",
                    lat: 1.6847,
                    lng: 98.7697,
                },
                {
                    id: 129,
                    kabupaten: "Kab. Tapanuli Selatan",
                    provinsi: "Sumatera Utara",
                    lat: 1.3289,
                    lng: 99.2769,
                },
                {
                    id: 130,
                    kabupaten: "Kota Padangsidimpuan",
                    provinsi: "Sumatera Utara",
                    lat: 1.3893,
                    lng: 99.2718,
                },
                {
                    id: 131,
                    kabupaten: "Kab. Mandailing Natal",
                    provinsi: "Sumatera Utara",
                    lat: 0.6059,
                    lng: 99.358,
                },
                {
                    id: 132,
                    kabupaten: "Kab. Asahan",
                    provinsi: "Sumatera Utara",
                    lat: 2.6895,
                    lng: 99.855,
                },
                {
                    id: 133,
                    kabupaten: "Kota Tanjungbalai",
                    provinsi: "Sumatera Utara",
                    lat: 2.9618,
                    lng: 99.8036,
                },
                // SUMATERA BARAT
                {
                    id: 134,
                    kabupaten: "Kota Padang",
                    provinsi: "Sumatera Barat",
                    lat: -0.9471,
                    lng: 100.4172,
                },
                {
                    id: 135,
                    kabupaten: "Kota Padang Panjang",
                    provinsi: "Sumatera Barat",
                    lat: -0.4665,
                    lng: 100.4053,
                },
                {
                    id: 136,
                    kabupaten: "Kab. Agam",
                    provinsi: "Sumatera Barat",
                    lat: -0.2387,
                    lng: 100.0049,
                },
                {
                    id: 137,
                    kabupaten: "Kota Bukittinggi",
                    provinsi: "Sumatera Barat",
                    lat: -0.3055,
                    lng: 100.3694,
                },
                {
                    id: 138,
                    kabupaten: "Kab. Lima Puluh Kota",
                    provinsi: "Sumatera Barat",
                    lat: -0.3555,
                    lng: 100.6693,
                },
                {
                    id: 139,
                    kabupaten: "Kab. Tanah Datar",
                    provinsi: "Sumatera Barat",
                    lat: -0.4566,
                    lng: 100.6016,
                },
                {
                    id: 140,
                    kabupaten: "Kab. Pesisir Selatan",
                    provinsi: "Sumatera Barat",
                    lat: -1.8626,
                    lng: 100.5655,
                },
                {
                    id: 141,
                    kabupaten: "Kab. Solok",
                    provinsi: "Sumatera Barat",
                    lat: -0.7958,
                    lng: 100.7153,
                },
                {
                    id: 142,
                    kabupaten: "Kota Solok",
                    provinsi: "Sumatera Barat",
                    lat: -0.799,
                    lng: 100.66,
                },
                // RIAU
                {
                    id: 143,
                    kabupaten: "Kota Pekanbaru",
                    provinsi: "Riau",
                    lat: 0.5071,
                    lng: 101.4478,
                },
                {
                    id: 144,
                    kabupaten: "Kab. Kampar",
                    provinsi: "Riau",
                    lat: 0.3578,
                    lng: 101.2145,
                },
                {
                    id: 145,
                    kabupaten: "Kab. Rokan Hulu",
                    provinsi: "Riau",
                    lat: 0.919,
                    lng: 100.6371,
                },
                {
                    id: 146,
                    kabupaten: "Kab. Rokan Hilir",
                    provinsi: "Riau",
                    lat: 2.153,
                    lng: 100.9032,
                },
                {
                    id: 147,
                    kabupaten: "Kab. Siak",
                    provinsi: "Riau",
                    lat: 1.1254,
                    lng: 102.001,
                },
                {
                    id: 148,
                    kabupaten: "Kab. Pelalawan",
                    provinsi: "Riau",
                    lat: 0.0004,
                    lng: 102.1184,
                },
                {
                    id: 149,
                    kabupaten: "Kab. Indragiri Hulu",
                    provinsi: "Riau",
                    lat: -0.342,
                    lng: 102.5306,
                },
                {
                    id: 150,
                    kabupaten: "Kab. Indragiri Hilir",
                    provinsi: "Riau",
                    lat: -0.3414,
                    lng: 103.4073,
                },
                {
                    id: 151,
                    kabupaten: "Kab. Bengkalis",
                    provinsi: "Riau",
                    lat: 1.4695,
                    lng: 102.1055,
                },
                {
                    id: 152,
                    kabupaten: "Kota Dumai",
                    provinsi: "Riau",
                    lat: 1.6672,
                    lng: 101.4472,
                },
                // KEPULAUAN RIAU
                {
                    id: 153,
                    kabupaten: "Kota Batam",
                    provinsi: "Kepulauan Riau",
                    lat: 1.13,
                    lng: 104.0537,
                },
                {
                    id: 154,
                    kabupaten: "Kota Tanjungpinang",
                    provinsi: "Kepulauan Riau",
                    lat: 0.919,
                    lng: 104.4431,
                },
                {
                    id: 155,
                    kabupaten: "Kab. Bintan",
                    provinsi: "Kepulauan Riau",
                    lat: 1.1205,
                    lng: 104.4854,
                },
                {
                    id: 156,
                    kabupaten: "Kab. Karimun",
                    provinsi: "Kepulauan Riau",
                    lat: 1.0052,
                    lng: 103.3943,
                },
                {
                    id: 157,
                    kabupaten: "Kab. Lingga",
                    provinsi: "Kepulauan Riau",
                    lat: 0.1932,
                    lng: 104.6123,
                },
                // SUMATERA SELATAN
                {
                    id: 158,
                    kabupaten: "Kota Palembang",
                    provinsi: "Sumatera Selatan",
                    lat: -2.9761,
                    lng: 104.7754,
                },
                {
                    id: 159,
                    kabupaten: "Kab. Ogan Komering Ilir",
                    provinsi: "Sumatera Selatan",
                    lat: -3.5026,
                    lng: 105.0,
                },
                {
                    id: 160,
                    kabupaten: "Kab. Ogan Komering Ulu",
                    provinsi: "Sumatera Selatan",
                    lat: -4.0298,
                    lng: 104.0456,
                },
                {
                    id: 161,
                    kabupaten: "Kab. Muara Enim",
                    provinsi: "Sumatera Selatan",
                    lat: -3.6581,
                    lng: 103.7544,
                },
                {
                    id: 162,
                    kabupaten: "Kab. Musi Banyuasin",
                    provinsi: "Sumatera Selatan",
                    lat: -2.5591,
                    lng: 104.231,
                },
                {
                    id: 163,
                    kabupaten: "Kab. Musi Rawas",
                    provinsi: "Sumatera Selatan",
                    lat: -3.0978,
                    lng: 103.1219,
                },
                {
                    id: 164,
                    kabupaten: "Kota Prabumulih",
                    provinsi: "Sumatera Selatan",
                    lat: -3.4288,
                    lng: 104.2336,
                },
                // LAMPUNG
                {
                    id: 165,
                    kabupaten: "Kota Bandar Lampung",
                    provinsi: "Lampung",
                    lat: -5.3971,
                    lng: 105.2668,
                },
                {
                    id: 166,
                    kabupaten: "Kab. Lampung Selatan",
                    provinsi: "Lampung",
                    lat: -5.6282,
                    lng: 105.55,
                },
                {
                    id: 167,
                    kabupaten: "Kab. Lampung Tengah",
                    provinsi: "Lampung",
                    lat: -4.824,
                    lng: 105.2498,
                },
                {
                    id: 168,
                    kabupaten: "Kab. Lampung Utara",
                    provinsi: "Lampung",
                    lat: -4.822,
                    lng: 104.9064,
                },
                {
                    id: 169,
                    kabupaten: "Kab. Lampung Barat",
                    provinsi: "Lampung",
                    lat: -5.0218,
                    lng: 104.1649,
                },
                {
                    id: 170,
                    kabupaten: "Kab. Tanggamus",
                    provinsi: "Lampung",
                    lat: -5.4748,
                    lng: 104.8744,
                },
                {
                    id: 171,
                    kabupaten: "Kota Metro",
                    provinsi: "Lampung",
                    lat: -5.1167,
                    lng: 105.306,
                },
                {
                    id: 172,
                    kabupaten: "Kab. Way Kanan",
                    provinsi: "Lampung",
                    lat: -4.3432,
                    lng: 104.5456,
                },
                {
                    id: 173,
                    kabupaten: "Kab. Pringsewu",
                    provinsi: "Lampung",
                    lat: -5.3598,
                    lng: 104.974,
                },
                {
                    id: 174,
                    kabupaten: "Kab. Mesuji",
                    provinsi: "Lampung",
                    lat: -3.9613,
                    lng: 105.38,
                },
                {
                    id: 175,
                    kabupaten: "Kab. Tulang Bawang",
                    provinsi: "Lampung",
                    lat: -4.2578,
                    lng: 105.5866,
                },
                // BENGKULU
                {
                    id: 176,
                    kabupaten: "Kota Bengkulu",
                    provinsi: "Bengkulu",
                    lat: -3.798,
                    lng: 102.2699,
                },
                {
                    id: 177,
                    kabupaten: "Kab. Bengkulu Selatan",
                    provinsi: "Bengkulu",
                    lat: -4.4506,
                    lng: 103.0206,
                },
                {
                    id: 178,
                    kabupaten: "Kab. Rejang Lebong",
                    provinsi: "Bengkulu",
                    lat: -3.4584,
                    lng: 102.5617,
                },
                // JAMBI
                {
                    id: 179,
                    kabupaten: "Kota Jambi",
                    provinsi: "Jambi",
                    lat: -1.6101,
                    lng: 103.6131,
                },
                {
                    id: 180,
                    kabupaten: "Kab. Batanghari",
                    provinsi: "Jambi",
                    lat: -1.7453,
                    lng: 103.0283,
                },
                {
                    id: 181,
                    kabupaten: "Kab. Kerinci",
                    provinsi: "Jambi",
                    lat: -2.0864,
                    lng: 101.6567,
                },
                {
                    id: 182,
                    kabupaten: "Kab. Muaro Jambi",
                    provinsi: "Jambi",
                    lat: -1.5884,
                    lng: 103.6427,
                },
                {
                    id: 183,
                    kabupaten: "Kab. Tanjung Jabung Barat",
                    provinsi: "Jambi",
                    lat: -1.0426,
                    lng: 103.1234,
                },
                // KALIMANTAN BARAT
                {
                    id: 184,
                    kabupaten: "Kota Pontianak",
                    provinsi: "Kalimantan Barat",
                    lat: -0.0263,
                    lng: 109.3425,
                },
                {
                    id: 185,
                    kabupaten: "Kab. Kubu Raya",
                    provinsi: "Kalimantan Barat",
                    lat: -0.215,
                    lng: 109.3699,
                },
                {
                    id: 186,
                    kabupaten: "Kab. Mempawah",
                    provinsi: "Kalimantan Barat",
                    lat: 0.3672,
                    lng: 108.9865,
                },
                {
                    id: 187,
                    kabupaten: "Kab. Sambas",
                    provinsi: "Kalimantan Barat",
                    lat: 1.3652,
                    lng: 109.2888,
                },
                {
                    id: 188,
                    kabupaten: "Kab. Sanggau",
                    provinsi: "Kalimantan Barat",
                    lat: 0.1286,
                    lng: 110.5944,
                },
                {
                    id: 189,
                    kabupaten: "Kab. Sintang",
                    provinsi: "Kalimantan Barat",
                    lat: 0.0742,
                    lng: 111.4748,
                },
                {
                    id: 190,
                    kabupaten: "Kab. Kapuas Hulu",
                    provinsi: "Kalimantan Barat",
                    lat: 1.0023,
                    lng: 113.9481,
                },
                {
                    id: 191,
                    kabupaten: "Kota Singkawang",
                    provinsi: "Kalimantan Barat",
                    lat: 0.9025,
                    lng: 108.9861,
                },
                {
                    id: 192,
                    kabupaten: "Kab. Ketapang",
                    provinsi: "Kalimantan Barat",
                    lat: -1.8303,
                    lng: 110.0039,
                },
                // KALIMANTAN TENGAH
                {
                    id: 193,
                    kabupaten: "Kota Palangka Raya",
                    provinsi: "Kalimantan Tengah",
                    lat: -2.2136,
                    lng: 113.9108,
                },
                {
                    id: 194,
                    kabupaten: "Kab. Kotawaringin Barat",
                    provinsi: "Kalimantan Tengah",
                    lat: -2.198,
                    lng: 111.6878,
                },
                {
                    id: 195,
                    kabupaten: "Kab. Kotawaringin Timur",
                    provinsi: "Kalimantan Tengah",
                    lat: -2.2088,
                    lng: 113.0413,
                },
                {
                    id: 196,
                    kabupaten: "Kab. Kapuas",
                    provinsi: "Kalimantan Tengah",
                    lat: -3.0215,
                    lng: 114.3893,
                },
                {
                    id: 197,
                    kabupaten: "Kab. Barito Selatan",
                    provinsi: "Kalimantan Tengah",
                    lat: -1.9954,
                    lng: 114.8268,
                },
                // KALIMANTAN SELATAN
                {
                    id: 198,
                    kabupaten: "Kota Banjarmasin",
                    provinsi: "Kalimantan Selatan",
                    lat: -3.3186,
                    lng: 114.5944,
                },
                {
                    id: 199,
                    kabupaten: "Kota Banjarbaru",
                    provinsi: "Kalimantan Selatan",
                    lat: -3.4422,
                    lng: 114.8316,
                },
                {
                    id: 200,
                    kabupaten: "Kab. Banjar",
                    provinsi: "Kalimantan Selatan",
                    lat: -3.5872,
                    lng: 114.8339,
                },
                {
                    id: 201,
                    kabupaten: "Kab. Barito Kuala",
                    provinsi: "Kalimantan Selatan",
                    lat: -2.9889,
                    lng: 114.7617,
                },
                {
                    id: 202,
                    kabupaten: "Kab. Tanah Laut",
                    provinsi: "Kalimantan Selatan",
                    lat: -3.9578,
                    lng: 115.0,
                },
                {
                    id: 203,
                    kabupaten: "Kab. Hulu Sungai Selatan",
                    provinsi: "Kalimantan Selatan",
                    lat: -2.5255,
                    lng: 115.4148,
                },
                {
                    id: 204,
                    kabupaten: "Kab. Hulu Sungai Tengah",
                    provinsi: "Kalimantan Selatan",
                    lat: -2.3559,
                    lng: 115.3629,
                },
                {
                    id: 205,
                    kabupaten: "Kab. Hulu Sungai Utara",
                    provinsi: "Kalimantan Selatan",
                    lat: -2.0765,
                    lng: 115.2305,
                },
                {
                    id: 206,
                    kabupaten: "Kab. Tabalong",
                    provinsi: "Kalimantan Selatan",
                    lat: -2.0244,
                    lng: 115.8714,
                },
                {
                    id: 207,
                    kabupaten: "Kab. Balangan",
                    provinsi: "Kalimantan Selatan",
                    lat: -2.3044,
                    lng: 115.5066,
                },
                // KALIMANTAN TIMUR
                {
                    id: 208,
                    kabupaten: "Kota Samarinda",
                    provinsi: "Kalimantan Timur",
                    lat: -0.5021,
                    lng: 117.1536,
                },
                {
                    id: 209,
                    kabupaten: "Kota Balikpapan",
                    provinsi: "Kalimantan Timur",
                    lat: -1.2654,
                    lng: 116.8312,
                },
                {
                    id: 210,
                    kabupaten: "Kab. Kutai Kartanegara",
                    provinsi: "Kalimantan Timur",
                    lat: -0.3813,
                    lng: 116.9879,
                },
                {
                    id: 211,
                    kabupaten: "Kab. Berau",
                    provinsi: "Kalimantan Timur",
                    lat: 2.1564,
                    lng: 117.4843,
                },
                {
                    id: 212,
                    kabupaten: "Kab. Kutai Barat",
                    provinsi: "Kalimantan Timur",
                    lat: -0.5949,
                    lng: 115.6593,
                },
                {
                    id: 213,
                    kabupaten: "Kab. Kutai Timur",
                    provinsi: "Kalimantan Timur",
                    lat: 1.0166,
                    lng: 117.578,
                },
                {
                    id: 214,
                    kabupaten: "Kota Bontang",
                    provinsi: "Kalimantan Timur",
                    lat: 0.1337,
                    lng: 117.5001,
                },
                // KALIMANTAN UTARA
                {
                    id: 215,
                    kabupaten: "Kota Tarakan",
                    provinsi: "Kalimantan Utara",
                    lat: 3.2986,
                    lng: 117.6297,
                },
                {
                    id: 216,
                    kabupaten: "Kab. Nunukan",
                    provinsi: "Kalimantan Utara",
                    lat: 4.1393,
                    lng: 117.661,
                },
                {
                    id: 217,
                    kabupaten: "Kab. Bulungan",
                    provinsi: "Kalimantan Utara",
                    lat: 2.8437,
                    lng: 117.2432,
                },
                {
                    id: 218,
                    kabupaten: "Kab. Malinau",
                    provinsi: "Kalimantan Utara",
                    lat: 3.586,
                    lng: 116.6325,
                },
                // SULAWESI SELATAN
                {
                    id: 219,
                    kabupaten: "Kota Makassar",
                    provinsi: "Sulawesi Selatan",
                    lat: -5.1477,
                    lng: 119.4327,
                },
                {
                    id: 220,
                    kabupaten: "Kab. Gowa",
                    provinsi: "Sulawesi Selatan",
                    lat: -5.2904,
                    lng: 119.6072,
                },
                {
                    id: 221,
                    kabupaten: "Kab. Maros",
                    provinsi: "Sulawesi Selatan",
                    lat: -5.0051,
                    lng: 119.5793,
                },
                {
                    id: 222,
                    kabupaten: "Kab. Pangkajene",
                    provinsi: "Sulawesi Selatan",
                    lat: -4.7715,
                    lng: 119.5285,
                },
                {
                    id: 223,
                    kabupaten: "Kab. Bone",
                    provinsi: "Sulawesi Selatan",
                    lat: -4.5407,
                    lng: 120.3294,
                },
                {
                    id: 224,
                    kabupaten: "Kab. Bulukumba",
                    provinsi: "Sulawesi Selatan",
                    lat: -5.559,
                    lng: 120.1974,
                },
                {
                    id: 225,
                    kabupaten: "Kab. Sinjai",
                    provinsi: "Sulawesi Selatan",
                    lat: -5.1209,
                    lng: 120.2456,
                },
                {
                    id: 226,
                    kabupaten: "Kab. Wajo",
                    provinsi: "Sulawesi Selatan",
                    lat: -4.1218,
                    lng: 120.0347,
                },
                {
                    id: 227,
                    kabupaten: "Kab. Soppeng",
                    provinsi: "Sulawesi Selatan",
                    lat: -4.3459,
                    lng: 119.8779,
                },
                {
                    id: 228,
                    kabupaten: "Kota Palopo",
                    provinsi: "Sulawesi Selatan",
                    lat: -2.9925,
                    lng: 120.1969,
                },
                {
                    id: 229,
                    kabupaten: "Kab. Luwu",
                    provinsi: "Sulawesi Selatan",
                    lat: -3.0678,
                    lng: 120.2527,
                },
                {
                    id: 230,
                    kabupaten: "Kota Parepare",
                    provinsi: "Sulawesi Selatan",
                    lat: -4.0136,
                    lng: 119.6198,
                },
                {
                    id: 231,
                    kabupaten: "Kab. Pinrang",
                    provinsi: "Sulawesi Selatan",
                    lat: -3.7881,
                    lng: 119.5821,
                },
                {
                    id: 232,
                    kabupaten: "Kab. Sidrap",
                    provinsi: "Sulawesi Selatan",
                    lat: -3.9481,
                    lng: 119.8491,
                },
                {
                    id: 233,
                    kabupaten: "Kab. Enrekang",
                    provinsi: "Sulawesi Selatan",
                    lat: -3.567,
                    lng: 119.789,
                },
                // SULAWESI TENGAH
                {
                    id: 234,
                    kabupaten: "Kota Palu",
                    provinsi: "Sulawesi Tengah",
                    lat: -0.9003,
                    lng: 119.8779,
                },
                {
                    id: 235,
                    kabupaten: "Kab. Donggala",
                    provinsi: "Sulawesi Tengah",
                    lat: -0.6782,
                    lng: 119.7493,
                },
                {
                    id: 236,
                    kabupaten: "Kab. Sigi",
                    provinsi: "Sulawesi Tengah",
                    lat: -1.2068,
                    lng: 119.9252,
                },
                {
                    id: 237,
                    kabupaten: "Kab. Poso",
                    provinsi: "Sulawesi Tengah",
                    lat: -1.3982,
                    lng: 120.7537,
                },
                {
                    id: 238,
                    kabupaten: "Kab. Morowali",
                    provinsi: "Sulawesi Tengah",
                    lat: -2.4977,
                    lng: 121.9497,
                },
                {
                    id: 239,
                    kabupaten: "Kab. Banggai",
                    provinsi: "Sulawesi Tengah",
                    lat: -1.3718,
                    lng: 122.6038,
                },
                {
                    id: 240,
                    kabupaten: "Kab. Toli-Toli",
                    provinsi: "Sulawesi Tengah",
                    lat: 1.1244,
                    lng: 120.7918,
                },
                {
                    id: 241,
                    kabupaten: "Kab. Buol",
                    provinsi: "Sulawesi Tengah",
                    lat: 1.1835,
                    lng: 121.4468,
                },
                // SULAWESI UTARA
                {
                    id: 242,
                    kabupaten: "Kota Manado",
                    provinsi: "Sulawesi Utara",
                    lat: 1.4748,
                    lng: 124.8421,
                },
                {
                    id: 243,
                    kabupaten: "Kab. Minahasa",
                    provinsi: "Sulawesi Utara",
                    lat: 1.3151,
                    lng: 124.8318,
                },
                {
                    id: 244,
                    kabupaten: "Kab. Minahasa Utara",
                    provinsi: "Sulawesi Utara",
                    lat: 1.6289,
                    lng: 125.0399,
                },
                {
                    id: 245,
                    kabupaten: "Kab. Minahasa Selatan",
                    provinsi: "Sulawesi Utara",
                    lat: 1.1082,
                    lng: 124.636,
                },
                {
                    id: 246,
                    kabupaten: "Kota Bitung",
                    provinsi: "Sulawesi Utara",
                    lat: 1.4415,
                    lng: 125.1984,
                },
                {
                    id: 247,
                    kabupaten: "Kota Tomohon",
                    provinsi: "Sulawesi Utara",
                    lat: 1.3213,
                    lng: 124.8278,
                },
                {
                    id: 248,
                    kabupaten: "Kab. Bolaang Mongondow",
                    provinsi: "Sulawesi Utara",
                    lat: 0.6,
                    lng: 124.0,
                },
                {
                    id: 249,
                    kabupaten: "Kota Kotamobagu",
                    provinsi: "Sulawesi Utara",
                    lat: 0.7278,
                    lng: 124.3043,
                },
                // GORONTALO
                {
                    id: 250,
                    kabupaten: "Kota Gorontalo",
                    provinsi: "Gorontalo",
                    lat: 0.5387,
                    lng: 123.0595,
                },
                {
                    id: 251,
                    kabupaten: "Kab. Gorontalo",
                    provinsi: "Gorontalo",
                    lat: 0.6936,
                    lng: 122.8228,
                },
                {
                    id: 252,
                    kabupaten: "Kab. Bone Bolango",
                    provinsi: "Gorontalo",
                    lat: 0.5548,
                    lng: 123.2267,
                },
                // SULAWESI TENGGARA
                {
                    id: 253,
                    kabupaten: "Kota Kendari",
                    provinsi: "Sulawesi Tenggara",
                    lat: -3.9747,
                    lng: 122.5136,
                },
                {
                    id: 254,
                    kabupaten: "Kab. Konawe",
                    provinsi: "Sulawesi Tenggara",
                    lat: -3.9777,
                    lng: 122.5136,
                },
                {
                    id: 255,
                    kabupaten: "Kab. Kolaka",
                    provinsi: "Sulawesi Tenggara",
                    lat: -4.0533,
                    lng: 121.6099,
                },
                {
                    id: 256,
                    kabupaten: "Kab. Buton",
                    provinsi: "Sulawesi Tenggara",
                    lat: -5.5348,
                    lng: 122.574,
                },
                {
                    id: 257,
                    kabupaten: "Kota Bau-Bau",
                    provinsi: "Sulawesi Tenggara",
                    lat: -5.4654,
                    lng: 122.5948,
                },
                {
                    id: 258,
                    kabupaten: "Kab. Muna",
                    provinsi: "Sulawesi Tenggara",
                    lat: -4.7706,
                    lng: 122.5834,
                },
                // MALUKU
                {
                    id: 259,
                    kabupaten: "Kota Ambon",
                    provinsi: "Maluku",
                    lat: -3.6557,
                    lng: 128.1908,
                },
                {
                    id: 260,
                    kabupaten: "Kab. Maluku Tengah",
                    provinsi: "Maluku",
                    lat: -3.3568,
                    lng: 129.706,
                },
                {
                    id: 261,
                    kabupaten: "Kab. Seram Bagian Barat",
                    provinsi: "Maluku",
                    lat: -3.1453,
                    lng: 128.3875,
                },
                {
                    id: 262,
                    kabupaten: "Kab. Maluku Tenggara",
                    provinsi: "Maluku",
                    lat: -5.6234,
                    lng: 132.74,
                },
                {
                    id: 263,
                    kabupaten: "Kota Tual",
                    provinsi: "Maluku",
                    lat: -5.6323,
                    lng: 132.7529,
                },
                {
                    id: 264,
                    kabupaten: "Kab. Buru",
                    provinsi: "Maluku",
                    lat: -3.282,
                    lng: 126.6817,
                },
                // MALUKU UTARA
                {
                    id: 265,
                    kabupaten: "Kota Ternate",
                    provinsi: "Maluku Utara",
                    lat: 0.7876,
                    lng: 127.382,
                },
                {
                    id: 266,
                    kabupaten: "Kota Tidore",
                    provinsi: "Maluku Utara",
                    lat: 0.6826,
                    lng: 127.4463,
                },
                {
                    id: 267,
                    kabupaten: "Kab. Halmahera Barat",
                    provinsi: "Maluku Utara",
                    lat: 1.072,
                    lng: 127.5283,
                },
                {
                    id: 268,
                    kabupaten: "Kab. Halmahera Utara",
                    provinsi: "Maluku Utara",
                    lat: 1.7989,
                    lng: 128.0476,
                },
                {
                    id: 269,
                    kabupaten: "Kab. Halmahera Selatan",
                    provinsi: "Maluku Utara",
                    lat: -0.5534,
                    lng: 127.8524,
                },
                // PAPUA
                {
                    id: 270,
                    kabupaten: "Kota Jayapura",
                    provinsi: "Papua",
                    lat: -2.5916,
                    lng: 140.669,
                },
                {
                    id: 271,
                    kabupaten: "Kab. Jayapura",
                    provinsi: "Papua",
                    lat: -2.5916,
                    lng: 140.669,
                },
                {
                    id: 272,
                    kabupaten: "Kab. Merauke",
                    provinsi: "Papua Selatan",
                    lat: -8.4935,
                    lng: 140.4017,
                },
                {
                    id: 273,
                    kabupaten: "Kab. Biak Numfor",
                    provinsi: "Papua",
                    lat: -1.1763,
                    lng: 136.0817,
                },
                {
                    id: 274,
                    kabupaten: "Kab. Nabire",
                    provinsi: "Papua Tengah",
                    lat: -3.368,
                    lng: 135.4918,
                },
                {
                    id: 275,
                    kabupaten: "Kab. Mimika",
                    provinsi: "Papua Tengah",
                    lat: -4.5473,
                    lng: 136.3939,
                },
                {
                    id: 276,
                    kabupaten: "Kab. Puncak Jaya",
                    provinsi: "Papua Tengah",
                    lat: -3.5429,
                    lng: 137.1189,
                },
                // PAPUA BARAT
                {
                    id: 277,
                    kabupaten: "Kota Sorong",
                    provinsi: "Papua Barat",
                    lat: -0.8762,
                    lng: 131.2571,
                },
                {
                    id: 278,
                    kabupaten: "Kab. Sorong",
                    provinsi: "Papua Barat",
                    lat: -1.0822,
                    lng: 131.5032,
                },
                {
                    id: 279,
                    kabupaten: "Kab. Manokwari",
                    provinsi: "Papua Barat",
                    lat: -0.86,
                    lng: 134.062,
                },
                {
                    id: 280,
                    kabupaten: "Kab. Fak-Fak",
                    provinsi: "Papua Barat",
                    lat: -2.9252,
                    lng: 132.2984,
                },
                {
                    id: 281,
                    kabupaten: "Kab. Kaimana",
                    provinsi: "Papua Barat",
                    lat: -3.6476,
                    lng: 133.7499,
                },
                // NUSA TENGGARA BARAT
                {
                    id: 282,
                    kabupaten: "Kota Mataram",
                    provinsi: "NTB",
                    lat: -8.5833,
                    lng: 116.1167,
                },
                {
                    id: 283,
                    kabupaten: "Kab. Lombok Barat",
                    provinsi: "NTB",
                    lat: -8.6526,
                    lng: 116.0994,
                },
                {
                    id: 284,
                    kabupaten: "Kab. Lombok Tengah",
                    provinsi: "NTB",
                    lat: -8.717,
                    lng: 116.2765,
                },
                {
                    id: 285,
                    kabupaten: "Kab. Lombok Timur",
                    provinsi: "NTB",
                    lat: -8.6151,
                    lng: 116.5869,
                },
                {
                    id: 286,
                    kabupaten: "Kab. Lombok Utara",
                    provinsi: "NTB",
                    lat: -8.3814,
                    lng: 116.1528,
                },
                {
                    id: 287,
                    kabupaten: "Kab. Sumbawa Barat",
                    provinsi: "NTB",
                    lat: -8.7857,
                    lng: 116.8977,
                },
                {
                    id: 288,
                    kabupaten: "Kab. Sumbawa",
                    provinsi: "NTB",
                    lat: -8.4889,
                    lng: 117.4213,
                },
                {
                    id: 289,
                    kabupaten: "Kab. Dompu",
                    provinsi: "NTB",
                    lat: -8.5379,
                    lng: 118.4628,
                },
                {
                    id: 290,
                    kabupaten: "Kab. Bima",
                    provinsi: "NTB",
                    lat: -8.4565,
                    lng: 118.7285,
                },
                {
                    id: 291,
                    kabupaten: "Kota Bima",
                    provinsi: "NTB",
                    lat: -8.4655,
                    lng: 118.7233,
                },
                // NUSA TENGGARA TIMUR
                {
                    id: 292,
                    kabupaten: "Kota Kupang",
                    provinsi: "NTT",
                    lat: -10.1772,
                    lng: 123.607,
                },
                {
                    id: 293,
                    kabupaten: "Kab. Kupang",
                    provinsi: "NTT",
                    lat: -10.175,
                    lng: 123.607,
                },
                {
                    id: 294,
                    kabupaten: "Kab. Timor Tengah Selatan",
                    provinsi: "NTT",
                    lat: -9.7456,
                    lng: 124.2272,
                },
                {
                    id: 295,
                    kabupaten: "Kab. Belu",
                    provinsi: "NTT",
                    lat: -9.2992,
                    lng: 124.8736,
                },
                {
                    id: 296,
                    kabupaten: "Kab. Ende",
                    provinsi: "NTT",
                    lat: -8.8505,
                    lng: 121.6625,
                },
                {
                    id: 297,
                    kabupaten: "Kab. Manggarai",
                    provinsi: "NTT",
                    lat: -8.6068,
                    lng: 120.4783,
                },
                {
                    id: 298,
                    kabupaten: "Kab. Sikka",
                    provinsi: "NTT",
                    lat: -8.6591,
                    lng: 122.2121,
                },
                {
                    id: 299,
                    kabupaten: "Kab. Flores Timur",
                    provinsi: "NTT",
                    lat: -8.3316,
                    lng: 122.9765,
                },
                // BALI
                {
                    id: 300,
                    kabupaten: "Kota Denpasar",
                    provinsi: "Bali",
                    lat: -8.6705,
                    lng: 115.2126,
                },
                {
                    id: 301,
                    kabupaten: "Kab. Badung",
                    provinsi: "Bali",
                    lat: -8.6244,
                    lng: 115.1807,
                },
                {
                    id: 302,
                    kabupaten: "Kab. Gianyar",
                    provinsi: "Bali",
                    lat: -8.5337,
                    lng: 115.3231,
                },
                {
                    id: 303,
                    kabupaten: "Kab. Tabanan",
                    provinsi: "Bali",
                    lat: -8.5411,
                    lng: 115.1237,
                },
                {
                    id: 304,
                    kabupaten: "Kab. Buleleng",
                    provinsi: "Bali",
                    lat: -8.1116,
                    lng: 115.0888,
                },
                {
                    id: 305,
                    kabupaten: "Kab. Klungkung",
                    provinsi: "Bali",
                    lat: -8.5431,
                    lng: 115.4024,
                },
                {
                    id: 306,
                    kabupaten: "Kab. Bangli",
                    provinsi: "Bali",
                    lat: -8.4578,
                    lng: 115.3566,
                },
                {
                    id: 307,
                    kabupaten: "Kab. Karangasem",
                    provinsi: "Bali",
                    lat: -8.4536,
                    lng: 115.6069,
                },
                {
                    id: 308,
                    kabupaten: "Kab. Jembrana",
                    provinsi: "Bali",
                    lat: -8.361,
                    lng: 114.6238,
                },
                // ACEH
                {
                    id: 309,
                    kabupaten: "Kota Banda Aceh",
                    provinsi: "Aceh",
                    lat: 5.5577,
                    lng: 95.3222,
                },
                {
                    id: 310,
                    kabupaten: "Kab. Aceh Besar",
                    provinsi: "Aceh",
                    lat: 5.478,
                    lng: 95.439,
                },
                {
                    id: 311,
                    kabupaten: "Kab. Pidie",
                    provinsi: "Aceh",
                    lat: 4.9706,
                    lng: 96.0818,
                },
                {
                    id: 312,
                    kabupaten: "Kab. Aceh Utara",
                    provinsi: "Aceh",
                    lat: 5.1118,
                    lng: 96.9993,
                },
                {
                    id: 313,
                    kabupaten: "Kota Lhokseumawe",
                    provinsi: "Aceh",
                    lat: 5.1801,
                    lng: 97.1381,
                },
                {
                    id: 314,
                    kabupaten: "Kab. Aceh Timur",
                    provinsi: "Aceh",
                    lat: 4.5953,
                    lng: 97.7627,
                },
                {
                    id: 315,
                    kabupaten: "Kota Langsa",
                    provinsi: "Aceh",
                    lat: 4.4683,
                    lng: 97.9671,
                },
                {
                    id: 316,
                    kabupaten: "Kab. Aceh Tamiang",
                    provinsi: "Aceh",
                    lat: 4.3239,
                    lng: 97.9888,
                },
                {
                    id: 317,
                    kabupaten: "Kab. Aceh Selatan",
                    provinsi: "Aceh",
                    lat: 3.2929,
                    lng: 97.203,
                },
                {
                    id: 318,
                    kabupaten: "Kab. Aceh Barat",
                    provinsi: "Aceh",
                    lat: 4.205,
                    lng: 96.0284,
                },
                {
                    id: 319,
                    kabupaten: "Kab. Aceh Tengah",
                    provinsi: "Aceh",
                    lat: 4.6265,
                    lng: 96.8019,
                },
                {
                    id: 320,
                    kabupaten: "Kab. Bener Meriah",
                    provinsi: "Aceh",
                    lat: 4.7179,
                    lng: 96.8527,
                },
            ];
            // Legacy data end — not used
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
                const isPast = date < today && !isToday;

                days.push({
                    key: "d" + hijriDay,
                    hijriDay: hijriDay,
                    masehiDay: masehiDay,
                    month: date.getMonth(), // 0=Jan, 1=Feb, 2=Mar
                    dayOfWeek: date.getDay(), // 0=Sun
                    isToday: isToday,
                    isCompleted: isCompleted,
                    isPast: isPast,
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
        setPrayerTimes() {
            const times = {
                imsak: "04:13",
                subuh: "04:23",
                terbit: "05:42",
                dhuha: "06:15",
                dzuhur: "11:52",
                ashar: "15:13",
                maghrib: "17:55",
                isya: "19:08",
            };
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
            // Re-run setPrayerTimes when location changes (future: use coords for actual calculation)
            this.setPrayerTimes();
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
            setInterval(tick, 30000);
        },

        // ── Dua & Verse ────────────────────────────────────────────────────
        setDuas() {
            this.duas = [
                {
                    title: "Doa Niat Puasa",
                    arabic: "نَوَيْتُ صَوْمَ غَدٍ عَنْ أَدَاءِ فَرْضِ شَهْرِ رَمَضَانَ هٰذِهِ السَّنَةِ لِلّٰهِ تَعَالَى",
                    latin: "Nawaitu shauma ghadin 'an adaa-i fardhi syahri ramadhaana haadzihis sanati lillaahi ta'aalaa",
                    meaning:
                        "Aku berniat puasa esok hari untuk menunaikan kewajiban di bulan Ramadhan tahun ini karena Allah Ta'ala.",
                },
                {
                    title: "Doa Berbuka Puasa",
                    arabic: "اَللّٰهُمَّ لَكَ صُمْتُ وَبِكَ اٰمَنْتُ وَعَلَى رِزْقِكَ أَفْطَرْتُ",
                    latin: "Allahumma laka shumtu wa bika aamantu wa 'ala rizqika afthartu",
                    meaning:
                        "Ya Allah, untuk-Mu aku berpuasa, kepada-Mu aku beriman, dan dengan rezeki-Mu aku berbuka.",
                },
                {
                    title: "Doa Setelah Adzan",
                    arabic: "اَللّٰهُمَّ رَبَّ هٰذِهِ الدَّعْوَةِ التَّامَّةِ وَالصَّلاَةِ الْقَائِمَةِ اٰتِ مُحَمَّدًا الْوَسِيْلَةَ وَالْفَضِيْلَةَ",
                    latin: "Allahumma rabba haadzihid da'watit taammah, wash sholaatil qoo-imah, aati muhammadanil wasiilata wal fadhiilah",
                    meaning:
                        "Ya Allah, Tuhan pemilik seruan yang sempurna ini dan sholat yang akan ditegakkan, berikanlah kepada Muhammad wasilah dan keutamaan.",
                },
                {
                    title: "Doa Lailatul Qadr",
                    arabic: "اَللّٰهُمَّ إِنَّكَ عَفُوٌّ تُحِبُّ الْعَفْوَ فَاعْفُ عَنِّي",
                    latin: "Allahumma innaka 'afuwwun tuhibbul 'afwa fa'fu 'annii",
                    meaning:
                        "Ya Allah, sesungguhnya Engkau Maha Pemaaf dan menyukai maaf, maka maafkanlah aku.",
                },
            ];
        },

        setDailyVerse() {
            const verses = [
                {
                    text: '"Sesungguhnya bersama kesulitan ada kemudahan."',
                    source: "QS. Al-Insyirah: 6",
                },
                {
                    text: '"Hai orang-orang yang beriman, diwajibkan atas kamu berpuasa sebagaimana diwajibkan atas orang-orang sebelum kamu agar kamu bertakwa."',
                    source: "QS. Al-Baqarah: 183",
                },
                {
                    text: '"Dan apabila hamba-hamba-Ku bertanya kepadamu tentang Aku, maka sesungguhnya Aku dekat."',
                    source: "QS. Al-Baqarah: 186",
                },
                {
                    text: '"Bulan Ramadhan adalah bulan yang di dalamnya diturunkan Al-Quran, sebagai petunjuk bagi manusia."',
                    source: "QS. Al-Baqarah: 185",
                },
                {
                    text: '"Sesungguhnya Allah tidak akan mengubah nasib suatu kaum hingga mereka mengubah diri mereka sendiri."',
                    source: "QS. Ar-Ra'd: 11",
                },
            ];
            this.dailyVerse = verses[new Date().getDate() % verses.length];
        },

        // ── Location ───────────────────────────────────────────────────────
        getLocation() {
            this.locationText = "Mendeteksi lokasi...";
            this.locationCity = "Mendeteksi lokasi...";
            this.locationCoords = "";
            if (!navigator.geolocation) {
                this.setDefaultLocation();
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.userLat = pos.coords.latitude;
                    this.userLng = pos.coords.longitude;
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
                            const kabupaten =
                                addr.county ||
                                addr.city ||
                                addr.state_district ||
                                addr.town ||
                                addr.state ||
                                "Lokasi Anda";
                            const provinsi = addr.state || "";
                            const clean = kabupaten
                                .replace(/^Kabupaten\s+/i, "Kab. ")
                                .replace(/^Kota\s+/i, "Kota ");
                            this.locationCity = provinsi
                                ? clean + ", " + provinsi
                                : clean;
                            this.cityName = clean;
                        })
                        .catch(() => {
                            this.locationCity = this.locationCoords;
                        });
                },
                () => {
                    this.setDefaultLocation();
                },
                { enableHighAccuracy: true, timeout: 10000 },
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
        },

        useGPS() {
            this.showLocationPicker = false;
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
            this.showLocationPicker = false;
            this.locationSearch = "";
            this.filteredLocations = this.indonesiaLocations;
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
                "Arah " +
                this.qiblaDirection.toFixed(1) +
                " derajat dari utara";
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

        getProgressPercent() {
            return Math.round((this.submittedDays.length / 30) * 100);
        },
    };
}
