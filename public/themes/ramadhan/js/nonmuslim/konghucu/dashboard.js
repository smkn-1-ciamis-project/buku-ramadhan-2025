// ── Dynamic App Settings Helper ──────────────────────────────────────
var _appCfg = window.__appSettings || {};
function _parseSettingDate(dateStr) {
    if (!dateStr) return null;
    var p = dateStr.split("-");
    return new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
}
var _ramadhanStart =
    _parseSettingDate(_appCfg.ramadhan_start_date) || new Date(2026, 1, 19);
var _ramadhanEnd =
    _parseSettingDate(_appCfg.ramadhan_end_date) || new Date(2026, 2, 20);
var _ramadhanTotalDays = _appCfg.ramadhan_total_days || 30;

function konghucuDashboard() {
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
        isSunday: false,
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
            this.checkSunday();
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
            var startDate = new Date(_ramadhanStart);
            var now = new Date();
            var today = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
            );
            var diff = Math.floor((today - startDate) / 86400000) + 1;
            this.ramadhanDay = Math.max(1, Math.min(diff, _ramadhanTotalDays));
        },
        checkSunday() {
            this.isSunday = new Date().getDay() === 0;
        },
        setMotivationalBadge() {
            if (this.isSunday) {
                this.motivationalBadge = "Hari Minggu — Waktunya sembahyang 🙏";
            } else {
                var badges = [
                    "Semangat berkegiatan positif!",
                    "Setiap hari adalah kesempatan baru",
                    "Berbuat kebajikan kepada sesama",
                    "Teruslah belajar dan berkembang",
                    "Lakukanlah kebaikan hari ini",
                ];
                this.motivationalBadge =
                    badges[this.ramadhanDay % badges.length];
            }
        },
        buildCalendar() {
            var startDate = new Date(_ramadhanStart);
            var endDate = new Date(_ramadhanEnd);
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
                var inRange = hijriDay >= 1 && hijriDay <= _ramadhanTotalDays;
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
                var lastUser = localStorage.getItem("konghucu_last_user");
                var currentUser = window.__siswaUserId || "unknown";
                if (lastUser && lastUser !== currentUser) {
                    this._clearOldUserData("konghucu_submitted_days_");
                    this._clearOldUserData("konghucu_form_day_");
                }
                localStorage.setItem("konghucu_last_user", currentUser);
                var saved = localStorage.getItem(
                    this._lsKey("konghucu_submitted_days"),
                );
                this.submittedDays = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.submittedDays = [];
            }
            var self = this;
            ApiRepository.formulir
                .getAll()
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    if (data.success && data.submitted_days) {
                        self.submittedDays = data.submitted_days.slice();
                        localStorage.setItem(
                            self._lsKey("konghucu_submitted_days"),
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
                    if (ApiRepository.isRateLimited(e)) console.warn(e.message);
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
                    title: "Doa Harian Konghucu",
                    category: "harian",
                    source: "Lunyu 1:1",
                    text: "Ya Tian Yang Maha Agung, pada hari ini hamba memohon bimbingan-Mu agar senantiasa berada di jalan kebajikan. Berilah hamba kekuatan untuk menjalankan Ren (cinta kasih) dan Yi (kebenaran) dalam setiap tindakan. Semoga segala perbuatan hamba hari ini membawa kebaikan bagi sesama dan memuliakan-Mu. Xian You Yi De.",
                    verse: '"Belajar dan terus-menerus mengulang, bukankah itu menyenangkan? Ada kawan datang dari jauh, bukankah itu membahagiakan?" — Lunyu 1:1',
                },
                {
                    id: 2,
                    title: "Doa Bangun Tidur (Konghucu)",
                    category: "harian",
                    source: "Doa Harian Konghucu",
                    text: "Ya Tian, terima kasih Engkau telah menganugerahkan hari yang baru ini. Hamba bersyukur atas nafas kehidupan yang masih Engkau berikan. Bimbinglah hamba agar hari ini selalu berada di jalan Dao, menjalankan kebajikan, dan menjadi manusia yang berguna bagi sesama. Xian You Yi De.",
                    verse: '"Orang yang bercita-cita pada Dao, berpegang pada De, bersandar pada Ren, dan bersenang-senang dalam seni." — Lunyu 7:6',
                },
                {
                    id: 3,
                    title: "Doa Sebelum Tidur (Konghucu)",
                    category: "harian",
                    source: "Lunyu 1:4",
                    text: "Ya Tian, hamba bersyukur atas segala yang telah Engkau berikan hari ini. Hamba memohon ampun jika ada perbuatan yang menyimpang dari jalan kebajikan. Seperti ajaran Nabi Kongzi, hamba bertanya pada diri sendiri: Apakah hari ini hamba sudah setia dalam melaksanakan tugas? Apakah hamba sudah jujur terhadap teman? Apakah hamba sudah mengamalkan ajaran guru? Semoga esok hamba menjadi lebih baik. Xian You Yi De.",
                    verse: '"Setiap hari aku memeriksa diriku dalam tiga hal: Apakah dalam melaksanakan tugas untuk orang lain aku sudah setia? Apakah dalam bergaul dengan teman aku sudah jujur? Apakah ajaran guru sudah kupraktikkan?" — Lunyu 1:4',
                },
                {
                    id: 4,
                    title: "Doa Sebelum Makan (Konghucu)",
                    category: "harian",
                    source: "Doa Meja Konghucu",
                    text: "Ya Tian, terima kasih atas rezeki makanan dan minuman yang Engkau berikan. Semoga makanan ini menguatkan badan hamba untuk menjalankan kebajikan. Hamba tidak lupa bersyukur kepada mereka yang telah bekerja keras menyediakan makanan ini. Xian You Yi De.",
                    verse: '"Junzi (manusia berbudi luhur) dalam hal makan tidak mencari kenikmatan berlebihan." — Lunyu 1:14',
                },
                {
                    id: 5,
                    title: "Doa Sesudah Makan (Konghucu)",
                    category: "harian",
                    source: "Doa Meja Konghucu",
                    text: "Terima kasih ya Tian, atas makanan yang telah hamba nikmati. Semoga kekuatan dari makanan ini hamba gunakan untuk melakukan perbuatan baik dan berbakti kepada sesama. Xian You Yi De.",
                    verse: "",
                },
                {
                    id: 6,
                    title: "Doa Syukur kepada Tian",
                    category: "syukur",
                    source: "Zhongyong 1",
                    text: "Ya Tian Yang Mahabesar, hamba memanjatkan syukur atas segala karunia-Mu yang tiada terhingga. Engkau memberikan kehidupan, kesehatan, keluarga, dan semua kebaikan. Hamba berjanji akan terus berusaha menjalankan kodrat sejati (Xing) yang telah Engkau anugerahkan. Xian You Yi De.",
                    verse: '"Kodrat yang dianugerahkan Tian disebut Xing (watak sejati). Menjalankan watak sejati disebut Dao (jalan). Membina Dao disebut Jiao (ajaran)." — Zhongyong 1',
                },
                {
                    id: 7,
                    title: "Doa Syukur atas Kebaikan",
                    category: "syukur",
                    source: "Lunyu 7:22",
                    text: "Ya Tian, hamba bersyukur atas segala kebaikan yang ada di dunia ini. Terima kasih telah menempatkan hamba di antara orang-orang baik. Biarlah hamba belajar dari kebaikan orang lain dan memperbaiki diri dari kekurangan mereka. Semoga rasa syukur ini menjadi jalan menuju kebajikan sejati. Xian You Yi De.",
                    verse: '"Bila berjalan bertiga, pasti ada yang bisa menjadi guruku. Kupilih sifat baiknya untuk kuikuti, sifat buruknya untuk kuperbarui." — Lunyu 7:22',
                },
                {
                    id: 8,
                    title: "Doa Syukur di Pagi Hari",
                    category: "syukur",
                    source: "Lunyu 4:8",
                    text: "Ya Tian, di pagi yang baru ini hamba mengucap syukur. Setiap hari adalah kesempatan untuk mendengar tentang Dao dan menjalaninya. Bimbinglah hamba agar hari ini dipenuhi dengan kebaikan, kebijaksanaan, dan cinta kasih. Xian You Yi De.",
                    verse: '"Pagi hari mendengar Dao, sore hari mati pun tiada penyesalan." — Lunyu 4:8',
                },
                {
                    id: 9,
                    title: "Doa Sembahyang kepada Tian",
                    category: "sembahyang",
                    source: "Lunyu 3:13",
                    text: "Ya Tian Yang Maha Esa, hamba datang dengan hati yang tulus untuk bersembahyang kepada-Mu. Hamba memohon bimbingan agar senantiasa berjalan di jalan yang benar. Ampunilah segala kesalahan hamba. Bimbinglah langkah hamba menuju jalan Dao yang lurus. Xian You Yi De.",
                    verse: '"Siapa yang berdosa kepada Tian, tidak ada lagi tempat berdoa memohon ampun." — Lunyu 3:13',
                },
                {
                    id: 10,
                    title: "Doa Sembahyang di Klenteng",
                    category: "sembahyang",
                    source: "Lunyu 10:11",
                    text: "Ya Tian, hamba hadir di tempat suci ini untuk mempersembahkan sembahyang. Dengan hati yang khusyuk dan pikiran yang tenang, hamba memohon berkat dan perlindungan-Mu. Semoga sembahyang ini diterima dan memperkuat hubungan hamba dengan Dao. Xian You Yi De.",
                    verse: '"Nabi Kongzi ketika berada di tempat ibadah, bersikap sangat khusyuk dan penuh hormat." — Lunyu 10:11',
                },
                {
                    id: 11,
                    title: "Doa Mengenang Leluhur",
                    category: "sembahyang",
                    source: "Lunyu 1:9",
                    text: "Ya Tian, hamba persembahkan doa ini untuk para leluhur yang telah mendahului. Semoga arwah mereka berada dalam kedamaian. Hamba berterima kasih atas jasa dan pengorbanan mereka. Semoga hamba dapat meneruskan kebajikan yang mereka wariskan. Xian You Yi De.",
                    verse: '"Berhati-hatilah di saat akhir hayat dan kenangkanlah yang jauh sudah tiada, niscaya kebajikan rakyat akan tebal kembali." — Lunyu 1:9',
                },
                {
                    id: 12,
                    title: "Delapan Kebajikan (Ba De)",
                    category: "kebajikan",
                    source: "Ajaran Konfusianisme",
                    text: "Ya Tian, bimbinglah hamba untuk menjalankan Delapan Kebajikan:\n1. Xiao (孝) — Berbakti kepada orang tua\n2. Di (悌) — Hormat kepada saudara\n3. Zhong (忠) — Setia dan loyal\n4. Xin (信) — Dapat dipercaya\n5. Li (禮) — Menjunjung kesusilaan\n6. Yi (義) — Menegakkan kebenaran\n7. Lian (廉) — Bersih dan jujur\n8. Chi (恥) — Memiliki rasa malu\nSemoga kedelapan kebajikan ini menjadi panduan hidup hamba. Xian You Yi De.",
                    verse: '"Seorang Junzi menuntut dari dirinya sendiri, seorang Xiaoren (orang kecil) menuntut dari orang lain." — Lunyu 15:21',
                },
                {
                    id: 13,
                    title: "Lima Hubungan (Wu Lun)",
                    category: "kebajikan",
                    source: "Mengzi — Teng Wen Gong",
                    text: "Ya Tian, ajarlah hamba untuk menjalankan Lima Hubungan yang harmonis:\n1. Raja dan rakyat — ada keadilan (Yi)\n2. Orang tua dan anak — ada kasih sayang (Qin)\n3. Suami dan istri — ada pembagian tugas (Bie)\n4. Tua dan muda — ada urutan (Xu)\n5. Teman dan teman — ada kepercayaan (Xin)\nSemoga hamba mampu menjaga hubungan yang baik dengan semua orang. Xian You Yi De.",
                    verse: '"Antara ayah dan anak ada kasih sayang, antara raja dan menteri ada keadilan, antara suami dan istri ada pembagian, antara tua dan muda ada urutan, antara teman ada kepercayaan." — Mengzi, Teng Wen Gong',
                },
                {
                    id: 14,
                    title: "Doa Menjalankan Ren (Cinta Kasih)",
                    category: "kebajikan",
                    source: "Lunyu 12:22",
                    text: "Ya Tian, anugerahkanlah kepada hamba hati yang penuh Ren (cinta kasih). Ajarlah hamba untuk mengasihi sesama manusia tanpa membeda-bedakan. Semoga cinta kasih ini terwujud dalam setiap perkataan dan perbuatan hamba. Xian You Yi De.",
                    verse: '"Fan Chi bertanya tentang Ren. Nabi bersabda: Mengasihi sesama manusia." — Lunyu 12:22',
                },
                {
                    id: 15,
                    title: "Doa Menegakkan Yi (Kebenaran)",
                    category: "kebajikan",
                    source: "Lunyu 4:16",
                    text: "Ya Tian, berilah hamba keberanian untuk selalu menegakkan Yi (kebenaran). Jadikanlah hamba orang yang mengerti kebenaran, bukan orang yang hanya mengejar keuntungan. Semoga hamba selalu memilih jalan yang benar meskipun sulit. Xian You Yi De.",
                    verse: '"Junzi (manusia berbudi luhur) memahami kebenaran (Yi), Xiaoren (orang kecil) memahami keuntungan (Li)." — Lunyu 4:16',
                },
                {
                    id: 16,
                    title: "Doa Sebelum Belajar (Konghucu)",
                    category: "belajar",
                    source: "Lunyu 2:15",
                    text: "Ya Tian, hamba akan memulai belajar. Bukakanlah pikiran hamba agar mampu memahami ilmu dengan baik. Ajarlah hamba untuk belajar dan merenungkan, karena belajar tanpa berpikir itu sia-sia, dan berpikir tanpa belajar itu berbahaya. Xian You Yi De.",
                    verse: '"Belajar tanpa berpikir itu sia-sia, berpikir tanpa belajar itu berbahaya." — Lunyu 2:15',
                },
                {
                    id: 17,
                    title: "Doa Sesudah Belajar (Konghucu)",
                    category: "belajar",
                    source: "Lunyu 2:11",
                    text: "Terima kasih ya Tian, atas waktu belajar yang telah Engkau berikan. Semoga ilmu yang hamba pelajari bisa hamba amalkan dan bermanfaat bagi banyak orang. Ajarlah hamba untuk menghargai ilmu lama sambil terus mempelajari yang baru. Xian You Yi De.",
                    verse: '"Orang yang mengulang yang lama dan mengetahui yang baru, layak menjadi guru." — Lunyu 2:11',
                },
                {
                    id: 18,
                    title: "Doa Menghadapi Ujian (Konghucu)",
                    category: "belajar",
                    source: "Lunyu 9:29",
                    text: "Ya Tian, hamba akan menghadapi ujian. Berilah hamba ketenangan hati, kejernihan pikiran, dan daya ingat yang kuat. Hamba percaya bahwa orang yang memiliki kebijaksanaan tidak akan bimbang. Semoga hamba dapat menunjukkan hasil belajar yang terbaik. Xian You Yi De.",
                    verse: '"Orang yang memiliki kebijaksanaan tidak bimbang, orang yang memiliki Ren tidak cemas, orang yang berani tidak gentar." — Lunyu 9:29',
                },
                {
                    id: 19,
                    title: "Doa Menuntut Ilmu",
                    category: "belajar",
                    source: "Lunyu 15:30",
                    text: "Ya Tian, jadikanlah hamba orang yang tekun dalam menuntut ilmu. Hamba tidak ingin hanya berpikir tanpa belajar, karena itu tidak akan membawa hasil. Bimbinglah hamba untuk terus belajar dengan semangat dan rendah hati. Xian You Yi De.",
                    verse: '"Aku pernah seharian penuh berpikir tanpa makan dan semalam suntuk tanpa tidur, tetapi tidak ada hasilnya. Lebih baik belajar." — Lunyu 15:30',
                },
                {
                    id: 20,
                    title: "Doa untuk Orang Tua & Leluhur",
                    category: "keluarga",
                    source: "Lunyu 2:5",
                    text: "Ya Tian, hamba mendoakan kebaikan dan kesehatan untuk orang tua hamba. Ajarlah hamba untuk selalu berbakti (Xiao) kepada mereka, melayani mereka dengan penuh hormat semasa hidup, dan mengenang mereka dengan tulus. Semoga hamba menjadi anak yang membawa kebahagiaan bagi orang tua. Xian You Yi De.",
                    verse: '"Nabi bersabda tentang bakti: Selagi orang tua hidup, layanilah dengan Li (kesusilaan). Setelah meninggal, kuburkanlah dengan Li, dan kenangkanlah dengan Li." — Lunyu 2:5',
                },
                {
                    id: 21,
                    title: "Doa untuk Teman & Sahabat",
                    category: "keluarga",
                    source: "Lunyu 12:24",
                    text: "Ya Tian, terima kasih untuk sahabat-sahabat yang Engkau berikan kepada hamba. Ajarlah hamba untuk menjadi teman yang baik — yang menasihati dengan tulus dan membimbing dengan bijaksana. Jika mereka tidak mau mendengar, hamba akan berhenti agar tidak memalukan diri. Semoga persahabatan kami selalu berdasarkan kebenaran. Xian You Yi De.",
                    verse: '"Zengzi berkata: Seorang Junzi melalui sastra memperoleh teman, melalui teman memupuk Ren (cinta kasih)." — Lunyu 12:24',
                },
                {
                    id: 22,
                    title: "Doa untuk Guru & Sekolah",
                    category: "keluarga",
                    source: "Lunyu 7:2",
                    text: "Ya Tian, berkatilah guru-guru hamba yang dengan sabar mendidik dan mengajar. Ajarlah hamba untuk menghormati mereka dan mengamalkan ilmu yang mereka berikan. Semoga sekolah hamba menjadi tempat yang baik untuk menuntut ilmu dan membentuk budi pekerti. Xian You Yi De.",
                    verse: '"Diam-diam menghimpun ilmu, belajar tidak pernah merasa puas, mengajar orang lain tidak pernah merasa lelah — hal itu apakah ada padaku?" — Lunyu 7:2',
                },
                {
                    id: 23,
                    title: "Zhongyong (Jalan Tengah)",
                    category: "keharmonisan",
                    source: "Zhongyong 1-2",
                    text: "Ya Tian, bimbinglah hamba untuk menjalankan Zhongyong — Jalan Tengah yang harmonis. Ajarlah hamba untuk tidak berlebihan dan tidak kekurangan dalam segala hal. Semoga perasaan hamba selalu terjaga dalam keseimbangan dan keharmonisan. Xian You Yi De.",
                    verse: '"Suka, duka, marah, dan gembira yang belum timbul disebut Zhong (tengah). Sudah timbul dan semuanya tepat pada ukurannya disebut He (harmonis). Zhong adalah dasar besar bagi seluruh dunia, He adalah Dao besar bagi seluruh dunia." — Zhongyong 1',
                },
                {
                    id: 24,
                    title: "Doa Keharmonisan Hidup",
                    category: "keharmonisan",
                    source: "Lunyu 1:12",
                    text: "Ya Tian, jadikanlah kehidupan hamba harmonis dan damai. Ajarlah hamba untuk mengutamakan kerukunan dalam segala hubungan, namun tetap berpegang pada kebenaran. Semoga hamba mampu menciptakan suasana yang harmonis di mana pun hamba berada. Xian You Yi De.",
                    verse: '"Dalam melaksanakan Li (kesusilaan), keharmonisan adalah yang paling berharga." — Lunyu 1:12',
                },
                {
                    id: 25,
                    title: "Doa Pengendalian Diri",
                    category: "keharmonisan",
                    source: "Lunyu 12:1",
                    text: "Ya Tian, berilah hamba kemampuan untuk mengendalikan diri dan kembali kepada kesusilaan. Ajarlah hamba agar tidak melihat, tidak mendengar, tidak berkata, dan tidak melakukan sesuatu yang melanggar Li (kesusilaan). Xian You Yi De.",
                    verse: '"Yan Yuan bertanya tentang Ren. Nabi bersabda: Mengendalikan diri dan kembali pada Li (kesusilaan), itulah Ren." — Lunyu 12:1',
                },
                {
                    id: 26,
                    title: "Doa untuk Bangsa & Negara",
                    category: "umum",
                    source: "Da Xue (Ajaran Besar)",
                    text: "Ya Tian, hamba mendoakan kedamaian dan kemakmuran untuk bangsa Indonesia. Seperti ajaran Da Xue, semoga para pemimpin negeri ini memperbaiki diri, mengatur keluarga, mengelola negara, dan mendamaikan dunia. Berkatilah kami semua agar hidup dalam kerukunan. Xian You Yi De.",
                    verse: '"Memperbaiki diri, mengatur keluarga, mengelola negara, mendamaikan seluruh dunia." — Da Xue (Ajaran Besar)',
                },
                {
                    id: 27,
                    title: "Tepa Sarira (Shu — Tenggang Rasa)",
                    category: "umum",
                    source: "Lunyu 15:24",
                    text: "Ya Tian, ajarlah hamba untuk selalu menjalankan Shu (tenggang rasa). Apa yang hamba tidak mau orang lain perbuat terhadap hamba, maka hamba pun tidak akan memperbuatnya terhadap orang lain. Semoga prinsip Tepa Sarira ini selalu menjadi panduan hamba dalam bergaul. Xian You Yi De.",
                    verse: '"Zigong bertanya: Adakah satu kata yang dapat dijadikan pedoman sepanjang hidup? Nabi bersabda: Barangkali kata Shu (tenggang rasa). Apa yang tidak ingin diperbuat orang terhadap dirimu, janganlah engkau perbuat terhadap orang lain." — Lunyu 15:24',
                },
                {
                    id: 28,
                    title: "Doa Menjadi Junzi (Manusia Berbudi)",
                    category: "umum",
                    source: "Lunyu 6:30",
                    text: "Ya Tian, hamba bercita-cita menjadi Junzi — manusia berbudi luhur. Ajarlah hamba untuk selalu ingin menegakkan diri sendiri, maka hamba akan menegakkan orang lain. Jika hamba ingin berhasil, maka hamba akan membantu orang lain berhasil. Semoga hamba mampu meneladani sifat-sifat mulia Nabi Kongzi. Xian You Yi De.",
                    verse: '"Orang yang memiliki Ren (cinta kasih), jika dirinya ingin tegak maka ia menegakkan orang lain, jika dirinya ingin berhasil maka ia menunjukkan jalan bagi orang lain untuk berhasil." — Lunyu 6:30',
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
                {
                    id: "sembahyang",
                    label: "Sembahyang",
                    count: catMap["sembahyang"] || 0,
                },
                {
                    id: "kebajikan",
                    label: "Kebajikan",
                    count: catMap["kebajikan"] || 0,
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
                {
                    id: "keharmonisan",
                    label: "Keharmonisan",
                    count: catMap["keharmonisan"] || 0,
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
                sembahyang: "Sembahyang",
                kebajikan: "Kebajikan",
                belajar: "Belajar",
                keluarga: "Keluarga",
                keharmonisan: "Keharmonisan",
                umum: "Umum",
            };
            return map[catId] || catId;
        },
        holyVerses: [
            {
                text: "Belajar dan terus-menerus mengulang, bukankah itu menyenangkan? Ada kawan datang dari jauh, bukankah itu membahagiakan? Orang lain tidak mengenal, namun tidak merasa kecewa, bukankah itu seorang Junzi?",
                source: "Lunyu 1:1",
            },
            {
                text: "Pagi hari mendengar Dao, sore hari mati pun tiada penyesalan.",
                source: "Lunyu 4:8",
            },
            {
                text: "Apa yang tidak ingin diperbuat orang terhadap dirimu, janganlah engkau perbuat terhadap orang lain.",
                source: "Lunyu 15:24",
            },
            {
                text: "Orang yang memiliki kebijaksanaan tidak bimbang, orang yang memiliki Ren tidak cemas, orang yang berani tidak gentar.",
                source: "Lunyu 9:29",
            },
            {
                text: "Belajar tanpa berpikir itu sia-sia, berpikir tanpa belajar itu berbahaya.",
                source: "Lunyu 2:15",
            },
            {
                text: "Bila berjalan bertiga, pasti ada yang bisa menjadi guruku. Kupilih sifat baiknya untuk kuikuti, sifat buruknya untuk kuperbarui.",
                source: "Lunyu 7:22",
            },
            {
                text: "Junzi memahami kebenaran (Yi), Xiaoren memahami keuntungan (Li).",
                source: "Lunyu 4:16",
            },
            {
                text: "Seorang Junzi menuntut dari dirinya sendiri, seorang Xiaoren menuntut dari orang lain.",
                source: "Lunyu 15:21",
            },
            {
                text: "Orang yang memiliki Ren, jika dirinya ingin tegak maka ia menegakkan orang lain, jika dirinya ingin berhasil maka ia menunjukkan jalan bagi orang lain untuk berhasil.",
                source: "Lunyu 6:30",
            },
            {
                text: "Kodrat yang dianugerahkan Tian disebut Xing. Menjalankan watak sejati disebut Dao. Membina Dao disebut Jiao.",
                source: "Zhongyong 1",
            },
            {
                text: "Memperbaiki diri, mengatur keluarga, mengelola negara, mendamaikan seluruh dunia.",
                source: "Da Xue (Ajaran Besar)",
            },
            {
                text: "Orang yang mengulang yang lama dan mengetahui yang baru, layak menjadi guru.",
                source: "Lunyu 2:11",
            },
            {
                text: "Setiap hari aku memeriksa diriku dalam tiga hal: Apakah dalam melaksanakan tugas untuk orang lain aku sudah setia? Apakah dalam bergaul dengan teman aku sudah jujur? Apakah ajaran guru sudah kupraktikkan?",
                source: "Lunyu 1:4",
            },
            {
                text: "Dalam melaksanakan Li, keharmonisan adalah yang paling berharga.",
                source: "Lunyu 1:12",
            },
            {
                text: "Mengendalikan diri dan kembali pada Li (kesusilaan), itulah Ren.",
                source: "Lunyu 12:1",
            },
            {
                text: "Fan Chi bertanya tentang Ren. Nabi bersabda: Mengasihi sesama manusia. Bertanya tentang Zhi (kebijaksanaan). Nabi bersabda: Mengenal sesama manusia.",
                source: "Lunyu 12:22",
            },
            {
                text: "Suka, duka, marah, dan gembira yang belum timbul disebut Zhong. Sudah timbul dan semuanya tepat pada ukurannya disebut He. Zhong adalah dasar besar bagi dunia, He adalah Dao besar bagi dunia.",
                source: "Zhongyong 1",
            },
            {
                text: "Berhati-hatilah di saat akhir hayat dan kenangkanlah yang jauh sudah tiada, niscaya kebajikan rakyat akan tebal kembali.",
                source: "Lunyu 1:9",
            },
            {
                text: "Aku pernah seharian penuh berpikir tanpa makan dan semalam suntuk tanpa tidur, tetapi tidak ada hasilnya. Lebih baik belajar.",
                source: "Lunyu 15:30",
            },
            {
                text: "Orang yang bercita-cita pada Dao, berpegang pada De, bersandar pada Ren, dan bersenang-senang dalam seni.",
                source: "Lunyu 7:6",
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
            ApiRepository.auth
                .changePassword(self.pwOld, self.pwNew, self.pwConfirm)
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
                    if (ApiRepository.isThrottled(e)) return;
                    self.pwMessage = ApiRepository.isRateLimited(e)
                        ? e.message
                        : "Terjadi kesalahan. Coba lagi.";
                });
        },
    };
}
