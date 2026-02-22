// @ts-nocheck
/**
 * Buku Kegiatan Positif — Dashboard Buddha Alpine.js Component
 * Buddhist version: includes Calendar, Progress, Buddhist Prayers/Meditations, Dhammapada Verses.
 */

function buddhaDashboard() {
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
                this.motivationalBadge =
                    "Hari Minggu — Waktunya bermeditasi 🙏";
            } else {
                var badges = [
                    "Semangat berkegiatan positif!",
                    "Setiap hari adalah kesempatan baru",
                    "Tebarkan cinta kasih kepada semua makhluk",
                    "Teruslah bertumbuh dalam kebajikan",
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

        // ── Submitted Days ─────────────────────────────────────────────
        loadSubmittedDays() {
            try {
                var saved = localStorage.getItem("buddha_submitted_days");
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
                        data.submitted_days.forEach(function (day) {
                            if (!self.submittedDays.includes(day)) {
                                self.submittedDays.push(day);
                            }
                        });
                        localStorage.setItem(
                            "buddha_submitted_days",
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

        // ── Buddhist Prayers (Doa & Meditasi Buddha) ───────────────────
        loadDoas() {
            this.allDuas = [
                // === Doa Harian ===
                {
                    id: 1,
                    title: "Namo Tassa Bhagavato",
                    category: "harian",
                    source: "Vandana — Penghormatan",
                    text: "Namo Tassa Bhagavato Arahato Samma Sambuddhassa.\n\nTerpujilah Sang Bhagava, Yang Maha Suci, Yang telah mencapai Penerangan Sempurna.\n\nSemoga dengan penghormatan ini, pikiran kita menjadi tenang dan terbuka untuk menerima ajaran Dhamma yang mulia. Semoga semua makhluk hidup berbahagia.",
                    verse: '"Namo Tassa Bhagavato Arahato Samma Sambuddhassa" — Vandana (Penghormatan kepada Buddha)',
                },
                {
                    id: 2,
                    title: "Tisarana — Tiga Perlindungan",
                    category: "harian",
                    source: "Tisarana",
                    text: "Buddham Saranam Gacchami — Aku berlindung kepada Buddha.\nDhammam Saranam Gacchami — Aku berlindung kepada Dhamma.\nSangham Saranam Gacchami — Aku berlindung kepada Sangha.\n\nDengan berlindung kepada Tiratana (Tiga Permata), semoga aku selalu berada di jalan yang benar dan terbebas dari penderitaan.",
                    verse: '"Buddham Saranam Gacchami, Dhammam Saranam Gacchami, Sangham Saranam Gacchami" — Tisarana',
                },
                {
                    id: 3,
                    title: "Pancasila Buddhis",
                    category: "harian",
                    source: "Lima Sila",
                    text: "1. Panatipata veramani sikkhapadam samadiyami — Aku bertekad melatih diri menghindari pembunuhan makhluk hidup.\n2. Adinnadana veramani sikkhapadam samadiyami — Aku bertekad melatih diri menghindari mengambil barang yang tidak diberikan.\n3. Kamesu micchacara veramani sikkhapadam samadiyami — Aku bertekad melatih diri menghindari perbuatan asusila.\n4. Musavada veramani sikkhapadam samadiyami — Aku bertekad melatih diri menghindari ucapan yang tidak benar.\n5. Suramerayamajja pamadatthana veramani sikkhapadam samadiyami — Aku bertekad melatih diri menghindari minuman keras dan obat-obatan yang melemahkan kesadaran.",
                    verse: '"Sila adalah dasar dari semua kebaikan, akar dari Pohon Bodhi." — Ajaran Buddha',
                },
                {
                    id: 4,
                    title: "Doa Sebelum Makan (Buddhis)",
                    category: "harian",
                    source: "Doa Makan Buddhis",
                    text: "Dengan penuh kesadaran, aku menerima makanan ini.\nSemoga makanan ini menguatkan tubuhku untuk berbuat kebajikan.\nAku bersyukur kepada semua pihak yang telah menyiapkan makanan ini.\nSemoga semua makhluk hidup tercukupi kebutuhannya dan terbebas dari kelaparan.\nDengan penuh rasa syukur, aku makan dengan penuh kesadaran. Sadhu.",
                    verse: '"Makanan yang dimakan dengan kesadaran akan memberi manfaat bagi tubuh dan pikiran." — Ajaran Buddha',
                },
                {
                    id: 5,
                    title: "Doa Bangun Tidur (Buddhis)",
                    category: "harian",
                    source: "Doa Pagi Buddhis",
                    text: "Dengan terbangunnya hari baru ini, aku bertekad untuk menjalani hari ini dengan penuh kesadaran.\nSemoga aku dapat berbuat kebajikan, menghindari kejahatan, dan membersihkan pikiran.\nSemoga semua makhluk hidup berbahagia dan terbebas dari penderitaan.\nSadhu, sadhu, sadhu.",
                    verse: '"Bangunlah! Jangan lengah! Jalani kehidupan yang benar." — Dhammapada 168',
                },

                // === Doa Ucapan Syukur ===
                {
                    id: 6,
                    title: "Doa Syukur atas Kehidupan",
                    category: "syukur",
                    source: "Doa Buddhis",
                    text: "Dengan penuh rasa syukur, aku bersyukur telah terlahir sebagai manusia yang memiliki kesempatan untuk mendengar Dhamma.\nTerima kasih atas nikmat kehidupan, kesehatan, dan kesempatan untuk berbuat kebaikan.\nSemoga aku tidak menyia-nyiakan kelahiran yang berharga ini.\nSadhu, sadhu, sadhu.",
                    verse: '"Kelahiran sebagai manusia itu langka, sulit diperoleh; hidup makhluk itu terbatas; sungguh berbahagia mereka yang memperoleh kebijaksanaan." — Dhammapada 182',
                },
                {
                    id: 7,
                    title: "Doa Syukur atas Dhamma",
                    category: "syukur",
                    source: "Doa Buddhis",
                    text: "Terima kasih Sang Buddha yang telah mengajarkan Dhamma kepada dunia.\nTerima kasih para Sangha yang telah menjaga dan menyebarkan ajaran ini.\nSemoga Dhamma tetap bersinar terang di dunia ini untuk kesejahteraan semua makhluk.\nSemoga aku selalu dapat mempraktikkan Dhamma dalam kehidupan sehari-hari.\nSadhu.",
                    verse: '"Dhamma yang Sang Bhagava ajarkan itu baik pada awalnya, baik pada pertengahannya, dan baik pada akhirnya." — Svakkhato',
                },
                {
                    id: 8,
                    title: "Doa Syukur Pagi Hari",
                    category: "syukur",
                    source: "Doa Pagi Buddhis",
                    text: "Hari ini adalah anugerah yang berharga.\nDengan penuh rasa syukur aku menyambut pagi ini.\nSemoga aku menggunakan setiap momen dengan bijaksana.\nSemoga kebaikan menghiasi setiap langkahku hari ini.\nSemoga semua makhluk berbahagia. Sadhu.",
                    verse: '"Jangan meremehkan kebaikan kecil, dengan berpikir: itu tidak akan berefek padaku. Tetes air yang jatuh pun akhirnya memenuhi tempayan." — Dhammapada 122',
                },

                // === Doa Meditasi ===
                {
                    id: 9,
                    title: "Metta Bhavana — Meditasi Cinta Kasih",
                    category: "meditasi",
                    source: "Metta Bhavana",
                    text: "Semoga aku berbahagia, sehat, dan damai.\nSemoga orang-orang yang aku cintai berbahagia, sehat, dan damai.\nSemoga semua orang yang aku kenal berbahagia, sehat, dan damai.\nSemoga semua makhluk di seluruh alam semesta berbahagia, sehat, dan damai.\nSemoga semua makhluk terbebas dari penderitaan dan sebab-sebab penderitaan.\nSemoga semua makhluk menemukan kebahagiaan dan sebab-sebab kebahagiaan.\nSadhu, sadhu, sadhu.",
                    verse: '"Sabbe satta bhavantu sukhitatta — Semoga semua makhluk berbahagia." — Karaniya Metta Sutta',
                },
                {
                    id: 10,
                    title: "Meditasi Anapanasati — Kesadaran Napas",
                    category: "meditasi",
                    source: "Anapanasati Sutta",
                    text: "Duduklah dengan tenang dan penuh kesadaran.\nPerhatikan napas masuk, perhatikan napas keluar.\nSaat menarik napas panjang, sadari: aku menarik napas panjang.\nSaat menghembuskan napas panjang, sadari: aku menghembuskan napas panjang.\nTenangkan pikiran, tenangkan tubuh.\nDengan setiap tarikan napas, lepaskan ketegangan.\nDengan setiap hembusan napas, lepaskan kekhawatiran.\nSemoga pikiran menjadi jernih seperti air yang tenang.",
                    verse: '"Dengan penuh perhatian ia menarik napas, dengan penuh perhatian ia menghembuskan napas." — Anapanasati Sutta (MN 118)',
                },
                {
                    id: 11,
                    title: "Meditasi Karuna — Welas Asih",
                    category: "meditasi",
                    source: "Empat Brahma Vihara",
                    text: "Semoga semua makhluk yang menderita terbebas dari penderitaan.\nSemoga mereka yang sakit mendapat kesembuhan.\nSemoga mereka yang berduka mendapat penghiburan.\nSemoga mereka yang takut mendapat keberanian.\nDengan hati yang penuh welas asih, aku mengirimkan cinta kasih kepada semua makhluk.\nSemoga aku mampu menolong mereka yang membutuhkan.\nSadhu.",
                    verse: '"Welas asih (Karuna) adalah keinginan agar semua makhluk terbebas dari penderitaan." — Visuddhimagga IX',
                },
                {
                    id: 12,
                    title: "Meditasi Mudita — Kegembiraan Simpatik",
                    category: "meditasi",
                    source: "Empat Brahma Vihara",
                    text: "Semoga aku dapat ikut bergembira atas kebahagiaan dan keberhasilan orang lain.\nSemoga aku terbebas dari iri hati dan cemburu.\nSemoga kegembiraan orang lain menjadi sumber inspirasi bagiku.\nSemoga kebaikan terus bertambah di dunia ini.\nDengan hati yang lapang, aku turut bersukacita. Sadhu.",
                    verse: '"Mudita adalah turut bergembira atas kebahagiaan dan keberhasilan orang lain tanpa iri hati." — Brahma Vihara',
                },

                // === Paritta ===
                {
                    id: 13,
                    title: "Karaniya Metta Sutta",
                    category: "paritta",
                    source: "Sutta Nipata 1.8",
                    text: "Inilah yang harus dilakukan oleh mereka yang terampil dalam kebajikan dan menginginkan kedamaian:\nHendaklah ia cakap, jujur, dan lurus, mudah dinasihati, lembut, dan tidak sombong.\nPuas hati dan mudah dipelihara, tidak sibuk, hidup sederhana.\nTenang indriyanya, bijaksana, tidak kasar, dan tidak serakah.\nApapun yang menyebabkan para bijaksana mencela, janganlah ia lakukan.\nSemoga semua makhluk berbahagia dan aman, semoga hati mereka penuh sukacita.",
                    verse: '"Karaniyam attha-kusalena, yam tam santam padam abhisamecca." — Karaniya Metta Sutta (Sn 1.8)',
                },
                {
                    id: 14,
                    title: "Mangala Sutta — Berkah Tertinggi",
                    category: "paritta",
                    source: "Sutta Nipata 2.4",
                    text: "Tidak bergaul dengan orang bodoh, bergaul dengan orang bijaksana, menghormati mereka yang patut dihormati — inilah berkah tertinggi.\nTinggal di tempat yang sesuai, memiliki jasa di masa lalu, mengarahkan diri sendiri dengan benar — inilah berkah tertinggi.\nBelajar banyak, terampil dalam pekerjaan, terlatih dalam disiplin, dan berkata-kata yang menyenangkan — inilah berkah tertinggi.\nMerawat orang tua, menghargai pasangan dan anak, pekerjaan yang damai — inilah berkah tertinggi.",
                    verse: '"Etam mangalam uttamam — Inilah berkah yang tertinggi." — Mangala Sutta (Sn 2.4)',
                },
                {
                    id: 15,
                    title: "Ratana Sutta — Sutta Permata",
                    category: "paritta",
                    source: "Sutta Nipata 2.1",
                    text: "Apapun permata yang ada, baik di sini maupun di alam lain, tidak ada yang menandingi Sang Tathagata. Permata yang mulia ini ada pada Buddha. Dengan kebenaran ini, semoga ada kesejahteraan.\nDhamma yang tanpa noda, tanpa nafsu, tanpa gangguan, yang Sang Mulia temukan — tidak ada yang menandinginya. Permata mulia ini ada pada Dhamma. Dengan kebenaran ini, semoga ada kesejahteraan.\nSangha para siswa yang terpuji, berjalan lurus, menjalani dengan benar — mereka layak menerima persembahan. Permata mulia ini ada pada Sangha.",
                    verse: '"Yam kinci vittam — Apapun harta yang ada di dunia ini atau di surga, tidak ada yang setara dengan Tathagata." — Ratana Sutta',
                },

                // === Doa Belajar ===
                {
                    id: 16,
                    title: "Doa Sebelum Belajar (Buddhis)",
                    category: "belajar",
                    source: "Doa Buddhis",
                    text: "Namo Buddhaya.\nDengan pikiran yang jernih dan terbuka, aku siap untuk belajar.\nSemoga ilmu yang kupelajari membawa kebijaksanaan.\nSemoga pengetahuan ini berguna untuk kebaikan diriku dan semua makhluk.\nSemoga aku tekun, sabar, dan penuh perhatian dalam belajar.\nSadhu, sadhu, sadhu.",
                    verse: '"Orang bijaksana yang rajin belajar dan mempraktikkan apa yang dipelajari, akan selalu dihormati oleh para bijak." — Dhammapada 79',
                },
                {
                    id: 17,
                    title: "Doa Sesudah Belajar (Buddhis)",
                    category: "belajar",
                    source: "Doa Buddhis",
                    text: "Terima kasih atas kesempatan belajar hari ini.\nSemoga ilmu yang telah kupelajari tertanam dalam ingatan dengan baik.\nSemoga pengetahuan ini menjadi bekal untuk berbuat kebajikan.\nSemoga aku dapat membagikan kebijaksanaan ini kepada yang membutuhkan.\nSadhu.",
                    verse: '"Belajarlah dengan sungguh-sungguh, karena hidup ini singkat dan pengetahuan luas tak terbatas." — Nasihat Buddha',
                },
                {
                    id: 18,
                    title: "Doa Menghadapi Ujian (Buddhis)",
                    category: "belajar",
                    source: "Doa Buddhis",
                    text: "Namo Buddhaya.\nDengan pikiran yang tenang dan jernih, aku akan menghadapi ujian ini.\nSemoga aku dapat mengingat apa yang telah kupelajari.\nSemoga kebijaksanaan menyertai setiap jawabanku.\nAku menghadapi ujian ini dengan ketenangan dan kejujuran.\nSadhu.",
                    verse: '"Dengan ketekunan, kesadaran, disiplin, dan pengendalian diri, orang bijaksana membangun pulau yang tidak dapat ditenggelamkan banjir." — Dhammapada 25',
                },

                // === Doa Keluarga ===
                {
                    id: 19,
                    title: "Doa untuk Orang Tua (Buddhis)",
                    category: "keluarga",
                    source: "Sigalovada Sutta",
                    text: "Semoga ayah dan ibu selalu sehat, bahagia, dan damai.\nTerima kasih atas kasih sayang, pengorbanan, dan didikan yang telah diberikan.\nSemoga aku dapat membalas budi orang tua dengan berbakti dan menghormati mereka.\nSemoga mereka selalu dilindungi dan diberkahi dalam kehidupan.\nSemoga pahala kebajikan ini aku persembahkan untuk orang tua tercinta.\nSadhu, sadhu, sadhu.",
                    verse: '"Merawat ibu dan ayah, menghargai pasangan dan anak, pekerjaan yang damai — inilah berkah yang tertinggi." — Mangala Sutta',
                },
                {
                    id: 20,
                    title: "Doa untuk Guru (Buddhis)",
                    category: "keluarga",
                    source: "Sigalovada Sutta",
                    text: "Semoga para guru yang telah dengan sabar mendidik kami selalu diberkahi.\nSemoga mereka sehat, bahagia, dan sejahtera.\nAku menghormati guru-guruku sebagaimana murid menghormati Acariya.\nSemoga ilmu yang mereka ajarkan menjadi berkat bagi semua.\nSadhu.",
                    verse: '"Seorang murid menghormati guru dengan bangun menyambut, melayani, belajar tekun, dan mendengarkan dengan baik." — Sigalovada Sutta (DN 31)',
                },
                {
                    id: 21,
                    title: "Doa untuk Teman & Sahabat (Buddhis)",
                    category: "keluarga",
                    source: "Doa Buddhis",
                    text: "Semoga teman-temanku selalu berbahagia dan terbebas dari penderitaan.\nSemoga persahabatan kami dilandasi cinta kasih, saling menghormati, dan saling mendukung.\nSemoga kami bersama-sama bertumbuh dalam kebajikan.\nSemoga kami menjadi sahabat sejati yang saling mengingatkan pada kebaikan.\nSadhu.",
                    verse: '"Sahabat sejati adalah mereka yang menasihati dalam kebaikan dan melindungi di saat kesulitan." — Sigalovada Sutta',
                },

                // === Doa Kebajikan ===
                {
                    id: 22,
                    title: "Doa Memohon Kesabaran",
                    category: "kebajikan",
                    source: "Khanti Paramita",
                    text: "Semoga aku memiliki kesabaran yang teguh dalam menghadapi segala cobaan.\nSemoga aku tidak mudah marah dan dapat mengendalikan emosi.\nKesabaran adalah kebajikan tertinggi — Khanti paramam tapo titikha.\nSemoga aku mampu bersabar dengan penuh kesadaran dan kebijaksanaan.\nSadhu.",
                    verse: '"Khanti paramam tapo titikha — Kesabaran adalah laku tapa yang tertinggi." — Dhammapada 184',
                },
                {
                    id: 23,
                    title: "Doa Melepaskan Kemarahan",
                    category: "kebajikan",
                    source: "Ajaran Buddha",
                    text: "Dengan kesadaran, aku melepaskan kemarahan dalam hatiku.\nKemarahan hanya melukai diri sendiri seperti memegang bara api.\nSemoga aku mampu memaafkan dan melepaskan dendam.\nSemoga hatiku dipenuhi cinta kasih menggantikan kemarahan.\nSemoga semua makhluk terbebas dari kebencian.\nSadhu.",
                    verse: '"Kebencian tidak pernah berhenti dengan kebencian. Kebencian hanya berhenti dengan cinta kasih. Inilah hukum abadi." — Dhammapada 5',
                },
                {
                    id: 24,
                    title: "Doa Dana Paramita — Kemurahan Hati",
                    category: "kebajikan",
                    source: "Dana Paramita",
                    text: "Semoga aku memiliki hati yang murah hati.\nSemoga aku dapat berbagi dengan sesama tanpa mengharapkan balasan.\nDana yang diberikan dengan tulus akan menghasilkan pahala yang besar.\nSemoga kebajikan dana ini aku bagikan kepada semua makhluk.\nSemoga semua makhluk terbebas dari kemiskinan dan keserakahan.\nSadhu, sadhu, sadhu.",
                    verse: '"Orang bijaksana yang memberi dengan iman, dengan hati yang tenang — makanannya berlimpah di dunia ini dan di dunia berikutnya." — Itivuttaka 26',
                },
                {
                    id: 25,
                    title: "Doa Sila Paramita — Moralitas",
                    category: "kebajikan",
                    source: "Sila Paramita",
                    text: "Semoga aku selalu menjaga sila dengan baik.\nSemoga tindakanku, ucapanku, dan pikiranku senantiasa bersih.\nMoralitas adalah fondasi dari segala kebajikan.\nSemoga aku menjadi contoh kebaikan bagi orang di sekitarku.\nSadhu.",
                    verse: '"Sila (moralitas) adalah wangi yang tercium ke segala arah, tidak terhalang oleh angin." — Dhammapada 54-56',
                },

                // === Doa Umum ===
                {
                    id: 26,
                    title: "Doa Pattidana — Transfer Jasa",
                    category: "umum",
                    source: "Pattidana",
                    text: "Semoga pahala kebajikan yang telah aku lakukan ini aku bagikan kepada semua makhluk.\nSemoga para leluhur, sanak keluarga yang telah meninggal, dan semua makhluk di alam lain dapat menerima pahala ini.\nSemoga mereka berbahagia dan terbebas dari penderitaan.\nIdang me natinam hotu, sukhita hontu natayo.\nSadhu, sadhu, sadhu.",
                    verse: '"Idang me natinam hotu, sukhita hontu natayo — Semoga pahala ini untuk saudara-saudaraku, semoga mereka berbahagia." — Pattidana',
                },
                {
                    id: 27,
                    title: "Doa untuk Bangsa & Negara",
                    category: "umum",
                    source: "Doa Buddhis",
                    text: "Semoga bangsa dan negara Indonesia selalu damai, aman, dan sejahtera.\nSemoga para pemimpin memiliki kebijaksanaan dan keadilan.\nSemoga rakyat hidup dalam keharmonisan meskipun berbeda suku, agama, dan budaya.\nSemoga Dhamma kedamaian menyinari negeri ini.\nSadhu, sadhu, sadhu.",
                    verse: '"Ketika penguasa suatu negara bersikap adil dan bijaksana, negara itu akan makmur dan rakyatnya sejahtera." — Cakkavatti Sihanada Sutta',
                },
                {
                    id: 28,
                    title: "Doa Sebelum Tidur (Buddhis)",
                    category: "umum",
                    source: "Doa Malam Buddhis",
                    text: "Hari ini telah berlalu. Aku merenungkan apa yang telah kulakukan.\nKebajikan yang telah kulakukan, semoga pahalanya kubagikan kepada semua makhluk.\nKesalahan yang telah kulakukan, semoga aku dapat memperbaikinya esok hari.\nDengan pikiran yang tenang dan damai, aku melepaskan hari ini.\nSemoga tidurku nyenyak dan esok aku terbangun dengan semangat baru.\nNamo Buddhaya. Sadhu.",
                    verse: '"Jangan mengejar masa lalu, jangan berharap berlebihan pada masa depan. Jalani saat ini dengan penuh kesadaran." — Bhaddekaratta Sutta (MN 131)',
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
                    id: "meditasi",
                    label: "Meditasi",
                    count: catMap["meditasi"] || 0,
                },
                {
                    id: "paritta",
                    label: "Paritta",
                    count: catMap["paritta"] || 0,
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
                    id: "kebajikan",
                    label: "Kebajikan",
                    count: catMap["kebajikan"] || 0,
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
                meditasi: "Meditasi",
                paritta: "Paritta",
                belajar: "Belajar",
                keluarga: "Keluarga",
                kebajikan: "Kebajikan",
                umum: "Umum",
            };
            return map[catId] || catId;
        },

        // ── Dhammapada / Sutta Verses ──────────────────────────────────
        holyVerses: [
            {
                text: "Pikiran adalah pelopor dari segala perbuatan. Pikiran adalah pemimpin, pikiran adalah pembentuk. Jika seseorang berbicara atau bertindak dengan pikiran jahat, maka penderitaan akan mengikutinya bagaikan roda pedati mengikuti langkah kaki lembu yang menariknya.",
                source: "Dhammapada 1:1",
            },
            {
                text: "Pikiran adalah pelopor dari segala perbuatan. Pikiran adalah pemimpin, pikiran adalah pembentuk. Jika seseorang berbicara atau bertindak dengan pikiran murni, maka kebahagiaan akan mengikutinya bagaikan bayangan yang tak pernah meninggalkannya.",
                source: "Dhammapada 1:2",
            },
            {
                text: "Kebencian tidak pernah berhenti dengan kebencian di dunia ini. Kebencian hanya berhenti dengan cinta kasih. Inilah hukum abadi.",
                source: "Dhammapada 1:5",
            },
            {
                text: "Seperti hujan menembus rumah yang atapnya bocor, demikian pula nafsu keinginan menembus pikiran yang tidak terlatih dalam meditasi.",
                source: "Dhammapada 1:13",
            },
            {
                text: "Tidak lengah adalah jalan menuju keabadian. Kelengahan adalah jalan menuju kematian. Mereka yang tidak lengah tidak akan mati. Mereka yang lengah seakan-akan sudah mati.",
                source: "Dhammapada 2:21",
            },
            {
                text: "Dengan ketekunan, kesadaran, disiplin, dan pengendalian diri, orang bijaksana membangun pulau yang tidak dapat ditenggelamkan oleh banjir.",
                source: "Dhammapada 2:25",
            },
            {
                text: "Pikiran itu sulit dikendalikan, bergerak lincah, mendarat di mana saja ia suka. Mengendalikan pikiran adalah baik. Pikiran yang terkendali membawa kebahagiaan.",
                source: "Dhammapada 3:35",
            },
            {
                text: "Barangsiapa menang atas dirinya sendiri, itulah kemenangan yang paling mulia daripada mengalahkan seribu orang di medan perang.",
                source: "Dhammapada 8:103",
            },
            {
                text: "Lebih baik menaklukkan diri sendiri daripada menaklukkan orang lain. Kemenangan atas diri sendiri tidak dapat direbut kembali oleh dewa, gandhabba, Mara, ataupun Brahma.",
                source: "Dhammapada 8:104-105",
            },
            {
                text: "Jangan meremehkan kebaikan kecil, dengan berpikir: itu tidak akan berefek padaku. Tetes air yang jatuh secara terus-menerus pun akhirnya memenuhi tempayan. Demikian pula orang bijaksana menghimpun kebaikan sedikit demi sedikit.",
                source: "Dhammapada 9:122",
            },
            {
                text: "Seperti seorang saudagar yang membawa banyak harta dengan pengawal sedikit menghindari jalan yang berbahaya, seperti orang yang mencintai hidupnya menghindari racun, demikian pula hendaknya orang menghindari kejahatan.",
                source: "Dhammapada 9:123",
            },
            {
                text: "Semua makhluk gentar terhadap hukuman; semua makhluk takut akan kematian. Dengan membandingkan diri sendiri dengan orang lain, janganlah membunuh atau menyebabkan pembunuhan.",
                source: "Dhammapada 10:129",
            },
            {
                text: "Seseorang bukanlah orang bijaksana hanya karena banyak berbicara. Orang yang damai, tanpa permusuhan, dan tanpa rasa takut layak disebut orang bijaksana.",
                source: "Dhammapada 19:258",
            },
            {
                text: "Kesabaran dan pemaafan adalah laku tapa yang tertinggi. Nibbana adalah yang tertinggi, demikian kata para Buddha. Seseorang bukanlah petapa jika ia menyakiti orang lain.",
                source: "Dhammapada 14:184",
            },
            {
                text: "Kesehatan adalah anugerah tertinggi, kepuasan adalah kekayaan tertinggi, kepercayaan adalah kerabat tertinggi, Nibbana adalah kebahagiaan tertinggi.",
                source: "Dhammapada 15:204",
            },
            {
                text: "Bangunlah! Jangan lengah! Jalani kehidupan Dhamma dengan benar. Orang yang menjalani Dhamma akan hidup bahagia di dunia ini maupun di dunia berikutnya.",
                source: "Dhammapada 13:168",
            },
            {
                text: "Jadikanlah dirimu sendiri pelindung bagi dirimu. Diri sendiri adalah tempat bergantung. Oleh karena itu, kendalikanlah dirimu sendiri seperti saudagar mengendalikan kuda yang baik.",
                source: "Dhammapada 25:380",
            },
            {
                text: "Gantungkanlah dirimu pada apa yang benar dan baik; itu akan membawamu pada damai, bahagia, dan penuh cinta kasih.",
                source: "Dhammapada 6:79",
            },
            {
                text: "Orang yang bijaksana tidak menganggap dirinya lebih tinggi, lebih rendah, atau sama dengan orang lain. Ia tidak terpengaruh oleh pujian ataupun celaan.",
                source: "Sutta Nipata 4:11",
            },
            {
                text: "Di dunia ini, kebencian tidak pernah memadamkan kebencian. Hanya cinta kasih yang memadamkan kebencian. Ini adalah kebenaran abadi yang tidak pernah berubah.",
                source: "Dhammapada 1:5",
            },
        ],

        setDailyVerse() {
            // Pick verse based on day number
            var idx = (this.ramadhanDay - 1) % this.holyVerses.length;
            this.dailyVerse = this.holyVerses[idx];
        },

        refreshVerse() {
            var idx = Math.floor(Math.random() * this.holyVerses.length);
            this.dailyVerse = this.holyVerses[idx];
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
