"use strict";

/**
 * Al-Quran Page — Alpine.js component
 * Uses server-side Repository Pattern proxy (/api/quran/*) with Redis caching
 * Supports multiple reciters (qari)
 */
function alQuranPage() {
    // Backend API base (our Laravel proxy with Redis cache)
    var _backendBase = "/api/quran";

    // Juz-to-surah mapping (approximate: which surah numbers roughly fall in each juz)
    var _juzSurahMap = {
        1: [1, 2],
        2: [2],
        3: [2, 3],
        4: [3, 4],
        5: [4],
        6: [4, 5],
        7: [5, 6],
        8: [6, 7],
        9: [7, 8],
        10: [8, 9],
        11: [9, 10, 11],
        12: [11, 12],
        13: [12, 13, 14],
        14: [15, 16],
        15: [17, 18],
        16: [18, 19, 20],
        17: [21, 22],
        18: [23, 24, 25],
        19: [25, 26, 27],
        20: [27, 28, 29],
        21: [29, 30, 31, 32, 33],
        22: [33, 34, 35, 36],
        23: [36, 37, 38, 39],
        24: [39, 40, 41],
        25: [41, 42, 43, 44, 45],
        26: [46, 47, 48, 49, 50, 51],
        27: [51, 52, 53, 54, 55, 56, 57],
        28: [58, 59, 60, 61, 62, 63, 64, 65, 66],
        29: [67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77],
        30: [
            78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94,
            95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108,
            109, 110, 111, 112, 113, 114,
        ],
    };

    // Indonesian surah meanings
    var _surahIdMeaning = {
        1: "Pembuka",
        2: "Sapi Betina",
        3: "Keluarga Imran",
        4: "Wanita",
        5: "Hidangan",
        6: "Binatang Ternak",
        7: "Tempat Tertinggi",
        8: "Rampasan Perang",
        9: "Pengampunan",
        10: "Yunus",
        11: "Hud",
        12: "Yusuf",
        13: "Guruh",
        14: "Ibrahim",
        15: "Batu",
        16: "Lebah",
        17: "Perjalanan Malam",
        18: "Gua",
        19: "Maryam",
        20: "Ta Ha",
        21: "Para Nabi",
        22: "Haji",
        23: "Orang-orang Beriman",
        24: "Cahaya",
        25: "Pembeda",
        26: "Para Penyair",
        27: "Semut",
        28: "Kisah-kisah",
        29: "Laba-laba",
        30: "Bangsa Romawi",
        31: "Luqman",
        32: "Sajdah",
        33: "Golongan Bersekutu",
        34: "Kaum Saba",
        35: "Pencipta",
        36: "Ya Sin",
        37: "Yang Berbaris",
        38: "Shad",
        39: "Rombongan",
        40: "Yang Maha Pengampun",
        41: "Yang Dijelaskan",
        42: "Musyawarah",
        43: "Perhiasan",
        44: "Asap",
        45: "Yang Berlutut",
        46: "Bukit Pasir",
        47: "Muhammad",
        48: "Kemenangan",
        49: "Kamar-kamar",
        50: "Qaf",
        51: "Angin yang Menerbangkan",
        52: "Bukit Sinai",
        53: "Bintang",
        54: "Bulan",
        55: "Yang Maha Pengasih",
        56: "Hari Kiamat",
        57: "Besi",
        58: "Wanita yang Mengajukan Gugatan",
        59: "Pengusiran",
        60: "Wanita yang Diuji",
        61: "Barisan",
        62: "Jumat",
        63: "Orang-orang Munafik",
        64: "Pengungkapan Kesalahan",
        65: "Talak",
        66: "Pengharaman",
        67: "Kerajaan",
        68: "Pena",
        69: "Hari Kiamat",
        70: "Tempat-tempat Naik",
        71: "Nuh",
        72: "Jin",
        73: "Orang yang Berselimut",
        74: "Orang yang Berkemul",
        75: "Hari Kiamat",
        76: "Manusia",
        77: "Malaikat yang Diutus",
        78: "Berita Besar",
        79: "Malaikat yang Mencabut",
        80: "Bermuka Masam",
        81: "Penggulungan",
        82: "Terbelah",
        83: "Orang-orang yang Curang",
        84: "Terbelahnya Langit",
        85: "Gugusan Bintang",
        86: "Yang Datang di Malam Hari",
        87: "Yang Paling Tinggi",
        88: "Hari Pembalasan",
        89: "Fajar",
        90: "Negeri",
        91: "Matahari",
        92: "Malam",
        93: "Waktu Dhuha",
        94: "Kelapangan",
        95: "Buah Tin",
        96: "Segumpal Darah",
        97: "Malam Kemuliaan",
        98: "Bukti Nyata",
        99: "Kegoncangan",
        100: "Kuda Perang",
        101: "Hari Kiamat",
        102: "Bermegah-megahan",
        103: "Masa",
        104: "Pengumpat",
        105: "Gajah",
        106: "Suku Quraisy",
        107: "Barang yang Berguna",
        108: "Nikmat yang Berlimpah",
        109: "Orang-orang Kafir",
        110: "Pertolongan",
        111: "Sabut",
        112: "Ikhlas",
        113: "Waktu Subuh",
        114: "Manusia",
    };

    return {
        // State
        view: "list", // 'list' | 'read'
        showSearch: false,
        searchQuery: "",
        selectedJuz: 0,

        // Surah list
        allSurahs: [],
        displayedSurahs: [],
        loadingSurahs: true,

        // Surah reader
        currentSurah: {},
        currentAyahs: [],
        filteredAyahs: [],
        ayahSearchQuery: "",
        loadingAyahs: false,
        ayahError: "",

        // Audio
        playingIndex: -1, // index in currentAyahs
        isPlaying: false,
        autoPlay: false,
        audioLoading: false,
        audioDuration: 0,
        audioCurrentTime: 0,
        _audioEl: null,
        _audioTimer: null,

        // Reciters (Qari)
        reciters: [],
        selectedReciter: "ar.alafasy",
        showReciterPicker: false,

        // Cache
        _surahCache: {},

        getSurahTranslation(number) {
            return _surahIdMeaning[number] || "";
        },

        init() {
            this.loadSurahList();
            this.loadReciters();
        },

        async loadSurahList() {
            this.loadingSurahs = true;
            try {
                var res = await fetch(_backendBase + "/surahs");
                var json = await res.json();
                if (json.code === 200 && json.data) {
                    this.allSurahs = json.data;
                    this.displayedSurahs = json.data;
                } else {
                    console.error("Failed to load surah list:", json);
                    this.allSurahs = [];
                    this.displayedSurahs = [];
                }
            } catch (e) {
                console.error("Surah list fetch error:", e);
                this.allSurahs = [];
                this.displayedSurahs = [];
            }
            this.loadingSurahs = false;
        },

        async loadReciters() {
            try {
                var res = await fetch(_backendBase + "/reciters");
                var json = await res.json();
                if (json.code === 200 && json.data) {
                    this.reciters = json.data;
                }
            } catch (e) {
                console.error("Reciters fetch error:", e);
                // Fallback
                this.reciters = [
                    {
                        id: "ar.alafasy",
                        name: "Mishary Rashid Alafasy",
                        style: "Murattal",
                    },
                ];
            }
        },

        filterSurahs() {
            var q = this.searchQuery.toLowerCase().trim();
            var juz = this.selectedJuz;
            var surahs = this.allSurahs;

            if (juz > 0) {
                var juzNums = _juzSurahMap[juz] || [];
                surahs = surahs.filter(function (s) {
                    return juzNums.indexOf(s.number) !== -1;
                });
            }

            if (q) {
                surahs = surahs.filter(function (s) {
                    return (
                        s.englishName.toLowerCase().indexOf(q) !== -1 ||
                        s.englishNameTranslation.toLowerCase().indexOf(q) !==
                            -1 ||
                        s.name.indexOf(q) !== -1 ||
                        String(s.number) === q
                    );
                });
            }

            this.displayedSurahs = surahs;
        },

        async openSurah(num) {
            if (num < 1 || num > 114) return;
            this.view = "read";
            this.ayahError = "";
            this.currentAyahs = [];
            this.filteredAyahs = [];
            this.ayahSearchQuery = "";

            // Find surah info
            var info = this.allSurahs.find(function (s) {
                return s.number === num;
            });
            if (info) {
                this.currentSurah = info;
            } else {
                this.currentSurah = {
                    number: num,
                    name: "",
                    englishName: "Surah " + num,
                    numberOfAyahs: 0,
                    revelationType: "",
                };
            }

            // Scroll to top
            window.scrollTo({ top: 0, behavior: "smooth" });

            // Cache key includes reciter edition
            var cacheKey = num + "_" + this.selectedReciter;

            // Check cache
            if (this._surahCache[cacheKey]) {
                this.currentAyahs = this._surahCache[cacheKey].ayahs;
                this.filteredAyahs = this._surahCache[cacheKey].ayahs;
                if (this._surahCache[cacheKey].surah) {
                    this.currentSurah = this._surahCache[cacheKey].surah;
                }
                return;
            }

            this.loadingAyahs = true;
            try {
                // Fetch from our backend proxy (cached by Redis)
                var res = await fetch(
                    _backendBase +
                        "/surah/" +
                        num +
                        "?edition=" +
                        encodeURIComponent(this.selectedReciter),
                );
                var json = await res.json();

                if (json.code === 200 && json.ayahs) {
                    this.currentAyahs = json.ayahs;
                    this.filteredAyahs = json.ayahs;
                    if (json.surah) {
                        this.currentSurah = json.surah;
                    }
                    // Store in client-side cache too
                    this._surahCache[cacheKey] = {
                        surah: json.surah,
                        ayahs: json.ayahs,
                    };
                } else {
                    this.ayahError =
                        json.message || "Gagal memuat ayat. Silakan coba lagi.";
                }
            } catch (e) {
                console.error("Ayah fetch error:", e);
                this.ayahError =
                    "Terjadi kesalahan jaringan. Pastikan koneksi internet Anda stabil.";
            }
            this.loadingAyahs = false;
        },

        goBack() {
            this.stopAudio();
            this.view = "list";
            this.ayahError = "";
            this.ayahSearchQuery = "";
            this.filteredAyahs = [];
            window.scrollTo({ top: 0, behavior: "smooth" });
        },

        // ── Ayah search / filter ────────────────────────────
        filterAyahs() {
            var q = this.ayahSearchQuery.trim().toLowerCase();
            if (!q) {
                this.filteredAyahs = this.currentAyahs;
                return;
            }
            this.filteredAyahs = this.currentAyahs.filter(function (ayah) {
                // Match by ayah number
                if (String(ayah.numberInSurah) === q) return true;
                // Match by translation text
                if (
                    ayah.translation &&
                    ayah.translation.toLowerCase().indexOf(q) !== -1
                )
                    return true;
                // Match by arabic text
                if (ayah.arabic && ayah.arabic.indexOf(q) !== -1) return true;
                return false;
            });
        },

        // ── Audio playback ──────────────────────────────────
        _ensureAudio() {
            if (!this._audioEl) {
                this._audioEl = new Audio();
                this._audioEl.preload = "auto";

                var self = this;
                this._audioEl.addEventListener("ended", function () {
                    self._clearTimer();
                    self.isPlaying = false;
                    self.audioCurrentTime = 0;

                    var next = self.playingIndex + 1;
                    if (next < self.currentAyahs.length) {
                        // Next ayah in same surah
                        if (self.autoPlay) {
                            self.playAyah(next);
                        } else {
                            self.playingIndex = -1;
                        }
                    } else {
                        // End of surah — always auto-advance to next surah
                        if (self.currentSurah.number < 114) {
                            self.autoPlay = true;
                            self.openSurah(self.currentSurah.number + 1).then(
                                function () {
                                    if (self.currentAyahs.length > 0) {
                                        self.playAyah(0);
                                    }
                                },
                            );
                        } else {
                            self.playingIndex = -1;
                            self.autoPlay = false;
                        }
                    }
                });

                this._audioEl.addEventListener("loadedmetadata", function () {
                    self.audioDuration = self._audioEl.duration || 0;
                    self.audioLoading = false;
                });

                this._audioEl.addEventListener("error", function () {
                    self.audioLoading = false;
                    self.isPlaying = false;
                    console.error("Audio playback error");
                });
            }
            return this._audioEl;
        },

        _startTimer() {
            this._clearTimer();
            var self = this;
            this._audioTimer = setInterval(function () {
                if (self._audioEl) {
                    self.audioCurrentTime = self._audioEl.currentTime || 0;
                }
            }, 250);
        },

        _clearTimer() {
            if (this._audioTimer) {
                clearInterval(this._audioTimer);
                this._audioTimer = null;
            }
        },

        playAyah(index) {
            var ayah = this.currentAyahs[index];
            if (!ayah || !ayah.audio) return;

            var audio = this._ensureAudio();

            // If same ayah is playing, toggle pause/resume
            if (this.playingIndex === index && !audio.paused) {
                audio.pause();
                this.isPlaying = false;
                this._clearTimer();
                return;
            }
            if (this.playingIndex === index && audio.paused && audio.src) {
                audio.play();
                this.isPlaying = true;
                this._startTimer();
                return;
            }

            // New ayah
            this.playingIndex = index;
            this.audioLoading = true;
            this.audioCurrentTime = 0;
            this.audioDuration = 0;
            audio.src = ayah.audio;
            audio.load();

            var self = this;
            audio
                .play()
                .then(function () {
                    self.isPlaying = true;
                    self._startTimer();
                    // Scroll ayah into view
                    self._scrollToAyah(index);
                })
                .catch(function (e) {
                    console.error("Play failed:", e);
                    self.audioLoading = false;
                });
        },

        toggleAutoPlay() {
            this.autoPlay = !this.autoPlay;
            // If turning on and nothing is playing, start from first ayah
            if (
                this.autoPlay &&
                this.playingIndex === -1 &&
                this.currentAyahs.length > 0
            ) {
                this.playAyah(0);
            }
        },

        stopAudio() {
            this._clearTimer();
            if (this._audioEl) {
                this._audioEl.pause();
                this._audioEl.currentTime = 0;
                this._audioEl.src = "";
            }
            this.playingIndex = -1;
            this.isPlaying = false;
            this.audioLoading = false;
            this.audioCurrentTime = 0;
            this.audioDuration = 0;
        },

        seekAudio(event) {
            if (!this._audioEl || !this.audioDuration) return;
            var bar = event.currentTarget;
            var rect = bar.getBoundingClientRect();
            var pct = (event.clientX - rect.left) / rect.width;
            pct = Math.max(0, Math.min(1, pct));
            this._audioEl.currentTime = pct * this.audioDuration;
            this.audioCurrentTime = this._audioEl.currentTime;
        },

        _scrollToAyah(index) {
            this.$nextTick(function () {
                var el = document.getElementById("ayah-" + index);
                if (el) {
                    el.scrollIntoView({ behavior: "smooth", block: "center" });
                }
            });
        },

        formatTime(sec) {
            if (!sec || isNaN(sec)) return "0:00";
            var m = Math.floor(sec / 60);
            var s = Math.floor(sec % 60);
            return m + ":" + (s < 10 ? "0" : "") + s;
        },

        // ── Reciter (Qari) selection ────────────────────────
        getReciterName() {
            var sel = this.selectedReciter;
            var r = this.reciters.find(function (q) {
                return q.id === sel;
            });
            return r ? r.name : "Mishary Alafasy";
        },

        changeReciter(reciterId) {
            if (reciterId === this.selectedReciter) {
                this.showReciterPicker = false;
                return;
            }
            this.stopAudio();
            this.selectedReciter = reciterId;
            this.showReciterPicker = false;

            // If currently viewing a surah, reload with new reciter
            if (this.view === "read" && this.currentSurah.number) {
                this.openSurah(this.currentSurah.number);
            }
        },
    };
}
