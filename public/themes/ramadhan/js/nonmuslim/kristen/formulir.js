/**
 * Buku Ramadhan — Formulir Harian Non-Muslim Alpine.js Component
 * Kegiatan Pembiasaan Positif & Pengendalian Diri
 */

function formulirNonMuslim() {
    return {
        formDay: 1,
        formSubmitted: false,
        formSaving: false,
        submittedDays: [],
        currentDay: 1,
        isSunday: false,

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

        /* ── Pembiasaan Pengendalian Diri ── */
        pengendalianDiri: [
            {
                key: "pengendalian_diri",
                label: "Latihan pengendalian diri (mengurangi jajan / screen time)",
            },
            { key: "refleksi_doa", label: "Refleksi / doa sesuai keyakinan" },
            {
                key: "baca_inspiratif",
                label: "Membaca buku inspiratif / nilai moral",
            },
        ],

        /* ── Kegiatan Harian groups (matches paper form) ── */
        kegiatanGroupA: [
            {
                key: "refleksi_pagi",
                label: "Refleksi pagi (menulis rasa syukur)",
            },
            { key: "olahraga", label: "Olahraga ringan" },
            { key: "membantu_ortu", label: "Membantu orang tua" },
            { key: "membersihkan_kamar", label: "Membersihkan kamar" },
            {
                key: "membersihkan_rumah",
                label: "Membersihkan rumah / halaman",
            },
            {
                key: "merawat_lingkungan",
                label: "Merawat lingkungan / tanaman",
            },
            { key: "refleksi_sore", label: "Refleksi sore" },
            { key: "sedekah", label: "Aksi berbagi / sedekah" },
            {
                key: "makan_keluarga",
                label: "Makan bersama keluarga tanpa gadget",
            },
        ],
        kegiatanGroupB: [
            {
                key: "literasi",
                label: "Literasi (membaca buku pengembangan diri / biografi)",
            },
            {
                key: "menulis_ringkasan",
                label: "Menulis ringkasan / refleksi bacaan",
            },
        ],
        kegiatanGroupC: [
            { key: "menabung", label: "Menabung" },
            { key: "tidur_lebih_awal", label: "Tidur lebih awal" },
            { key: "bangun_pagi", label: "Bangun pagi" },
            {
                key: "target_kebaikan",
                label: "Menetapkan target kebaikan harian",
            },
        ],

        /* ── Form data ── */
        formData: {
            pengendalian: {
                pengendalian_diri: "",
                refleksi_doa: "",
                baca_inspiratif: "",
            },

            kegiatan: {
                refleksi_pagi: "",
                olahraga: "",
                membantu_ortu: "",
                membersihkan_kamar: "",
                membersihkan_rumah: "",
                merawat_lingkungan: "",
                refleksi_sore: "",
                sedekah: "",
                makan_keluarga: "",
                literasi: "",
                menulis_ringkasan: "",
                menabung: "",
                tidur_lebih_awal: "",
                bangun_pagi: "",
                target_kebaikan: "",
            },

            catatan: "",
        },

        /* ── Lifecycle ── */
        init() {
            this.calculateCurrentDay();
            this.checkSunday();
            this.loadSubmittedDays();
            this.syncFromServer();
            this.formDay = this.getFirstUnfilledDay();
            this.checkFormSubmitted();
        },

        calculateCurrentDay() {
            var startDate = new Date(2026, 1, 19); // Same start as Ramadhan
            var now = new Date();
            var today = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
            );
            var diff = Math.floor((today - startDate) / 86400000) + 1;
            this.currentDay = Math.max(1, Math.min(diff, 30));
        },

        checkSunday() {
            var now = new Date();
            this.isSunday = now.getDay() === 0; // 0 = Sunday
        },

        /* Return the earliest unfilled day (1..currentDay) */
        getFirstUnfilledDay() {
            for (var d = 1; d <= this.currentDay; d++) {
                if (!this.submittedDays.includes(d)) return d;
            }
            return this.currentDay;
        },

        /* Count how many days before today are still unfilled */
        getMissedCount() {
            var count = 0;
            for (var d = 1; d < this.currentDay; d++) {
                if (!this.submittedDays.includes(d)) count++;
            }
            return count;
        },

        /* Reset form fields */
        resetFormData() {
            this.formData = {
                pengendalian: {
                    pengendalian_diri: "",
                    refleksi_doa: "",
                    baca_inspiratif: "",
                },
                kegiatan: {
                    refleksi_pagi: "",
                    olahraga: "",
                    membantu_ortu: "",
                    membersihkan_kamar: "",
                    membersihkan_rumah: "",
                    merawat_lingkungan: "",
                    refleksi_sore: "",
                    sedekah: "",
                    makan_keluarga: "",
                    literasi: "",
                    menulis_ringkasan: "",
                    menabung: "",
                    tidur_lebih_awal: "",
                    bangun_pagi: "",
                    target_kebaikan: "",
                },
                catatan: "",
            };
            if (this.$refs.catatanEditor)
                this.$refs.catatanEditor.innerHTML = "";
        },

        /* ── Rich text editor commands ── */
        execCmd(cmd) {
            document.execCommand(cmd, false, null);
            if (this.$refs.catatanEditor) this.$refs.catatanEditor.focus();
            this.updateEditorFormats();
        },

        /* ── LocalStorage persistence ── */
        loadSubmittedDays() {
            try {
                var saved = localStorage.getItem("nonmuslim_submitted_days");
                this.submittedDays = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.submittedDays = [];
            }
        },

        checkFormSubmitted() {
            this.formSubmitted = this.submittedDays.includes(this.formDay);
            try {
                var savedForm = localStorage.getItem(
                    "nonmuslim_form_day_" + this.formDay,
                );
                if (savedForm) {
                    var parsed = JSON.parse(savedForm);
                    this.formData = this._deepMerge(this.formData, parsed);
                    var self = this;
                    if (self.formData.catatan) {
                        this.$nextTick(function () {
                            if (self.$refs.catatanEditor) {
                                self.$refs.catatanEditor.innerHTML =
                                    self.formData.catatan;
                            }
                        });
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

        submitForm() {
            this.formSaving = true;
            if (this.$refs.catatanEditor) {
                this.formData.catatan = this.$refs.catatanEditor.innerHTML;
            }
            localStorage.setItem(
                "nonmuslim_form_day_" + this.formDay,
                JSON.stringify(this.formData),
            );
            if (!this.submittedDays.includes(this.formDay)) {
                this.submittedDays.push(this.formDay);
                localStorage.setItem(
                    "nonmuslim_submitted_days",
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
                var next = self.getFirstUnfilledDay();
                if (next !== self.formDay) {
                    self.formDay = next;
                    self.formSubmitted = false;
                    self.resetFormData();
                    self.checkFormSubmitted();
                }
            }, 600);
        },

        editForm() {
            this.formSubmitted = false;
        },

        /* ── Sync submitted days from server ── */
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
                            "nonmuslim_submitted_days",
                            JSON.stringify(self.submittedDays),
                        );
                        if (data.submissions) {
                            data.submissions.forEach(function (sub) {
                                var key = "nonmuslim_form_day_" + sub.hari_ke;
                                if (!localStorage.getItem(key) && sub.data) {
                                    localStorage.setItem(
                                        key,
                                        JSON.stringify(sub.data),
                                    );
                                }
                            });
                        }
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
