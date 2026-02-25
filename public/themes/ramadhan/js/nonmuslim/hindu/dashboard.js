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

function hinduDashboard() {
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
        calendarDays: [],
        allDuas: [],
        filteredDuas: [],
        doaSearch: "",
        activeDoaCategory: "semua",
        expandedDoas: [],
        doasLoading: false,
        doaCategories: [],
        paginatedDuas: [],
        doaPage: 1,
        doaPerPage: 10,
        doaTotalPages: 1,
        doaPageNumbers: [],
        dailyVerse: {},
        clockMain: "--:--:--",
        clockWIB: "--:--",
        clockWITA: "--:--",
        clockWIT: "--:--",
        greeting: "",
        selectedTz: "WIB",
        gregorianDate: "",
        ramadhanDay: 1,
        calendarMonthLabel: "",
        isHolyDay: false,
        motivationalBadge: "",
        submittedDays: [],
        submissionStatuses: {},
        showNotifModal: false,
        notifTitle: "",
        notifMessage: "",
        notifRedirectUrl: "",
        init() {
            this.setDates();
            this.calculateRamadhanDay();
            this.checkHolyDay();
            this.loadSubmittedDays();
            this.buildCalendar();
            this.loadDoas();
            this.setDailyVerse();
            this.startClock();
            this.setMotivationalBadge();
        },
        startClock() {
            var self = this;
            function tick() {
                var now = new Date();
                var utc = now.getTime() + now.getTimezoneOffset() * 60000;
                var wib = new Date(utc + 7 * 3600000);
                var wita = new Date(utc + 8 * 3600000);
                var wit = new Date(utc + 9 * 3600000);
                var fmt2 = function (n) {
                    return n < 10 ? "0" + n : "" + n;
                };
                var fmtTime = function (d) {
                    return fmt2(d.getHours()) + ":" + fmt2(d.getMinutes());
                };
                var fmtFull = function (d) {
                    return (
                        fmt2(d.getHours()) +
                        ":" +
                        fmt2(d.getMinutes()) +
                        ":" +
                        fmt2(d.getSeconds())
                    );
                };
                if (self.selectedTz === "WIB") self.clockMain = fmtFull(wib);
                else if (self.selectedTz === "WITA")
                    self.clockMain = fmtFull(wita);
                else self.clockMain = fmtFull(wit);
                self.clockWIB = fmtTime(wib);
                self.clockWITA = fmtTime(wita);
                self.clockWIT = fmtTime(wit);
                var h = wib.getHours();
                if (h >= 3 && h < 11) self.greeting = "Selamat Pagi ☀️";
                else if (h >= 11 && h < 15) self.greeting = "Selamat Siang 🌤️";
                else if (h >= 15 && h < 18) self.greeting = "Selamat Sore 🌅";
                else self.greeting = "Selamat Malam 🌙";
            }
            tick();
            setInterval(tick, 1000);
        },
        setDates() {
            var now = new Date();
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
            this.gregorianDate =
                days[now.getDay()] +
                ", " +
                now.getDate() +
                " " +
                months[now.getMonth()] +
                " " +
                now.getFullYear();
        },
        calculateRamadhanDay() {
            var startDate = new Date(2026, 1, 19);
            var now = new Date();
            var today = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
            );
            var diff = Math.floor((today - startDate) / 86400000) + 1;
            this.ramadhanDay = Math.max(1, Math.min(diff, 30));
        },
        checkHolyDay() {
            this.isHolyDay = false;
        },
        setMotivationalBadge() {
            var badges = [
                "Semangat berkegiatan positif!",
                "Setiap hari adalah kesempatan baru",
                "Dharma adalah jalan kebenaran",
                "Teruslah bertumbuh dalam kebajikan",
                "Lakukanlah kebaikan tanpa pamrih",
                "Tat Tvam Asi — Aku adalah Engkau",
                "Ahimsa — Cintai semua makhluk",
            ];
            this.motivationalBadge = badges[this.ramadhanDay % badges.length];
        },
        buildCalendar() {
            var startDate = new Date(2026, 1, 19);
            var endDate = new Date(2026, 2, 20);
            var startDow = startDate.getDay();
            var mondayOffset = startDow === 0 ? -6 : 1 - startDow;
            var calStart = new Date(startDate);
            calStart.setDate(calStart.getDate() + mondayOffset);
            var today = new Date();
            today = new Date(
                today.getFullYear(),
                today.getMonth(),
                today.getDate(),
            );
            var days = [];
            var d = new Date(calStart);
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
            this.calendarMonthLabel =
                months[startDate.getMonth()] +
                " — " +
                months[endDate.getMonth()] +
                " " +
                endDate.getFullYear();
            for (var i = 0; i < 42; i++) {
                var cur = new Date(d);
                var hijriDay = Math.floor((cur - startDate) / 86400000) + 1;
                var inRange = hijriDay >= 1 && hijriDay <= 30;
                var isToday = cur.getTime() === today.getTime() && inRange;
                var isPast = cur < today && inRange;
                var isCompleted =
                    inRange && this.submittedDays.includes(hijriDay);
                var dayStatus = this.submissionStatuses[hijriDay];
                var statusStr = dayStatus ? dayStatus.status : "";
                var kesiswaanStr = dayStatus
                    ? dayStatus.kesiswaan_status || ""
                    : "";
                var isValidated =
                    isCompleted &&
                    statusStr === "verified" &&
                    kesiswaanStr === "validated";
                var isRejected =
                    isCompleted &&
                    (statusStr === "rejected" || kesiswaanStr === "rejected");
                var isVerified =
                    isCompleted &&
                    statusStr === "verified" &&
                    !isValidated &&
                    !isRejected;
                var isPending =
                    isCompleted &&
                    (statusStr === "pending" || statusStr === "");
                var isPastUnfilled =
                    isPast && !isCompleted && !isToday && inRange;
                days.push({
                    key: "d" + i,
                    masehiDay: cur.getDate(),
                    hijriDay: inRange ? hijriDay : 0,
                    isToday: isToday,
                    isPast: isPast,
                    isCompleted: isCompleted,
                    isValidated: isValidated,
                    isVerified: isVerified,
                    isPending: isPending,
                    isRejected: isRejected,
                    isPastUnfilled: isPastUnfilled,
                });
                d.setDate(d.getDate() + 1);
                if (i > 27 && hijriDay >= 30 && d.getDay() === 1) break;
            }
            this.calendarDays = days;
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
                var lastUser = localStorage.getItem("hindu_last_user");
                var currentUser = window.__siswaUserId || "unknown";
                if (lastUser && lastUser !== currentUser) {
                    this._clearOldUserData("hindu_submitted_days_");
                    this._clearOldUserData("hindu_form_day_");
                }
                localStorage.setItem("hindu_last_user", currentUser);
                var saved = localStorage.getItem(
                    this._lsKey("hindu_submitted_days"),
                );
                this.submittedDays = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.submittedDays = [];
            }
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
                            self._lsKey("hindu_submitted_days"),
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
                var s = this.submissionStatuses[key];
                if (
                    s.status === "verified" &&
                    s.kesiswaan_status === "validated"
                )
                    count++;
            }
            return count;
        },
        getVerifiedCount() {
            var count = 0;
            for (var key in this.submissionStatuses) {
                var s = this.submissionStatuses[key];
                if (
                    s.status === "verified" &&
                    s.kesiswaan_status !== "validated"
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
        getPendingPercent() {
            return Math.round((this.getPendingCount() / 30) * 100);
        },
        getRejectedPercent() {
            return Math.round((this.getRejectedCount() / 30) * 100);
        },
        getValidatedPercent() {
            return Math.round((this.getValidatedCount() / 30) * 100);
        },
        loadDoas() {
            this.allDuas = [
                {
                    id: 1,
                    title: "Doa Pagi (Prātaḥ Prārthanā)",
                    category: "harian",
                    source: "Doa Harian Hindu",
                    text: "Om Sang Hyang Widhi Wasa, pada pagi hari ini hamba memohon berkat dan perlindungan-Mu. Terangilah hari ini dengan cahaya kebijaksanaan-Mu. Tuntunlah langkah hamba di jalan Dharma yang benar. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Kewajiban yang dilakukan tanpa pamrih, tanpa kelekatan, tanpa rasa suka atau benci, disebut kewajiban dalam sifat kebaikan." — Bhagavad Gita 18:23',
                },
                {
                    id: 2,
                    title: "Doa Sebelum Tidur (Sayam Prārthanā)",
                    category: "harian",
                    source: "Doa Harian Hindu",
                    text: "Om Sang Hyang Widhi Wasa, terima kasih atas segala berkat yang hamba terima hari ini. Ampunilah segala kesalahan yang hamba lakukan sengaja maupun tidak sengaja. Lindungilah hamba sepanjang malam ini agar hamba dapat beristirahat dengan damai. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Serahkanlah segala perbuatanmu kepada-Ku, pusatkan pikiranmu kepada Atman, bebaskan diri dari keinginan dan keakuan, dan bertarunglah tanpa rasa duka." — Bhagavad Gita 3:30',
                },
                {
                    id: 3,
                    title: "Doa Sebelum Makan (Bhojana Mantra)",
                    category: "harian",
                    source: "Bhagavad Gita 4:24",
                    text: "Om Brahmarpanam Brahma Havir, Brahmagnau Brahmana Hutam. Brahmaiva Tena Gantavyam, Brahma Karma Samadhina. Om.\n\nArtinya: Persembahan adalah Brahman, makanan persembahan adalah Brahman, dipersembahkan oleh Brahman ke dalam api Brahman. Brahman akan dicapai oleh mereka yang melihat Brahman dalam segala tindakan.",
                    verse: '"Brahman adalah persembahan, Brahman adalah sesaji, oleh Brahman dipersembahkan ke dalam api Brahman." — Bhagavad Gita 4:24',
                },
                {
                    id: 4,
                    title: "Doa Sesudah Makan",
                    category: "harian",
                    source: "Doa Harian Hindu",
                    text: "Om Sang Hyang Widhi Wasa, terima kasih atas rejeki makanan yang telah hamba nikmati. Semoga makanan ini menjadi kekuatan bagi hamba untuk melakukan kebajikan dan menjalankan Dharma. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Makhluk hidup ada karena makanan, makanan ada karena hujan, hujan ada karena yajna (persembahan)." — Bhagavad Gita 3:14',
                },
                {
                    id: 5,
                    title: "Doa Ucapan Syukur (Dhanyavāda Prārthanā)",
                    category: "syukur",
                    source: "Doa Hindu",
                    text: "Om Sang Hyang Widhi Wasa, hamba mengucapkan puji syukur yang tak terhingga atas segala karunia-Mu. Terima kasih atas kehidupan, kesehatan, keluarga, dan segala kebaikan yang Engkau limpahkan. Ajarlah hamba untuk selalu bersyukur dan berbagi kebaikan. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Siapa pun yang mempersembahkan kepada-Ku dengan penuh bakti sehelai daun, sekuntum bunga, sebuah buah, atau seteguk air, Aku menerimanya." — Bhagavad Gita 9:26',
                },
                {
                    id: 6,
                    title: "Doa Syukur atas Alam Semesta",
                    category: "syukur",
                    source: "Rg Veda",
                    text: "Om Sang Hyang Widhi Wasa, terima kasih atas keindahan alam semesta ciptaan-Mu. Matahari yang menyinari, air yang menyejukkan, bumi yang menopang, dan udara yang menyegarkan. Semua adalah karunia-Mu yang tak ternilai. Ajarlah hamba untuk menjaga dan merawat alam ini. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Aku adalah asal mula dan akhir dari seluruh alam semesta ini." — Bhagavad Gita 7:6',
                },
                {
                    id: 7,
                    title: "Doa Syukur atas Keluarga",
                    category: "syukur",
                    source: "Doa Hindu",
                    text: "Om Sang Hyang Widhi Wasa, terima kasih untuk keluarga yang Engkau anugerahkan. Berkatilah ayah, ibu, saudara, dan seluruh keluarga hamba. Jadikanlah keluarga hamba tempat di mana Dharma dan kasih sayang-Mu tumbuh subur. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Dari Dharma lahir kebahagiaan, dan dari Dharma lahir segala yang baik." — Bhagavad Gita 14:16',
                },
                {
                    id: 8,
                    title: "Tri Sandhya",
                    category: "puja",
                    source: "Puja Tri Sandhya",
                    text: "Om Bhur Bwah Swah\nTat Savitur Varenyam\nBhargo Devasya Dhimahi\nDhiyo Yo Nah Pracodayat. Om.\n\nArtinya: Ya Sang Hyang Widhi Wasa yang Mahasuci, yang meresapi bumi, angkasa, dan surga. Hamba memusatkan pikiran pada cahaya kemulian-Mu yang Mahaagung. Semoga Engkau berkenan menerangi pikiran dan budi pekerti hamba.",
                    verse: '"Aku adalah Gayatri di antara mantra-mantra." — Bhagavad Gita 10:35',
                },
                {
                    id: 9,
                    title: "Puja Tri Sandhya Lengkap",
                    category: "puja",
                    source: "Puja Hindu Dharma",
                    text: "Om Bhur Bwah Swah, Tat Savitur Varenyam, Bhargo Devasya Dhimahi, Dhiyo Yo Nah Pracodayat.\n\nOm Narayana Evedam Sarvam, Yad Bhutam Yac Ca Bhavyam, Niskalanko Niranjano, Nirvikalpo Nirakyatah, Suddho Deva Eko, Narayano Na Dvitiyo Sti Kascit.\n\nOm Tvam Sivah Tvam Mahadevah, Isvarah Paramesvarah, Brahma Visnu Ca Rudras Ca, Purusah Parikirtitah.\n\nOm Papo 'Ham Papakarmaham, Papatma Papasambhavah, Trahi Mam Pundarikaksa, Sabahyabhyantarah Sucih.\n\nOm Ksamasva Mam Mahadeva, Sarvaprani Hitankara, Mam Moca Sarva Papebhyah, Palayasva Sada Siva.\n\nOm Ksantavyah Kayiko Dosah, Ksantavyo Vaciko Mama, Ksantavyo Manaso Dosah, Tat Pramadat Ksamasva Mam.\n\nOm Shanti, Shanti, Shanti. Om.",
                    verse: '"Dengan pikiran yang tenang dan tertuju kepada-Ku, praktikkanlah yoga untuk mencapai kedamaian tertinggi." — Bhagavad Gita 6:15',
                },
                {
                    id: 10,
                    title: "Doa Puja (Sembahyang)",
                    category: "puja",
                    source: "Puja Hindu Dharma",
                    text: "Om Sang Hyang Widhi Wasa, hamba datang dengan hati yang tulus untuk memuja keagungan-Mu. Terimalah persembahan dan bakti hamba yang sederhana ini. Tuntunlah hamba selalu di jalan Dharma. Om Dewa Suksma Paramacintya Ya Namah Svaha. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Orang yang mempersembahkan segala perbuatannya kepada-Ku dan menjadikan-Ku tujuan tertinggi, memuja-Ku dengan meditasi yang tidak terbagi." — Bhagavad Gita 12:6',
                },
                {
                    id: 11,
                    title: "Gayatri Mantra",
                    category: "mantra",
                    source: "Rg Veda 3.62.10",
                    text: "Om Bhur Bwah Swah\nTat Savitur Varenyam\nBhargo Devasya Dhimahi\nDhiyo Yo Nah Pracodayat. Om.\n\nArtinya: Ya Tuhan Yang Maha Esa, Engkau yang meresapi bumi, angkasa, dan surga. Hamba memusatkan pikiran pada cahaya kemulian-Mu yang Mahaagung. Semoga Engkau menerangi pikiran hamba.",
                    verse: '"Di antara mantra-mantra, Aku adalah Gayatri Mantra." — Bhagavad Gita 10:35',
                },
                {
                    id: 12,
                    title: "Mantra Om (Pranava Mantra)",
                    category: "mantra",
                    source: "Mandukya Upanishad",
                    text: "Om.\n\nOm adalah suara primordial alam semesta. Om melambangkan Brahman, realitas tertinggi. Mengucapkan Om dengan penuh kesadaran membantu menenangkan pikiran dan menghubungkan diri dengan Yang Maha Esa.\n\nOm Tat Sat.",
                    verse: '"Di antara huruf-huruf, Aku adalah huruf A, dan di antara kata majemuk, Aku adalah Dvandva." — Bhagavad Gita 10:33',
                },
                {
                    id: 13,
                    title: "Mantra Mrityunjaya (Penolak Bahaya)",
                    category: "mantra",
                    source: "Rg Veda 7.59.12",
                    text: "Om Tryambakam Yajamahe\nSugandhim Pushti Vardhanam\nUrvarukam Iva Bandhanan\nMrityor Mukshiya Maamritat. Om.\n\nArtinya: Kami memuja Yang Bermata Tiga (Tuhan), yang harum dan memelihara semua makhluk. Semoga Ia membebaskan kami dari kematian demi keabadian, sebagaimana buah mentimun dipisahkan dari tangkainya.",
                    verse: '"Bagi jiwa tidak ada kelahiran ataupun kematian. Ia tidak pernah berhenti ada." — Bhagavad Gita 2:20',
                },
                {
                    id: 14,
                    title: "Mantra Asato Ma Sadgamaya",
                    category: "mantra",
                    source: "Brihadaranyaka Upanishad 1.3.28",
                    text: "Om Asato Ma Sadgamaya\nTamaso Ma Jyotir Gamaya\nMrityor Ma Amritam Gamaya.\nOm Shanti, Shanti, Shanti. Om.\n\nArtinya: Tuntunlah hamba dari ketidaknyataan menuju kenyataan. Tuntunlah hamba dari kegelapan menuju cahaya. Tuntunlah hamba dari kematian menuju keabadian. Om Damai, Damai, Damai.",
                    verse: '"Aku adalah cahaya matahari dan bulan, Aku adalah suku kata Om dalam mantra-mantra Veda." — Bhagavad Gita 7:8',
                },
                {
                    id: 15,
                    title: "Doa Sebelum Belajar (Vidyārambha)",
                    category: "belajar",
                    source: "Saraswati Vandana",
                    text: "Om Saraswati Namastubhyam, Varade Kama Rupini. Vidyarambham Karishyami, Siddhir Bhavatu Me Sada. Om.\n\nArtinya: Ya Dewi Saraswati, hamba bersujud kepada-Mu, pemberi anugerah dan pemenuh keinginan. Hamba akan memulai belajar, semoga hamba selalu berhasil. Om.",
                    verse: '"Tidak ada yang menyamai pengetahuan dalam hal kesucian di dunia ini." — Bhagavad Gita 4:38',
                },
                {
                    id: 16,
                    title: "Doa Sesudah Belajar",
                    category: "belajar",
                    source: "Doa Hindu",
                    text: "Om Sang Hyang Widhi Wasa, terima kasih atas ilmu yang telah hamba pelajari hari ini. Tolonglah hamba mengingat dan memahami apa yang telah dipelajari. Semoga pengetahuan ini berguna bagi hamba dan sesama. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Pengetahuan adalah api yang membakar semua karma menjadi abu." — Bhagavad Gita 4:37',
                },
                {
                    id: 17,
                    title: "Doa Menghadapi Ujian",
                    category: "belajar",
                    source: "Doa Hindu",
                    text: "Om Sang Hyang Widhi Wasa, hamba akan menghadapi ujian. Berilah hamba ketenangan pikiran, konsentrasi, dan kebijaksanaan untuk menjawab setiap pertanyaan. Semoga Dewi Saraswati memberikan terang pada pikiran hamba. Om Aim Saraswatyai Namah. Om.",
                    verse: '"Yogasthah Kuru Karmani — Bertindaklah dalam ketetapan yoga, lepaskan kelekatan." — Bhagavad Gita 2:48',
                },
                {
                    id: 18,
                    title: "Doa untuk Orang Tua",
                    category: "keluarga",
                    source: "Taittiriya Upanishad 1.11",
                    text: "Om Sang Hyang Widhi Wasa, berkatilah ayah dan ibu hamba. Berilah mereka kesehatan, umur panjang, dan kedamaian. Sebagaimana kitab suci mengajarkan: Matru Devo Bhava, Pitru Devo Bhava — Ibu adalah Dewa, Ayah adalah Dewa. Hamba berterima kasih atas kasih sayang dan pengorbanan mereka. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Hormati ibumu sebagai Tuhan, hormati ayahmu sebagai Tuhan." — Taittiriya Upanishad 1:11',
                },
                {
                    id: 19,
                    title: "Doa untuk Guru (Guru Vandana)",
                    category: "keluarga",
                    source: "Guru Stotram",
                    text: "Om Gurur Brahma, Gurur Vishnu, Gurur Devo Maheshwara. Gurur Sakshat Para Brahma, Tasmai Shri Gurave Namah. Om.\n\nArtinya: Guru adalah Brahma (pencipta), Guru adalah Vishnu (pemelihara), Guru adalah Maheshwara (pemusnah ketidaktahuan). Guru adalah Brahman (Yang Maha Esa) sendiri. Hormat hamba kepada Guru yang mulia. Om.",
                    verse: '"Pelajarilah kebenaran dengan bersujud, bertanya, dan melayani orang bijak." — Bhagavad Gita 4:34',
                },
                {
                    id: 20,
                    title: "Doa untuk Teman & Sahabat",
                    category: "keluarga",
                    source: "Doa Hindu",
                    text: "Om Sang Hyang Widhi Wasa, terima kasih untuk teman-teman yang Engkau berikan. Berkatilah mereka semua dengan kesehatan dan kebahagiaan. Ajarlah kami saling mengasihi dan saling membantu dalam jalan Dharma. Semoga persahabatan kami menjadi sumber kebaikan. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Sang Atman adalah sahabat bagi mereka yang telah menguasai diri, dan musuh bagi mereka yang tidak." — Bhagavad Gita 6:6',
                },
                {
                    id: 21,
                    title: "Doa untuk Guru & Sekolah",
                    category: "keluarga",
                    source: "Doa Hindu",
                    text: "Om Sang Hyang Widhi Wasa, berkatilah guru-guru kami yang dengan sabar mendidik dan mengajar kami. Sebagaimana Acarya Devo Bhava — Guru adalah Dewa. Berilah mereka kesehatan dan semangat. Berkatilah sekolah kami agar menjadi tempat yang baik untuk belajar dan bertumbuh. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Acarya Devo Bhava — Hormati gurumu sebagai Tuhan." — Taittiriya Upanishad 1:11',
                },
                {
                    id: 22,
                    title: "Doa Sebelum Meditasi (Dhyāna)",
                    category: "meditasi",
                    source: "Bhagavad Gita 6:10-15",
                    text: "Om Sang Hyang Widhi Wasa, hamba duduk dalam ketenangan untuk memusatkan pikiran kepada-Mu. Tenangkanlah gelombang pikiran hamba. Bukakanlah mata batin hamba agar dapat merasakan kehadiran-Mu. Ajarlah hamba untuk melepaskan segala kelekatan dan menuju ketenangan sejati. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Dengan pikiran yang terkendali dan bebas dari keinginan serta kelekatan, hendaknya ia duduk bermeditasi." — Bhagavad Gita 6:10',
                },
                {
                    id: 23,
                    title: "Mantra Meditasi Kedamaian",
                    category: "meditasi",
                    source: "Isha Upanishad",
                    text: "Om Purnamadah Purnamidam, Purnat Purnamudacyate. Purnasya Purnamadaya, Purnamevavasisyate. Om Shanti, Shanti, Shanti. Om.\n\nArtinya: Yang Itu sempurna, Yang Ini sempurna. Dari Yang Sempurna lahir Yang Sempurna. Jika Yang Sempurna diambil dari Yang Sempurna, yang tersisa tetap Sempurna. Om Damai, Damai, Damai.",
                    verse: '"Orang yang damai tenang dalam panas dan dingin, suka dan duka, serta terhormati dan terhina." — Bhagavad Gita 6:7',
                },
                {
                    id: 24,
                    title: "Doa Penutup Meditasi",
                    category: "meditasi",
                    source: "Doa Hindu",
                    text: "Om Sang Hyang Widhi Wasa, terima kasih atas saat-saat ketenangan yang telah hamba rasakan. Biarlah kedamaian ini hamba bawa dalam setiap langkah dan perbuatan hamba. Semoga semua makhluk hidup merasakan kedamaian. Om Sarve Bhavantu Sukhinah, Sarve Santu Niramayah. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Semoga semua makhluk berbahagia, semoga semua bebas dari penyakit." — Shanti Mantra',
                },
                {
                    id: 25,
                    title: "Doa untuk Kedamaian Dunia (Loka Samastha)",
                    category: "umum",
                    source: "Shanti Mantra",
                    text: "Om Sarve Bhavantu Sukhinah, Sarve Santu Niramayah. Sarve Bhadrani Pasyantu, Ma Kaschid Duhkha Bhag Bhavet. Om Shanti, Shanti, Shanti. Om.\n\nArtinya: Semoga semua makhluk berbahagia. Semoga semua bebas dari penyakit. Semoga semua melihat kebaikan. Semoga tak seorang pun menderita. Om Damai, Damai, Damai.",
                    verse: '"Orang bijak melihat dengan pandangan yang sama seorang brahmana, seekor sapi, seekor gajah, seekor anjing, dan seorang pemakan anjing." — Bhagavad Gita 5:18',
                },
                {
                    id: 26,
                    title: "Doa untuk Bangsa & Negara",
                    category: "umum",
                    source: "Doa Hindu",
                    text: "Om Sang Hyang Widhi Wasa, kami mendoakan bangsa dan negara Indonesia. Berkatilah para pemimpin kami agar memiliki kebijaksanaan dan keadilan. Jagalah persatuan dalam keberagaman negeri kami. Semoga Indonesia menjadi bangsa yang Dharma dan sejahtera. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Kapan pun Dharma memudar dan Adharma merajalela, saat itulah Aku mewujudkan diri." — Bhagavad Gita 4:7',
                },
                {
                    id: 27,
                    title: "Doa Kasih untuk Sesama (Metta)",
                    category: "umum",
                    source: "Doa Hindu",
                    text: "Om Sang Hyang Widhi Wasa, ajarlah hamba mengasihi sesama sebagaimana Tat Tvam Asi — Aku adalah Engkau. Biarlah hamba melihat-Mu dalam diri setiap makhluk. Biarlah hamba selalu berbuat kebaikan tanpa mengharap imbalan. Vasudhaiva Kutumbakam — Seluruh dunia adalah keluarga. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Orang yang melihat-Ku di mana-mana dan melihat segala sesuatu di dalam-Ku, Aku tidak pernah hilang darinya dan dia tidak pernah hilang dari-Ku." — Bhagavad Gita 6:30',
                },
                {
                    id: 28,
                    title: "Doa Penyerahan Diri (Saranagati)",
                    category: "umum",
                    source: "Bhagavad Gita 18:66",
                    text: "Om Sang Hyang Widhi Wasa, hamba menyerahkan diri sepenuhnya kepada-Mu. Tinggalkanlah segala kewajiban yang lain dan serahkanlah diri kepada-Mu semata. Engkau akan membebaskan hamba dari segala dosa. Janganlah bersedih. Om Sarva Dharman Parityajya Mam Ekam Saranam Vraja. Om Shanti, Shanti, Shanti. Om.",
                    verse: '"Tinggalkan semua bentuk Dharma dan menyerahlah kepada-Ku semata. Aku akan membebaskanmu dari segala dosa; jangan bersedih." — Bhagavad Gita 18:66',
                },
            ];
            var catMap = {};
            this.allDuas.forEach(function (d) {
                catMap[d.category] = (catMap[d.category] || 0) + 1;
            });
            this.doaCategories = [
                { id: "semua", label: "Semua", count: this.allDuas.length },
                { id: "harian", label: "Harian", count: catMap["harian"] || 0 },
                { id: "syukur", label: "Syukur", count: catMap["syukur"] || 0 },
                { id: "puja", label: "Puja", count: catMap["puja"] || 0 },
                { id: "mantra", label: "Mantra", count: catMap["mantra"] || 0 },
                {
                    id: "belajar",
                    label: "Belajar",
                    count: catMap["belajar"] || 0,
                },
                {
                    id: "keluarga",
                    label: "Keluarga",
                    count: catMap["keluarga"] || 0,
                },
                {
                    id: "meditasi",
                    label: "Meditasi",
                    count: catMap["meditasi"] || 0,
                },
                { id: "umum", label: "Umum", count: catMap["umum"] || 0 },
            ];
            this.filteredDuas = this.allDuas.slice();
            this.paginateDuas();
        },
        filterDuas() {
            var self = this;
            var search = this.doaSearch.toLowerCase();
            this.filteredDuas = this.allDuas.filter(function (d) {
                var matchCat =
                    self.activeDoaCategory === "semua" ||
                    d.category === self.activeDoaCategory;
                var matchSearch =
                    !search ||
                    d.title.toLowerCase().indexOf(search) !== -1 ||
                    d.text.toLowerCase().indexOf(search) !== -1 ||
                    (d.source && d.source.toLowerCase().indexOf(search) !== -1);
                return matchCat && matchSearch;
            });
            this.doaPage = 1;
            this.paginateDuas();
        },
        paginateDuas() {
            var start = (this.doaPage - 1) * this.doaPerPage;
            this.paginatedDuas = this.filteredDuas.slice(
                start,
                start + this.doaPerPage,
            );
            this.doaTotalPages = Math.max(
                1,
                Math.ceil(this.filteredDuas.length / this.doaPerPage),
            );
            var pages = [];
            var total = this.doaTotalPages;
            if (total <= 5) {
                for (var i = 1; i <= total; i++) pages.push(i);
            } else {
                var cur = this.doaPage;
                pages.push(1);
                if (cur > 3) pages.push("...");
                var s = Math.max(2, cur - 1);
                var e = Math.min(total - 1, cur + 1);
                for (var j = s; j <= e; j++) pages.push(j);
                if (cur < total - 2) pages.push("...");
                pages.push(total);
            }
            this.doaPageNumbers = pages;
        },
        toggleDoaExpand(id) {
            var idx = this.expandedDoas.indexOf(id);
            if (idx === -1) this.expandedDoas.push(id);
            else this.expandedDoas.splice(idx, 1);
        },
        getCategoryLabel(catId) {
            var map = {
                harian: "Harian",
                syukur: "Syukur",
                puja: "Puja",
                mantra: "Mantra",
                belajar: "Belajar",
                keluarga: "Keluarga",
                meditasi: "Meditasi",
                umum: "Umum",
            };
            return map[catId] || catId;
        },
        holyVerses: [
            {
                text: "Kamu tidak perlu bersedih atas apa yang tidak patut disedihkan. Orang bijak tidak bersedih baik untuk yang hidup maupun yang mati.",
                source: "Bhagavad Gita 2:11",
            },
            {
                text: "Jiwa tidak pernah dilahirkan dan tidak pernah mati. Ia tidak pernah berhenti ada. Jiwa tidak dilahirkan, kekal, abadi, dan purba. Ia tidak terbunuh ketika badan dibunuh.",
                source: "Bhagavad Gita 2:20",
            },
            {
                text: "Seperti seseorang menanggalkan pakaian yang usang dan mengenakan yang baru, demikian pula jiwa meninggalkan badan yang usang dan memasuki badan yang baru.",
                source: "Bhagavad Gita 2:22",
            },
            {
                text: "Engkau berhak melakukan kewajibanmu, tetapi tidak berhak atas hasil perbuatanmu. Janganlah hasil perbuatan menjadi motifmu, dan jangan pula terikat pada ketidakberbuat.",
                source: "Bhagavad Gita 2:47",
            },
            {
                text: "Bertindaklah dengan ketetapan dalam yoga, lepaskan segala kelekatan, dan bersikaplah sama dalam keberhasilan maupun kegagalan. Keseimbangan pikiran itulah yang disebut yoga.",
                source: "Bhagavad Gita 2:48",
            },
            {
                text: "Orang yang tidak terganggu oleh penderitaan dan tidak tergiur oleh kesenangan, yang bebas dari kelekatan, ketakutan, dan kemarahan, disebut orang bijak yang teguh pikirannya.",
                source: "Bhagavad Gita 2:56",
            },
            {
                text: "Kapan pun Dharma memudar dan Adharma merajalela, pada saat itulah Aku mewujudkan diri-Ku untuk melindungi kebajikan dan menghancurkan kejahatan.",
                source: "Bhagavad Gita 4:7-8",
            },
            {
                text: "Tidak ada yang menyamai pengetahuan dalam hal kesuciannya di dunia ini. Orang yang telah mencapai kesempurnaan dalam yoga akan merasakan pengetahuan itu di dalam dirinya pada waktunya.",
                source: "Bhagavad Gita 4:38",
            },
            {
                text: "Pelajarilah kebenaran dengan bersujud kepada guru, bertanya dengan rendah hati, dan melayani mereka. Para guru yang telah melihat kebenaran akan mengajarkan pengetahuan kepadamu.",
                source: "Bhagavad Gita 4:34",
            },
            {
                text: "Orang yang melihat-Ku di mana-mana dan melihat segala sesuatu di dalam-Ku, Aku tidak pernah hilang darinya dan ia tidak pernah hilang dari-Ku.",
                source: "Bhagavad Gita 6:30",
            },
            {
                text: "Pikiran yang gelisah dan tidak menentu dapat dikendalikan melalui latihan dan ketidaklekatan, wahai Arjuna.",
                source: "Bhagavad Gita 6:35",
            },
            {
                text: "Aku adalah asal mula segala sesuatu, dari-Ku segala ciptaan bermula. Mengetahui hal ini, orang bijak memuja-Ku dengan penuh perasaan.",
                source: "Bhagavad Gita 10:8",
            },
            {
                text: "Siapa pun yang mempersembahkan kepada-Ku dengan penuh bakti sehelai daun, sekuntum bunga, sebuah buah, atau seteguk air — Aku menerimanya karena dipersembahkan dengan cinta dan hati yang suci.",
                source: "Bhagavad Gita 9:26",
            },
            {
                text: "Di antara semua yogi, orang yang dengan penuh keyakinan selalu tinggal di dalam diri-Ku, memuja-Ku dengan penuh bakti, dialah yang paling erat bersatu dengan-Ku dalam yoga.",
                source: "Bhagavad Gita 6:47",
            },
            {
                text: "Mereka yang memuja-Ku, menyerahkan semua perbuatan kepada-Ku, menjadikan-Ku tujuan tertinggi, bermeditasi kepada-Ku dengan bakti yang tiada terbagi — mereka, Aku segera mengangkat dari lautan kelahiran dan kematian.",
                source: "Bhagavad Gita 12:6-7",
            },
            {
                text: "Ketika seseorang menanggapi kebahagiaan dan penderitaan orang lain seolah-olah itu kebahagiaan dan penderitaannya sendiri, ia dianggap telah mencapai yoga tertinggi.",
                source: "Bhagavad Gita 6:32",
            },
            {
                text: "Kemarahan melahirkan kebingungan, kebingungan melahirkan hilangnya ingatan, hilangnya ingatan melahirkan hancurnya akal budi, dan hancurnya akal budi membawa kehancuran.",
                source: "Bhagavad Gita 2:63",
            },
            {
                text: "Iman setiap orang sesuai dengan sifat alaminya. Manusia terbentuk dari imannya. Apa yang ia yakini, itulah ia.",
                source: "Bhagavad Gita 17:3",
            },
            {
                text: "Tinggalkan semua bentuk Dharma dan menyerahlah kepada-Ku semata. Aku akan membebaskanmu dari segala dosa; jangan bersedih.",
                source: "Bhagavad Gita 18:66",
            },
            {
                text: "Sang Atman tidak dapat dipotong oleh senjata, tidak dapat dibakar oleh api, tidak dapat dibasahi oleh air, dan tidak dapat dikeringkan oleh angin.",
                source: "Bhagavad Gita 2:23",
            },
        ],
        setDailyVerse() {
            var idx = (this.ramadhanDay - 1) % this.holyVerses.length;
            this.dailyVerse = this.holyVerses[idx];
        },
        refreshVerse() {
            var idx = Math.floor(Math.random() * this.holyVerses.length);
            this.dailyVerse = this.holyVerses[idx];
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
    };
}
