/**
 * Buku Ramadhan — Formulir Harian Alpine.js Component
 */

/* ── 114 Surah data (number, name, total ayat) ── */
const QURAN_SURAHS = [
    { number: 1, name: "Al-Fatihah", ayat: 7 },
    { number: 2, name: "Al-Baqarah", ayat: 286 },
    { number: 3, name: "Ali 'Imran", ayat: 200 },
    { number: 4, name: "An-Nisa'", ayat: 176 },
    { number: 5, name: "Al-Ma'idah", ayat: 120 },
    { number: 6, name: "Al-An'am", ayat: 165 },
    { number: 7, name: "Al-A'raf", ayat: 206 },
    { number: 8, name: "Al-Anfal", ayat: 75 },
    { number: 9, name: "At-Taubah", ayat: 129 },
    { number: 10, name: "Yunus", ayat: 109 },
    { number: 11, name: "Hud", ayat: 123 },
    { number: 12, name: "Yusuf", ayat: 111 },
    { number: 13, name: "Ar-Ra'd", ayat: 43 },
    { number: 14, name: "Ibrahim", ayat: 52 },
    { number: 15, name: "Al-Hijr", ayat: 99 },
    { number: 16, name: "An-Nahl", ayat: 128 },
    { number: 17, name: "Al-Isra'", ayat: 111 },
    { number: 18, name: "Al-Kahf", ayat: 110 },
    { number: 19, name: "Maryam", ayat: 98 },
    { number: 20, name: "Taha", ayat: 135 },
    { number: 21, name: "Al-Anbiya'", ayat: 112 },
    { number: 22, name: "Al-Hajj", ayat: 78 },
    { number: 23, name: "Al-Mu'minun", ayat: 118 },
    { number: 24, name: "An-Nur", ayat: 64 },
    { number: 25, name: "Al-Furqan", ayat: 77 },
    { number: 26, name: "Asy-Syu'ara'", ayat: 227 },
    { number: 27, name: "An-Naml", ayat: 93 },
    { number: 28, name: "Al-Qasas", ayat: 88 },
    { number: 29, name: "Al-'Ankabut", ayat: 69 },
    { number: 30, name: "Ar-Rum", ayat: 60 },
    { number: 31, name: "Luqman", ayat: 34 },
    { number: 32, name: "As-Sajdah", ayat: 30 },
    { number: 33, name: "Al-Ahzab", ayat: 73 },
    { number: 34, name: "Saba'", ayat: 54 },
    { number: 35, name: "Fatir", ayat: 45 },
    { number: 36, name: "Ya-Sin", ayat: 83 },
    { number: 37, name: "As-Saffat", ayat: 182 },
    { number: 38, name: "Sad", ayat: 88 },
    { number: 39, name: "Az-Zumar", ayat: 75 },
    { number: 40, name: "Ghafir", ayat: 85 },
    { number: 41, name: "Fussilat", ayat: 54 },
    { number: 42, name: "Asy-Syura", ayat: 53 },
    { number: 43, name: "Az-Zukhruf", ayat: 89 },
    { number: 44, name: "Ad-Dukhan", ayat: 59 },
    { number: 45, name: "Al-Jasiyah", ayat: 37 },
    { number: 46, name: "Al-Ahqaf", ayat: 35 },
    { number: 47, name: "Muhammad", ayat: 38 },
    { number: 48, name: "Al-Fath", ayat: 29 },
    { number: 49, name: "Al-Hujurat", ayat: 18 },
    { number: 50, name: "Qaf", ayat: 45 },
    { number: 51, name: "Az-Zariyat", ayat: 60 },
    { number: 52, name: "At-Tur", ayat: 49 },
    { number: 53, name: "An-Najm", ayat: 62 },
    { number: 54, name: "Al-Qamar", ayat: 55 },
    { number: 55, name: "Ar-Rahman", ayat: 78 },
    { number: 56, name: "Al-Waqi'ah", ayat: 96 },
    { number: 57, name: "Al-Hadid", ayat: 29 },
    { number: 58, name: "Al-Mujadalah", ayat: 22 },
    { number: 59, name: "Al-Hasyr", ayat: 24 },
    { number: 60, name: "Al-Mumtahanah", ayat: 13 },
    { number: 61, name: "As-Saff", ayat: 14 },
    { number: 62, name: "Al-Jumu'ah", ayat: 11 },
    { number: 63, name: "Al-Munafiqun", ayat: 11 },
    { number: 64, name: "At-Tagabun", ayat: 18 },
    { number: 65, name: "At-Talaq", ayat: 12 },
    { number: 66, name: "At-Tahrim", ayat: 12 },
    { number: 67, name: "Al-Mulk", ayat: 30 },
    { number: 68, name: "Al-Qalam", ayat: 52 },
    { number: 69, name: "Al-Haqqah", ayat: 52 },
    { number: 70, name: "Al-Ma'arij", ayat: 44 },
    { number: 71, name: "Nuh", ayat: 28 },
    { number: 72, name: "Al-Jinn", ayat: 28 },
    { number: 73, name: "Al-Muzzammil", ayat: 20 },
    { number: 74, name: "Al-Muddassir", ayat: 56 },
    { number: 75, name: "Al-Qiyamah", ayat: 40 },
    { number: 76, name: "Al-Insan", ayat: 31 },
    { number: 77, name: "Al-Mursalat", ayat: 50 },
    { number: 78, name: "An-Naba'", ayat: 40 },
    { number: 79, name: "An-Nazi'at", ayat: 46 },
    { number: 80, name: "'Abasa", ayat: 42 },
    { number: 81, name: "At-Takwir", ayat: 29 },
    { number: 82, name: "Al-Infitar", ayat: 19 },
    { number: 83, name: "Al-Mutaffifin", ayat: 36 },
    { number: 84, name: "Al-Insyiqaq", ayat: 25 },
    { number: 85, name: "Al-Buruj", ayat: 22 },
    { number: 86, name: "At-Tariq", ayat: 17 },
    { number: 87, name: "Al-A'la", ayat: 19 },
    { number: 88, name: "Al-Gasyiyah", ayat: 26 },
    { number: 89, name: "Al-Fajr", ayat: 30 },
    { number: 90, name: "Al-Balad", ayat: 20 },
    { number: 91, name: "Asy-Syams", ayat: 15 },
    { number: 92, name: "Al-Lail", ayat: 21 },
    { number: 93, name: "Ad-Duha", ayat: 11 },
    { number: 94, name: "Al-Insyirah", ayat: 8 },
    { number: 95, name: "At-Tin", ayat: 8 },
    { number: 96, name: "Al-'Alaq", ayat: 19 },
    { number: 97, name: "Al-Qadr", ayat: 5 },
    { number: 98, name: "Al-Bayyinah", ayat: 8 },
    { number: 99, name: "Az-Zalzalah", ayat: 8 },
    { number: 100, name: "Al-'Adiyat", ayat: 11 },
    { number: 101, name: "Al-Qari'ah", ayat: 11 },
    { number: 102, name: "At-Takasur", ayat: 8 },
    { number: 103, name: "Al-'Asr", ayat: 3 },
    { number: 104, name: "Al-Humazah", ayat: 9 },
    { number: 105, name: "Al-Fil", ayat: 5 },
    { number: 106, name: "Quraisy", ayat: 4 },
    { number: 107, name: "Al-Ma'un", ayat: 7 },
    { number: 108, name: "Al-Kausar", ayat: 3 },
    { number: 109, name: "Al-Kafirun", ayat: 6 },
    { number: 110, name: "An-Nasr", ayat: 3 },
    { number: 111, name: "Al-Lahab", ayat: 5 },
    { number: 112, name: "Al-Ikhlas", ayat: 4 },
    { number: 113, name: "Al-Falaq", ayat: 5 },
    { number: 114, name: "An-Nas", ayat: 6 },
];

function formulirHarian() {
    return {
        formDay: 1,
        formSubmitted: false,
        formSaving: false,
        showSuccessPopup: false,
        showValidationError: false,
        validationMessage: "",
        submittedDays: [],
        submissionStatuses: {},
        currentDayStatus: "",
        currentDayNote: "",
        configLoaded: false,
        formDisabled: false,
        formDisabledMessage: "",

        /* ── Dynamic config from server ── */
        sectionConfig: [],
        enabledSections: {},
        extraSections: [],

        /* ── Surah autocomplete ── */
        allSurahs: QURAN_SURAHS,
        filteredSurahs: [],
        showSurahList: false,
        selectedSurahAyat: 0,
        ayatError: "",

        /* ── Editor format state ── */
        editorFormats: {
            bold: false,
            italic: false,
            underline: false,
            ul: false,
            ol: false,
        },

        updateEditorFormats() {
            this.editorFormats.bold = document.queryCommandState("bold");
            this.editorFormats.italic = document.queryCommandState("italic");
            this.editorFormats.underline =
                document.queryCommandState("underline");
            this.editorFormats.ul = document.queryCommandState(
                "insertUnorderedList",
            );
            this.editorFormats.ol =
                document.queryCommandState("insertOrderedList");
        },

        /* ── Puasa reason suggestions ── */
        showPuasaSuggest: false,
        puasaSuggestions: [
            "Sakit (demam, maag, dll)",
            "Haid",
            "Lupa niat / tidak sahur",
            "Kondisi tubuh tidak kuat",
            "Bepergian jauh",
            "Izin orang tua",
        ],

        /* ── Dynamic item arrays (populated from config) ── */
        sholatFarduItems: [
            { key: "subuh", label: "Subuh" },
            { key: "dzuhur", label: "Dzuhur" },
            { key: "ashar", label: "Ashar" },
            { key: "maghrib", label: "Maghrib" },
            { key: "isya", label: "Isya" },
        ],
        sholatFarduOptions: ["jamaah", "munfarid", "tidak"],
        tarawihItems: [{ key: "tarawih", label: "Tarawih" }],
        tarawihOptions: ["jamaah", "munfarid", "tidak"],
        sholatSunatItems: [
            { key: "rowatib", label: "Rowatib" },
            { key: "tahajud", label: "Tahajud" },
            { key: "dhuha", label: "Dhuha" },
        ],
        sholatSunatOptions: ["ya", "tidak"],

        /* ── Kegiatan groups (matches paper form) ── */
        kegiatanGroupA: [
            { key: "dzikir_pagi", label: "Dzikir Pagi" },
            { key: "olahraga", label: "Olahraga Ringan" },
            { key: "membantu_ortu", label: "Membantu Orang Tua" },
            { key: "membersihkan_kamar", label: "Membersihkan Kamar" },
            { key: "membersihkan_rumah", label: "Membersihkan Rumah" },
            { key: "membersihkan_halaman", label: "Membersihkan Halaman" },
            { key: "merawat_lingkungan", label: "Merawat Lingkungan" },
            { key: "dzikir_petang", label: "Dzikir Petang" },
            { key: "sedekah", label: "Sedekah / Poe Ibu" },
            { key: "buka_keluarga", label: "Buka Bersama Keluarga" },
        ],
        kegiatanGroupB: [
            { key: "kajian", label: "Kajian Al-Quran, Tafsir & Hadits" },
        ],
        kegiatanGroupC: [
            { key: "menabung", label: "Menabung" },
            { key: "tidur_cepat", label: "Tidur Cepat" },
            { key: "bangun_pagi", label: "Bangun Pagi / Sahur" },
        ],

        /* ── Section titles (dynamic from config) ── */
        sectionTitles: {
            puasa: "Puasa",
            sholat_fardu: "Sholat Fardu",
            tarawih: "Sholat Tarawih",
            sholat_sunat: "Sholat Sunat",
            tadarus: "Tadarus Al-Quran",
            kegiatan: "Kegiatan Harian",
            ceramah: "Ringkasan Ceramah",
        },
        groupTitles: [
            "Amaliyah Cageur, Bageur dan Bener",
            "Amaliyah Pancawaluya Pinter",
            "Amaliyah Pancawaluya Singer",
        ],

        /* ── Form data ── */
        formData: {
            puasa: "",
            puasa_alasan: "",

            sholat: { subuh: "", dzuhur: "", ashar: "", maghrib: "", isya: "" },
            tarawih: "",
            sunat: { rowatib: "", tahajud: "", dhuha: "" },

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
                kajian: false,
                menabung: false,
                tidur_cepat: false,
                bangun_pagi: false,
            },

            ceramah_mode: "",
            ceramah_tema: "",
            ringkasan_ceramah: "",
        },

        /* ── Load dynamic form config from server ── */
        loadFormConfig() {
            var self = this;
            fetch("/api/form-settings/Islam", {
                headers: { Accept: "application/json" },
            })
                .then(function (r) {
                    if (r.status === 403) {
                        return r.json().then(function (d) {
                            self.formDisabled = true;
                            self.formDisabledMessage =
                                d.message || "Formulir sedang dinonaktifkan.";
                            self.configLoaded = true;
                            return null;
                        });
                    }
                    return r.json();
                })
                .then(function (data) {
                    if (!data || !data.sections) return;
                    self.sectionConfig = data.sections;

                    // Build enabled sections map
                    var enabled = {};
                    data.sections.forEach(function (s) {
                        enabled[s.key] = s.enabled !== false;
                    });
                    self.enabledSections = enabled;

                    // Apply config to each section
                    data.sections.forEach(function (section) {
                        // Update section titles
                        if (section.title) {
                            self.sectionTitles[section.key] = section.title;
                        }

                        if (
                            section.key === "puasa" &&
                            section.type === "ya_tidak"
                        ) {
                            if (
                                section.reason_suggestions &&
                                section.reason_suggestions.length > 0
                            ) {
                                self.puasaSuggestions =
                                    section.reason_suggestions;
                            }
                        }

                        if (section.key === "sholat_fardu" && section.items) {
                            self.sholatFarduItems = section.items;
                            if (section.options)
                                self.sholatFarduOptions = section.options;
                            // Rebuild sholat formData
                            var newSholat = {};
                            section.items.forEach(function (item) {
                                newSholat[item.key] =
                                    self.formData.sholat[item.key] || "";
                            });
                            self.formData.sholat = newSholat;
                        }

                        if (section.key === "tarawih" && section.items) {
                            self.tarawihItems = section.items;
                            if (section.options)
                                self.tarawihOptions = section.options;
                        }

                        if (section.key === "sholat_sunat" && section.items) {
                            self.sholatSunatItems = section.items;
                            if (section.options)
                                self.sholatSunatOptions = section.options;
                            // Rebuild sunat formData
                            var newSunat = {};
                            section.items.forEach(function (item) {
                                newSunat[item.key] =
                                    self.formData.sunat[item.key] || "";
                            });
                            self.formData.sunat = newSunat;
                        }

                        if (section.key === "kegiatan" && section.groups) {
                            var allGroups = section.groups;
                            if (allGroups[0]) {
                                self.kegiatanGroupA = allGroups[0].items || [];
                                self.groupTitles[0] =
                                    allGroups[0].title || self.groupTitles[0];
                            }
                            if (allGroups[1]) {
                                self.kegiatanGroupB = allGroups[1].items || [];
                                self.groupTitles[1] =
                                    allGroups[1].title || self.groupTitles[1];
                            }
                            if (allGroups[2]) {
                                self.kegiatanGroupC = allGroups[2].items || [];
                                self.groupTitles[2] =
                                    allGroups[2].title || self.groupTitles[2];
                            }
                            // Rebuild kegiatan formData
                            var newKegiatan = {};
                            allGroups.forEach(function (group) {
                                (group.items || []).forEach(function (item) {
                                    newKegiatan[item.key] =
                                        self.formData.kegiatan[item.key] ||
                                        false;
                                });
                            });
                            self.formData.kegiatan = newKegiatan;
                        }
                    });

                    // Collect extra (dynamic) sections not handled by hardcoded keys
                    var knownKeys = [
                        "puasa",
                        "sholat_fardu",
                        "tarawih",
                        "sholat_sunat",
                        "tadarus",
                        "kegiatan",
                        "ceramah",
                    ];
                    var extras = [];
                    data.sections.forEach(function (section) {
                        if (
                            knownKeys.indexOf(section.key) === -1 &&
                            section.enabled !== false
                        ) {
                            extras.push(section);
                            if (section.type === "ya_tidak") {
                                self.formData[section.key] =
                                    self.formData[section.key] || "";
                                if (section.has_reason) {
                                    self.formData[section.key + "_alasan"] =
                                        self.formData[
                                            section.key + "_alasan"
                                        ] || "";
                                }
                            } else if (
                                section.type === "ya_tidak_list" &&
                                section.items
                            ) {
                                if (
                                    !self.formData[section.key] ||
                                    typeof self.formData[section.key] !==
                                        "object"
                                ) {
                                    self.formData[section.key] = {};
                                }
                                section.items.forEach(function (item) {
                                    self.formData[section.key][item.key] =
                                        self.formData[section.key][item.key] ||
                                        "";
                                });
                            } else if (
                                section.type === "multi_option" &&
                                section.items
                            ) {
                                if (
                                    !self.formData[section.key] ||
                                    typeof self.formData[section.key] !==
                                        "object"
                                ) {
                                    self.formData[section.key] = {};
                                }
                                section.items.forEach(function (item) {
                                    self.formData[section.key][item.key] =
                                        self.formData[section.key][item.key] ||
                                        "";
                                });
                            } else if (
                                (section.type === "checklist_groups" ||
                                    section.type === "ya_tidak_groups") &&
                                section.groups
                            ) {
                                if (
                                    !self.formData[section.key] ||
                                    typeof self.formData[section.key] !==
                                        "object"
                                ) {
                                    self.formData[section.key] = {};
                                }
                                section.groups.forEach(function (group) {
                                    (group.items || []).forEach(
                                        function (item) {
                                            self.formData[section.key][
                                                item.key
                                            ] =
                                                section.type ===
                                                "checklist_groups"
                                                    ? self.formData[
                                                          section.key
                                                      ][item.key] || false
                                                    : self.formData[
                                                          section.key
                                                      ][item.key] || "";
                                        },
                                    );
                                });
                            } else if (section.type === "catatan") {
                                self.formData[section.key] =
                                    self.formData[section.key] || "";
                            }
                        }
                    });
                    self.extraSections = extras;

                    self.configLoaded = true;
                    // Re-check form submitted after config loaded (restores saved data properly)
                    self.checkFormSubmitted();
                })
                .catch(function (e) {
                    console.warn("Gagal memuat konfigurasi formulir:", e);
                    self.configLoaded = true; // Use defaults
                });
        },

        /* Check if a section is enabled by key */
        isSectionEnabled(key) {
            if (Object.keys(this.enabledSections).length === 0) return true; // defaults before config loads
            return this.enabledSections[key] !== false;
        },

        /* ── Lifecycle ── */
        init() {
            this.calculateRamadhanDay();
            this.loadSubmittedDays();
            this.loadFormConfig();
            this.syncFromServer();
            // Always open the oldest unfilled day first (sequential enforcement)
            this.formDay = this.getFirstUnfilledDay();
            this.checkFormSubmitted();
            this.filteredSurahs = this.allSurahs;
        },

        ramadhanDay: 1,

        calculateRamadhanDay() {
            const ramadhanStart = new Date(2026, 1, 19); // 1 Ramadhan 1447H
            const now = new Date();
            const today = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
            );
            const diff = Math.floor((today - ramadhanStart) / 86400000) + 1;
            this.ramadhanDay = Math.max(1, Math.min(diff, 30));
        },

        /* Return the earliest unfilled day (1..ramadhanDay) */
        getFirstUnfilledDay() {
            for (var d = 1; d <= this.ramadhanDay; d++) {
                if (!this.submittedDays.includes(d)) return d;
            }
            return this.ramadhanDay;
        },

        /* Count how many days before today are still unfilled */
        getMissedCount() {
            var count = 0;
            for (var d = 1; d < this.ramadhanDay; d++) {
                if (!this.submittedDays.includes(d)) count++;
            }
            return count;
        },

        /* Reset form fields (used when advancing to the next day) */
        resetFormData() {
            // Build sholat object from dynamic items
            var sholat = {};
            this.sholatFarduItems.forEach(function (item) {
                sholat[item.key] = "";
            });
            // Build sunat object from dynamic items
            var sunat = {};
            this.sholatSunatItems.forEach(function (item) {
                sunat[item.key] = "";
            });
            // Build kegiatan object from dynamic groups
            var kegiatan = {};
            this.kegiatanGroupA.forEach(function (item) {
                kegiatan[item.key] = false;
            });
            this.kegiatanGroupB.forEach(function (item) {
                kegiatan[item.key] = false;
            });
            this.kegiatanGroupC.forEach(function (item) {
                kegiatan[item.key] = false;
            });

            this.formData = {
                puasa: "",
                puasa_alasan: "",
                sholat: sholat,
                tarawih: "",
                sunat: sunat,
                tadarus_surat: "",
                tadarus_ayat: "",
                kegiatan: kegiatan,
                ceramah_mode: "",
                ceramah_tema: "",
                ringkasan_ceramah: "",
            };
            // Reset extra (dynamic) section formData
            var self = this;
            this.extraSections.forEach(function (section) {
                if (section.type === "ya_tidak") {
                    self.formData[section.key] = "";
                    if (section.has_reason)
                        self.formData[section.key + "_alasan"] = "";
                } else if (
                    section.type === "ya_tidak_list" ||
                    section.type === "multi_option"
                ) {
                    var obj = {};
                    (section.items || []).forEach(function (item) {
                        obj[item.key] = "";
                    });
                    self.formData[section.key] = obj;
                } else if (
                    section.type === "checklist_groups" ||
                    section.type === "ya_tidak_groups"
                ) {
                    var obj = {};
                    (section.groups || []).forEach(function (g) {
                        (g.items || []).forEach(function (item) {
                            obj[item.key] =
                                section.type === "checklist_groups"
                                    ? false
                                    : "";
                        });
                    });
                    self.formData[section.key] = obj;
                } else if (section.type === "catatan") {
                    self.formData[section.key] = "";
                }
            });
            this.selectedSurahAyat = 0;
            this.ayatError = "";
            if (this.$refs.ceramahEditor)
                this.$refs.ceramahEditor.innerHTML = "";
        },

        /* ── Surah search / filter ── */
        filterSurah(query) {
            if (!query || query.length === 0) {
                this.filteredSurahs = this.allSurahs;
                return;
            }
            var q = query.toLowerCase();
            this.filteredSurahs = this.allSurahs.filter(function (s) {
                return (
                    s.name.toLowerCase().indexOf(q) !== -1 ||
                    String(s.number).indexOf(q) !== -1
                );
            });
        },

        selectSurah(s) {
            this.formData.tadarus_surat = s.name;
            this.selectedSurahAyat = s.ayat;
            this.showSurahList = false;
            this.ayatError = "";
            // Re-validate existing ayat input against new surah
            if (this.formData.tadarus_ayat) {
                this.validateAyat(this.formData.tadarus_ayat);
            }
        },

        validateAyat(val) {
            // Strip anything that's not a digit or dash
            var clean = val.replace(/[^0-9\-]/g, "");
            // Collapse multiple dashes
            clean = clean.replace(/-{2,}/g, "-");
            // Remove leading dash
            clean = clean.replace(/^-/, "");
            this.formData.tadarus_ayat = clean;

            if (!clean || !this.selectedSurahAyat) {
                this.ayatError = "";
                return;
            }

            var parts = clean.split("-").filter(function (p) {
                return p !== "";
            });
            var max = this.selectedSurahAyat;
            var exceeded = parts.some(function (p) {
                return parseInt(p) > max;
            });
            var reversed =
                parts.length === 2 && parseInt(parts[0]) > parseInt(parts[1]);

            if (exceeded) {
                this.ayatError = "Melebihi jumlah ayat surat ini (" + max + ")";
            } else if (reversed) {
                this.ayatError =
                    "Ayat awal tidak boleh lebih besar dari ayat akhir";
            } else {
                this.ayatError = "";
            }
        },

        /* ── Rich text editor commands ── */
        execCmd(cmd) {
            document.execCommand(cmd, false, null);
            if (this.$refs.ceramahEditor) this.$refs.ceramahEditor.focus();
            this.updateEditorFormats();
        },

        /* ── LocalStorage persistence ── */
        loadSubmittedDays() {
            try {
                var saved = localStorage.getItem("ramadhan_submitted_days");
                this.submittedDays = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.submittedDays = [];
            }
        },

        checkFormSubmitted() {
            this.formSubmitted = this.submittedDays.includes(this.formDay);
            // Set current day verification status
            var dayStatus = this.submissionStatuses[this.formDay];
            this.currentDayStatus = dayStatus ? dayStatus.status : "";
            this.currentDayNote = dayStatus ? dayStatus.catatan_guru || "" : "";
            // If rejected, allow re-edit
            if (this.currentDayStatus === "rejected") {
                this.formSubmitted = false;
            }
            try {
                var savedForm = localStorage.getItem(
                    "ramadhan_form_day_" + this.formDay,
                );
                if (savedForm) {
                    var parsed = JSON.parse(savedForm);
                    this.formData = this._deepMerge(this.formData, parsed);
                    // Restore rich text editor content
                    var self = this;
                    if (self.formData.ringkasan_ceramah) {
                        this.$nextTick(function () {
                            if (self.$refs.ceramahEditor) {
                                self.$refs.ceramahEditor.innerHTML =
                                    self.formData.ringkasan_ceramah;
                            }
                        });
                    }
                    // Restore surah ayat count
                    if (this.formData.tadarus_surat) {
                        var found = this.allSurahs.find(function (s) {
                            return s.name === self.formData.tadarus_surat;
                        });
                        if (found) this.selectedSurahAyat = found.ayat;
                    }
                }
            } catch (e) {}
        },

        _deepMerge(target, source) {
            var result = Object.assign({}, target);
            for (var key in source) {
                if (!source.hasOwnProperty(key)) continue;
                if (
                    source[key] &&
                    typeof source[key] === "object" &&
                    !Array.isArray(source[key]) &&
                    target[key] &&
                    typeof target[key] === "object" &&
                    !Array.isArray(target[key])
                ) {
                    result[key] = this._deepMerge(target[key], source[key]);
                } else if (source[key] !== undefined) {
                    result[key] = source[key];
                }
            }
            return result;
        },

        validateForm() {
            var errors = [];
            // Puasa wajib diisi
            if (!this.formData.puasa) {
                errors.push("Puasa belum diisi");
            }
            // Minimal 1 sholat wajib diisi
            if (this.isSectionEnabled("sholat")) {
                var anySholat = false;
                for (var key in this.formData.sholat) {
                    if (this.formData.sholat[key]) {
                        anySholat = true;
                        break;
                    }
                }
                if (!anySholat) errors.push("Sholat fardhu belum diisi");
            }
            // Validasi ayat tadarus
            if (this.ayatError) {
                errors.push(this.ayatError);
            }
            return errors;
        },

        submitForm() {
            if (this.formDisabled) {
                this.validationMessage = this.formDisabledMessage;
                this.showValidationError = true;
                var self = this;
                setTimeout(function () {
                    self.showValidationError = false;
                }, 4000);
                return;
            }
            // Validate first
            var errors = this.validateForm();
            if (errors.length > 0) {
                this.validationMessage = errors.join(", ");
                this.showValidationError = true;
                var self = this;
                setTimeout(function () {
                    self.showValidationError = false;
                }, 4000);
                return;
            }

            this.formSaving = true;
            // Capture editor content before saving
            if (this.$refs.ceramahEditor) {
                this.formData.ringkasan_ceramah =
                    this.$refs.ceramahEditor.innerHTML;
            }
            localStorage.setItem(
                "ramadhan_form_day_" + this.formDay,
                JSON.stringify(this.formData),
            );
            if (!this.submittedDays.includes(this.formDay)) {
                this.submittedDays.push(this.formDay);
                localStorage.setItem(
                    "ramadhan_submitted_days",
                    JSON.stringify(this.submittedDays),
                );
            }
            this.formSubmitted = true;

            // POST to backend
            var self = this;
            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            fetch("/api/formulir", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken
                        ? csrfToken.getAttribute("content")
                        : "",
                },
                body: JSON.stringify({
                    hari_ke: self.formDay,
                    data: self.formData,
                }),
            })
                .then(function (r) {
                    return r.json();
                })
                .catch(function (e) {
                    console.warn("Formulir gagal disimpan ke server:", e);
                });

            setTimeout(function () {
                self.formSaving = false;
                self.showSuccessPopup = true;
                setTimeout(function () {
                    self.showSuccessPopup = false;
                }, 3000);

                // Auto-advance to the next unfilled day (sequential enforcement)
                var next = self.getFirstUnfilledDay();
                if (next !== self.formDay) {
                    setTimeout(function () {
                        // There is still a day that needs filling
                        self.formDay = next;
                        self.formSubmitted = false;
                        self.resetFormData();
                        self.checkFormSubmitted(); // loads saved data if any
                    }, 2000);
                }
            }, 600);
        },

        editForm() {
            this.formSubmitted = false;
        },

        /* ── Sync submitted days from server ── */
        syncFromServer() {
            var self = this;
            fetch("/api/formulir", {
                headers: { Accept: "application/json" },
            })
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    if (data.success && data.submitted_days) {
                        // Merge server submitted days into local
                        data.submitted_days.forEach(function (day) {
                            if (!self.submittedDays.includes(day)) {
                                self.submittedDays.push(day);
                            }
                        });
                        localStorage.setItem(
                            "ramadhan_submitted_days",
                            JSON.stringify(self.submittedDays),
                        );
                        // Track submission statuses
                        if (data.submissions) {
                            data.submissions.forEach(function (sub) {
                                self.submissionStatuses[sub.hari_ke] = {
                                    status: sub.status || "pending",
                                    catatan_guru: sub.catatan_guru || "",
                                };
                                var key = "ramadhan_form_day_" + sub.hari_ke;
                                if (!localStorage.getItem(key) && sub.data) {
                                    localStorage.setItem(
                                        key,
                                        JSON.stringify(sub.data),
                                    );
                                }
                            });
                        }
                        // Re-evaluate current day
                        self.formDay = self.getFirstUnfilledDay();
                        self.checkFormSubmitted();
                    }
                })
                .catch(function (e) {
                    console.warn("Gagal sync dari server:", e);
                });
        },
    };
}
