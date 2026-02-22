// @ts-nocheck
/**
 * Buku Kegiatan Positif — Dashboard Non-Muslim Alpine.js Component
 * Stripped from Islamic features (prayer times, qibla, Islamic duas).
 * Includes: Calendar, Progress, Christian Prayers, Bible Verses.
 */

function nonmuslimDashboard() {
    return {
        // ── State ──────────────────────────────────────────────────────
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
        isSunday: false,
        motivationalBadge: "",

        // ── Form State ─────────────────────────────────────────────────
        submittedDays: [],
        submissionStatuses: {},

        // ── Lifecycle ──────────────────────────────────────────────────
        init() {
            this.setDates();
            this.calculateRamadhanDay();
            this.checkSunday();
            this.loadSubmittedDays();
            this.buildCalendar();
            this.loadDoas();
            this.setDailyVerse();
            this.startClock();
            this.setMotivationalBadge();
        },

        // ── Clock ──────────────────────────────────────────────────────
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

                // Main clock follows selected timezone
                if (self.selectedTz === "WIB") self.clockMain = fmtFull(wib);
                else if (self.selectedTz === "WITA")
                    self.clockMain = fmtFull(wita);
                else self.clockMain = fmtFull(wit);

                self.clockWIB = fmtTime(wib);
                self.clockWITA = fmtTime(wita);
                self.clockWIT = fmtTime(wit);

                // Greeting
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
            var startDate = new Date(2026, 1, 19); // Feb 19 2026 = day 1
            var now = new Date();
            var today = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
            );
            var diff = Math.floor((today - startDate) / 86400000) + 1;
            this.ramadhanDay = Math.max(1, Math.min(diff, 30));
        },

        checkSunday() {
            this.isSunday = new Date().getDay() === 0;
        },

        setMotivationalBadge() {
            if (this.isSunday) {
                this.motivationalBadge = "Hari Minggu — Waktunya beribadah 🙏";
            } else {
                var badges = [
                    "Semangat berkegiatan positif!",
                    "Setiap hari adalah kesempatan baru",
                    "Jadilah berkat bagi orang lain",
                    "Teruslah bertumbuh dalam kebaikan",
                    "Lakukanlah kebaikan hari ini",
                ];
                this.motivationalBadge =
                    badges[this.ramadhanDay % badges.length];
            }
        },

        // ── Calendar ───────────────────────────────────────────────────
        buildCalendar() {
            var startDate = new Date(2026, 1, 19);
            var endDate = new Date(2026, 2, 20); // 30 days
            // Find the Monday of the week containing startDate
            var startDow = startDate.getDay(); // 0=Sun
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

            // Month label
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

            // Generate 5 weeks (35 cells max) to cover 30 days
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
                var isVerified = isCompleted && statusStr === "verified";
                var isPending =
                    isCompleted &&
                    (statusStr === "pending" || statusStr === "");
                var isRejected = isCompleted && statusStr === "rejected";
                var isPastUnfilled =
                    isPast && !isCompleted && !isToday && inRange;

                days.push({
                    key: "d" + i,
                    masehiDay: cur.getDate(),
                    hijriDay: inRange ? hijriDay : 0,
                    isToday: isToday,
                    isPast: isPast,
                    isCompleted: isCompleted,
                    isVerified: isVerified,
                    isPending: isPending,
                    isRejected: isRejected,
                    isPastUnfilled: isPastUnfilled,
                });

                d.setDate(d.getDate() + 1);
                // Stop after enough rows
                if (i > 27 && hijriDay >= 30 && d.getDay() === 1) break;
            }
            this.calendarDays = days;
        },

        // ── Per-user localStorage helpers ─────────────────────────────
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

        // ── Submitted Days ─────────────────────────────────────────────
        loadSubmittedDays() {
            try {
                var lastUser = localStorage.getItem("nonmuslim_last_user");
                var currentUser = window.__siswaUserId || "unknown";
                if (lastUser && lastUser !== currentUser) {
                    this._clearOldUserData("nonmuslim_submitted_days_");
                    this._clearOldUserData("nonmuslim_form_day_");
                }
                localStorage.setItem("nonmuslim_last_user", currentUser);

                var saved = localStorage.getItem(
                    this._lsKey("nonmuslim_submitted_days"),
                );
                this.submittedDays = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.submittedDays = [];
            }
            // Sync from server
            var self = this;
            fetch("/api/formulir", { headers: { Accept: "application/json" } })
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    if (data.success && data.submitted_days) {
                        self.submittedDays = data.submitted_days.slice();
                        localStorage.setItem(
                            self._lsKey("nonmuslim_submitted_days"),
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
            return Math.round((this.getVerifiedCount() / 30) * 100);
        },

        getVerifiedCount() {
            var count = 0;
            for (var key in this.submissionStatuses) {
                if (this.submissionStatuses[key].status === "verified") count++;
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

        // ── Christian Prayers (Doa Kristen) ────────────────────────────
        loadDoas() {
            this.allDuas = [
                // === Doa Harian ===
                {
                    id: 1,
                    title: "Doa Bapa Kami",
                    category: "harian",
                    source: "Matius 6:9-13",
                    text: "Bapa kami yang di surga, dikuduskanlah nama-Mu, datanglah Kerajaan-Mu, jadilah kehendak-Mu di bumi seperti di surga.\nBerikanlah kami pada hari ini makanan kami yang secukupnya dan ampunilah kami akan kesalahan kami, seperti kami juga mengampuni orang yang bersalah kepada kami.\nDan janganlah membawa kami ke dalam pencobaan, tetapi lepaskanlah kami dari yang jahat.\nKarena Engkaulah yang empunya Kerajaan dan kuasa dan kemuliaan sampai selama-lamanya. Amin.",
                    verse: '"Berdoalah demikian: Bapa kami yang di surga, dikuduskanlah nama-Mu." — Matius 6:9',
                },
                {
                    id: 2,
                    title: "Doa Bangun Tidur",
                    category: "harian",
                    source: "Doa Pagi Kristiani",
                    text: "Tuhan Yesus, terima kasih untuk hari yang baru ini. Terima kasih karena Engkau setia menjaga kami sepanjang malam. Biarlah hari ini Engkau menyertai setiap langkah kami, memberi hikmat dan kekuatan untuk melakukan yang benar. Amin.",
                    verse: '"Inilah hari yang dijadikan Tuhan, marilah kita bergirang dan bersukacita karenanya." — Mazmur 118:24',
                },
                {
                    id: 3,
                    title: "Doa Sebelum Tidur",
                    category: "harian",
                    source: "Doa Malam Kristiani",
                    text: "Tuhan, terima kasih untuk hari ini. Ampunilah segala kesalahan kami hari ini. Lindungilah kami sepanjang malam ini dan berikanlah kami istirahat yang damai. Biarlah kami bangun esok hari dengan semangat baru untuk memuliakan-Mu. Amin.",
                    verse: '"Dengan tenteram aku mau membaringkan diri, lalu segera tidur, sebab hanya Engkaulah, ya Tuhan, yang membiarkan aku diam dengan aman." — Mazmur 4:9',
                },
                {
                    id: 4,
                    title: "Doa Sebelum Makan",
                    category: "harian",
                    source: "Doa Meja Kristiani",
                    text: "Ya Tuhan, berkatilah makanan dan minuman ini bagi tubuh kami. Terima kasih atas berkat-Mu yang selalu mencukupi kebutuhan kami. Kiranya makanan ini menguatkan kami untuk melayani-Mu. Dalam nama Tuhan Yesus kami berdoa. Amin.",
                    verse: '"Karena dari Dialah dan oleh Dialah dan kepada Dialah segala sesuatu. Bagi Dialah kemuliaan sampai selama-lamanya!" — Roma 11:36',
                },
                {
                    id: 5,
                    title: "Doa Sesudah Makan",
                    category: "harian",
                    source: "Doa Meja Kristiani",
                    text: "Terima kasih Tuhan untuk makanan yang telah kami nikmati. Terima kasih atas kasih-Mu yang besar dalam memenuhi kebutuhan kami setiap hari. Amin.",
                    verse: "",
                },

                // === Doa Ucapan Syukur ===
                {
                    id: 6,
                    title: "Doa Ucapan Syukur",
                    category: "syukur",
                    source: "Mazmur 136:1",
                    text: "Tuhan yang Mahakasih, kami mengucap syukur kepada-Mu karena kasih setia-Mu kekal selama-lamanya. Terima kasih untuk berkat kehidupan, kesehatan, keluarga, dan segala kebaikan yang Engkau limpahkan. Ajarlah kami selalu bersyukur dalam segala keadaan. Amin.",
                    verse: '"Bersyukurlah kepada Tuhan, sebab Ia baik! Bahwasanya untuk selama-lamanya kasih setia-Nya." — Mazmur 136:1',
                },
                {
                    id: 7,
                    title: "Doa Syukur atas Keluarga",
                    category: "syukur",
                    source: "Doa Kristiani",
                    text: "Tuhan, terima kasih untuk keluarga yang Engkau berikan. Berkati orang tua kami, saudara-saudari kami, dan seluruh anggota keluarga. Jadikanlah keluarga kami tempat di mana kasih-Mu nyata. Amin.",
                    verse: '"Besi menajamkan besi, orang menajamkan sesamanya." — Amsal 27:17',
                },
                {
                    id: 8,
                    title: "Doa Syukur di Pagi Hari",
                    category: "syukur",
                    source: "Ratapan 3:22-23",
                    text: "Tuhan, kasih setia-Mu tidak berkesudahan, belas kasihan-Mu tidak habis-habisnya, setiap pagi selalu baru. Terima kasih untuk pagi yang indah ini. Biarlah hari ini menjadi hari yang penuh dengan kasih karunia-Mu. Amin.",
                    verse: '"Bahwasanya kemurahan Tuhan tidak berkesudahan, belas kasihan-Nya tidak habis-habisnya: setiap pagi selalu baru." — Ratapan 3:22-23',
                },

                // === Doa Pengampunan ===
                {
                    id: 9,
                    title: "Doa Memohon Pengampunan",
                    category: "pengampunan",
                    source: "1 Yohanes 1:9",
                    text: "Tuhan Yesus, aku datang kepada-Mu dengan hati yang rendah. Ampunilah segala dosa dan kesalahanku. Aku tahu Engkau setia dan adil untuk mengampuni dosa-dosaku dan menyucikanku dari segala kejahatan. Tolonglah aku untuk tidak mengulangi kesalahan yang sama. Amin.",
                    verse: '"Jika kita mengaku dosa kita, Ia adalah setia dan adil, sehingga Ia akan mengampuni segala dosa kita dan menyucikan kita dari segala kejahatan." — 1 Yohanes 1:9',
                },
                {
                    id: 10,
                    title: "Doa Pertobatan",
                    category: "pengampunan",
                    source: "Mazmur 51",
                    text: "Ya Allah, kasihanilah aku menurut kasih setia-Mu, hapuskanlah pelanggaranku menurut rahmat-Mu yang besar! Bersihkanlah aku seluruhnya dari kesalahanku, dan tahirkanlah aku dari dosaku. Jadikanlah hatiku yang bersih, ya Allah, dan perbaharuilah batinku dengan roh yang teguh. Amin.",
                    verse: '"Jadikanlah hatiku yang bersih, ya Allah, dan perbaharuilah batinku dengan roh yang teguh." — Mazmur 51:12',
                },

                // === Doa Kekuatan ===
                {
                    id: 11,
                    title: "Doa Memohon Kekuatan",
                    category: "kekuatan",
                    source: "Filipi 4:13",
                    text: "Tuhan, aku percaya bahwa segala perkara dapat kutanggung di dalam Dia yang memberi kekuatan kepadaku. Berilah aku kekuatan untuk menghadapi setiap tantangan hari ini. Biarlah aku tidak mengandalkan kekuatanku sendiri, tetapi selalu bergantung kepada-Mu. Amin.",
                    verse: '"Segala perkara dapat kutanggung di dalam Dia yang memberi kekuatan kepadaku." — Filipi 4:13',
                },
                {
                    id: 12,
                    title: "Doa Ketenangan Hati",
                    category: "kekuatan",
                    source: "Yohanes 14:27",
                    text: "Tuhan Yesus, Engkau berkata: Damai-Ku Kuberikan kepadamu. Berilah aku damai yang melampaui segala akal. Tenangkanlah hatiku yang gelisah dan gundah. Aku percaya Engkau menggenggam hidupku. Amin.",
                    verse: '"Damai sejahtera Kutinggalkan bagimu. Damai sejahtera-Ku Kuberikan kepadamu." — Yohanes 14:27',
                },
                {
                    id: 13,
                    title: "Doa Saat Menghadapi Masalah",
                    category: "kekuatan",
                    source: "Yesaya 41:10",
                    text: "Tuhan, janganlah aku takut, sebab Engkau menyertaiku. Janganlah aku cemas, sebab Engkau Allahku. Engkau menguatkanku dan menolongku. Biarlah aku menghadapi masalah ini dengan iman dan keberanian yang datang dari-Mu. Amin.",
                    verse: '"Janganlah takut, sebab Aku menyertai engkau, janganlah cemas, sebab Aku ini Allahmu." — Yesaya 41:10',
                },
                {
                    id: 14,
                    title: "Doa Saat Kesepian",
                    category: "kekuatan",
                    source: "Ulangan 31:6",
                    text: "Tuhan, meskipun aku merasa sendiri, aku tahu Engkau selalu ada bersamaku. Engkau tidak pernah meninggalkan aku, tidak pernah melepaskan aku. Hibur hatiku dan berilah aku teman-teman yang mendukung. Amin.",
                    verse: '"Kuatkan dan teguhkanlah hatimu, janganlah takut dan jangan gemetar. Sebab Tuhan, Allahmu, Dialah yang berjalan menyertai engkau; Ia tidak akan membiarkan engkau dan tidak akan meninggalkan engkau." — Ulangan 31:6',
                },

                // === Doa Belajar ===
                {
                    id: 15,
                    title: "Doa Sebelum Belajar",
                    category: "belajar",
                    source: "Amsal 2:6",
                    text: "Tuhan, Engkau adalah sumber segala hikmat. Bukakanlah pikiranku agar aku dapat memahami pelajaran yang akan kupelajari. Berilah aku konsentrasi dan daya ingat yang baik. Biarlah ilmu yang kupelajari menjadi berkat bagi banyak orang. Amin.",
                    verse: '"Karena Tuhanlah yang memberikan hikmat, dari mulut-Nya datang pengetahuan dan kepandaian." — Amsal 2:6',
                },
                {
                    id: 16,
                    title: "Doa Sesudah Belajar",
                    category: "belajar",
                    source: "Doa Kristiani",
                    text: "Terima kasih Tuhan, untuk waktu belajar yang telah Engkau berikan. Tolonglah aku mengingat dan memahami apa yang telah kupelajari. Biarlah pengetahuan ini berguna bagi masa depanku dan untuk kemuliaan-Mu. Amin.",
                    verse: '"Hati orang berpengertian memperoleh pengetahuan, dan telinga orang bijak menuntut pengetahuan." — Amsal 18:15',
                },
                {
                    id: 17,
                    title: "Doa Menghadapi Ujian",
                    category: "belajar",
                    source: "Yakobus 1:5",
                    text: "Tuhan, aku akan menghadapi ujian. Berilah aku ketenangan, konsentrasi, dan hikmat untuk menjawab setiap pertanyaan. Aku percaya Engkau memberikan hikmat dengan murah hati kepada siapa pun yang memintanya. Amin.",
                    verse: '"Tetapi apabila di antara kamu ada yang kekurangan hikmat, hendaklah ia memintakannya kepada Allah, — yang memberikan kepada semua orang dengan murah hati." — Yakobus 1:5',
                },

                // === Doa Keluarga ===
                {
                    id: 18,
                    title: "Doa untuk Orang Tua",
                    category: "keluarga",
                    source: "Ef. 6:1-3",
                    text: "Tuhan, berkatilah ayah dan ibu kami. Berilah mereka kesehatan, umur panjang, dan kedamaian. Ajarlah kami untuk selalu menghormati dan mencintai orang tua kami. Peliharalah hubungan keluarga kami agar selalu harmonis dalam kasih-Mu. Amin.",
                    verse: '"Hormatilah ayahmu dan ibumu — ini adalah suatu perintah yang penting, seperti yang nyata dari janji ini." — Efesus 6:2',
                },
                {
                    id: 19,
                    title: "Doa untuk Teman & Sahabat",
                    category: "keluarga",
                    source: "Amsal 17:17",
                    text: "Tuhan, terima kasih untuk teman-teman yang Engkau berikan. Berkatilah mereka semua. Ajarlah kami saling mengasihi, saling mendukung, dan saling menguatkan. Biarlah persahabatan kami menjadi berkat dan memuliakan-Mu. Amin.",
                    verse: '"Seorang sahabat menaruh kasih setiap waktu, dan menjadi saudara dalam kesukaran." — Amsal 17:17',
                },
                {
                    id: 20,
                    title: "Doa untuk Guru & Sekolah",
                    category: "keluarga",
                    source: "Doa Kristiani",
                    text: "Tuhan, berkatilah guru-guru kami yang dengan sabar mendidik dan mengajar kami. Berilah mereka kesehatan dan semangat. Berkatilah sekolah kami agar menjadi tempat yang aman dan nyaman untuk belajar. Amin.",
                    verse: "",
                },

                // === Doa Ibadah ===
                {
                    id: 21,
                    title: "Doa Sebelum Ibadah",
                    category: "ibadah",
                    source: "Mazmur 122:1",
                    text: "Tuhan, betapa senangnya hatiku ketika diajak pergi ke rumah-Mu. Bukakanlah hati dan pikiranku saat mendengarkan firman-Mu. Biarlah ibadah ini menjadi berkat dan memperbaharui imanku. Amin.",
                    verse: '"Aku bersukacita ketika orang berkata kepadaku: "Mari kita pergi ke rumah Tuhan!"" — Mazmur 122:1',
                },
                {
                    id: 22,
                    title: "Doa Sesudah Ibadah",
                    category: "ibadah",
                    source: "Doa Kristiani",
                    text: "Terima kasih Tuhan untuk ibadah hari ini. Biarlah firman-Mu tinggal di dalam hatiku dan mengubah hidupku. Tolonglah aku untuk melakukan apa yang telah kudengar dan kupelajari dari-Mu hari ini. Amin.",
                    verse: "",
                },
                {
                    id: 23,
                    title: "Doa Hari Minggu",
                    category: "ibadah",
                    source: "Ibrani 10:25",
                    text: "Tuhan, di hari Minggu ini aku ingin mengkhususkan waktu untuk beribadah kepada-Mu. Sertailah perjalananku ke gereja, berkatilah ibadah yang akan kami lakukan bersama jemaat. Biarlah pujian dan penyembahan kami berkenan di hadapan-Mu. Amin.",
                    verse: '"Janganlah kita menjauhkan diri dari pertemuan-pertemuan ibadah kita, seperti kebiasaan beberapa orang, tetapi marilah kita saling menasihati." — Ibrani 10:25',
                },

                // === Doa Umum ===
                {
                    id: 24,
                    title: "Doa Minta Berkat",
                    category: "umum",
                    source: "Bilangan 6:24-26",
                    text: "Tuhan, berkatilah kami dan peliharalah kami. Sinarilah kami dengan wajah-Mu dan kasihanilah kami. Hadapkanlah wajah-Mu kepada kami dan berilah kami damai sejahtera. Amin.",
                    verse: '"Tuhan memberkati engkau dan melindungi engkau; Tuhan menyinari engkau dengan wajah-Nya dan memberi engkau kasih karunia; Tuhan menghadapkan wajah-Nya kepadamu dan memberi engkau damai sejahtera." — Bilangan 6:24-26',
                },
                {
                    id: 25,
                    title: "Doa Penyerahan Diri",
                    category: "umum",
                    source: "Amsal 3:5-6",
                    text: "Tuhan, aku mau percaya kepada-Mu dengan segenap hatiku dan tidak bersandar pada pengertianku sendiri. Dalam segala lakuku aku mau mengakui Engkau, dan biarlah Engkau yang meluruskan jalanku. Amin.",
                    verse: '"Percayalah kepada Tuhan dengan segenap hatimu, dan janganlah bersandar kepada pengertianmu sendiri. Akuilah Dia dalam segala lakumu, maka Ia akan meluruskan jalanmu." — Amsal 3:5-6',
                },
                {
                    id: 26,
                    title: "Doa Rasa Takut & Cemas",
                    category: "umum",
                    source: "Filipi 4:6-7",
                    text: "Tuhan, Firman-Mu mengatakan jangan khawatir tentang apa pun juga ketika aku berdoa. Aku menyerahkan segala kekuatiranku kepada-Mu dalam doa dan permohonan dengan ucapan syukur. Berilah aku damai sejahtera-Mu yang melampaui segala akal. Amin.",
                    verse: '"Janganlah hendaknya kamu kuatir tentang apa pun juga, tetapi nyatakanlah dalam segala hal keinginanmu kepada Allah dalam doa dan permohonan dengan ucapan syukur." — Filipi 4:6',
                },
                {
                    id: 27,
                    title: "Doa untuk Bangsa & Negara",
                    category: "umum",
                    source: "1 Timotius 2:1-2",
                    text: "Tuhan, kami mendoakan bangsa dan negara Indonesia. Berkatilah para pemimpin kami agar memiliki hikmat dan keadilan. Jagalah persatuan dan kedamaian di negeri kami. Biarlah Indonesia menjadi bangsa yang memberkati dunia. Amin.",
                    verse: '"Pertama-tama aku menasihatkan: Naikkanlah permohonan, doa syafaat dan ucapan syukur untuk semua orang, untuk raja-raja dan untuk semua pembesar." — 1 Timotius 2:1-2',
                },
                {
                    id: 28,
                    title: "Doa Kasih untuk Sesama",
                    category: "umum",
                    source: "1 Korintus 13:4-7",
                    text: "Tuhan, ajarlah aku mengasihi sesama seperti Engkau mengasihi kami. Biarlah kasih itu sabar dan murah hati, tidak cemburu, tidak memegahkan diri, dan tidak sombong. Biarlah aku selalu menutupi segala sesuatu, percaya segala sesuatu, mengharapkan segala sesuatu, dan sabar menanggung segala sesuatu. Amin.",
                    verse: '"Kasih itu sabar; kasih itu murah hati; ia tidak cemburu. Ia tidak memegahkan diri dan tidak sombong." — 1 Korintus 13:4',
                },
            ];

            // Build category counts
            var catMap = {};
            this.allDuas.forEach(function (d) {
                catMap[d.category] = (catMap[d.category] || 0) + 1;
            });
            this.doaCategories = [
                { id: "semua", label: "Semua", count: this.allDuas.length },
                { id: "harian", label: "Harian", count: catMap["harian"] || 0 },
                { id: "syukur", label: "Syukur", count: catMap["syukur"] || 0 },
                {
                    id: "pengampunan",
                    label: "Pengampunan",
                    count: catMap["pengampunan"] || 0,
                },
                {
                    id: "kekuatan",
                    label: "Kekuatan",
                    count: catMap["kekuatan"] || 0,
                },
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
                { id: "ibadah", label: "Ibadah", count: catMap["ibadah"] || 0 },
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
            // Build page numbers
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
                pengampunan: "Pengampunan",
                kekuatan: "Kekuatan",
                belajar: "Belajar",
                keluarga: "Keluarga",
                ibadah: "Ibadah",
                umum: "Umum",
            };
            return map[catId] || catId;
        },

        // ── Bible Verses ───────────────────────────────────────────────
        bibleVerses: [
            {
                text: "Sebab Aku ini mengetahui rancangan-rancangan apa yang ada pada-Ku mengenai kamu, demikianlah firman Tuhan, yaitu rancangan damai sejahtera dan bukan rancangan kecelakaan, untuk memberikan kepadamu hari depan yang penuh harapan.",
                source: "Yeremia 29:11",
            },
            {
                text: "Tuhan adalah gembalaku, takkan kekurangan aku. Ia membaringkan aku di padang yang berumput hijau, Ia membimbing aku ke air yang tenang.",
                source: "Mazmur 23:1-2",
            },
            {
                text: "Tetapi orang-orang yang menanti-nantikan Tuhan mendapat kekuatan baru: mereka seumpama rajawali yang naik terbang dengan kekuatan sayapnya.",
                source: "Yesaya 40:31",
            },
            {
                text: "Segala perkara dapat kutanggung di dalam Dia yang memberi kekuatan kepadaku.",
                source: "Filipi 4:13",
            },
            {
                text: "Karena begitu besar kasih Allah akan dunia ini, sehingga Ia telah mengaruniakan Anak-Nya yang tunggal, supaya setiap orang yang percaya kepada-Nya tidak binasa, melainkan beroleh hidup yang kekal.",
                source: "Yohanes 3:16",
            },
            {
                text: "Percayalah kepada Tuhan dengan segenap hatimu, dan janganlah bersandar kepada pengertianmu sendiri. Akuilah Dia dalam segala lakumu, maka Ia akan meluruskan jalanmu.",
                source: "Amsal 3:5-6",
            },
            {
                text: "Janganlah takut, sebab Aku menyertai engkau, janganlah cemas, sebab Aku ini Allahmu; Aku akan meneguhkan, bahkan akan menolong engkau.",
                source: "Yesaya 41:10",
            },
            {
                text: "Kasih itu sabar; kasih itu murah hati; ia tidak cemburu. Ia tidak memegahkan diri dan tidak sombong.",
                source: "1 Korintus 13:4",
            },
            {
                text: "Kamu adalah terang dunia. Kota yang terletak di atas gunung tidak mungkin tersembunyi.",
                source: "Matius 5:14",
            },
            {
                text: "Berbahagialah orang yang membawa damai, karena mereka akan disebut anak-anak Allah.",
                source: "Matius 5:9",
            },
            {
                text: "Serahkanlah segala kekuatiranmu kepada-Nya, sebab Ia yang memelihara kamu.",
                source: "1 Petrus 5:7",
            },
            {
                text: "Bersyukurlah dalam segala hal, sebab itulah yang dikehendaki Allah di dalam Kristus Yesus bagi kamu.",
                source: "1 Tesalonika 5:18",
            },
            {
                text: "Bergembiralah senantiasa dalam Tuhan! Sekali lagi kukatakan: Bergembiralah!",
                source: "Filipi 4:4",
            },
            {
                text: "Tetapi kamu akan menerima kuasa, kalau Roh Kudus turun ke atas kamu, dan kamu akan menjadi saksi-Ku.",
                source: "Kisah Para Rasul 1:8",
            },
            {
                text: "Tuhan dekat kepada orang-orang yang patah hati, dan Ia menyelamatkan orang-orang yang remuk jiwanya.",
                source: "Mazmur 34:19",
            },
            {
                text: "Bahwasanya kemurahan Tuhan tidak berkesudahan, belas kasihan-Nya tidak habis-habisnya: setiap pagi selalu baru; besar kesetiaan-Mu!",
                source: "Ratapan 3:22-23",
            },
            {
                text: "Dan kita tahu, bahwa Allah turut bekerja dalam segala sesuatu untuk mendatangkan kebaikan bagi mereka yang mengasihi Dia.",
                source: "Roma 8:28",
            },
            {
                text: "Sebab sesungguhnya bersama kesulitan ada kemudahan. Tuhan memberkati orang yang bersabar.",
                source: "Yakobus 1:12",
            },
            {
                text: "Akulah jalan dan kebenaran dan hidup. Tidak ada seorang pun yang datang kepada Bapa, kalau tidak melalui Aku.",
                source: "Yohanes 14:6",
            },
            {
                text: "Hendaklah kamu saling mengasihi, sebab kasih berasal dari Allah; dan setiap orang yang mengasihi, lahir dari Allah dan mengenal Allah.",
                source: "1 Yohanes 4:7",
            },
        ],

        setDailyVerse() {
            // Pick verse based on day number
            var idx = (this.ramadhanDay - 1) % this.bibleVerses.length;
            this.dailyVerse = this.bibleVerses[idx];
        },

        refreshVerse() {
            var idx = Math.floor(Math.random() * this.bibleVerses.length);
            this.dailyVerse = this.bibleVerses[idx];
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

            fetch("/api/change-password", {
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
            })
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
                .catch(function () {
                    self.pwLoading = false;
                    self.pwMessage = "Terjadi kesalahan. Coba lagi.";
                });
        },
    };
}
