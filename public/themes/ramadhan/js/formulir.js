function formulirHarian() {
    return {
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

        init() {
            this.calculateRamadhanDay();
            this.formDay = this.ramadhanDay;
            this.loadSubmittedDays();
            this.checkFormSubmitted();
        },

        ramadhanDay: 1,

        calculateRamadhanDay() {
            // 1 Ramadhan 1447H = 19 Februari 2026
            const ramadhanStart = new Date(2026, 1, 19); // Feb 19, 2026
            const now = new Date();
            const today = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
            );
            const diff = Math.floor((today - ramadhanStart) / 86400000) + 1;
            this.ramadhanDay = Math.max(1, Math.min(diff, 30));
        },

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
            try {
                const savedForm = localStorage.getItem(
                    "ramadhan_form_day_" + this.formDay,
                );
                if (savedForm) {
                    this.formData = JSON.parse(savedForm);
                }
            } catch (e) {}
        },

        submitForm() {
            this.formSaving = true;
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
            setTimeout(() => {
                this.formSaving = false;
            }, 500);
        },

        editForm() {
            this.formSubmitted = false;
        },
    };
}
