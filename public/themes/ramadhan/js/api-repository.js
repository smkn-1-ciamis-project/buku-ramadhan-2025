/**
 * ══════════════════════════════════════════════════════════════════════
 * ApiRepository — Centralized API abstraction layer (Repository Pattern)
 * ══════════════════════════════════════════════════════════════════════
 *
 * Senior-level Separation of Concerns:
 *   UI → ApiRepository → Server API
 *
 * UI code NEVER calls fetch() or hardcoded URLs directly.
 * All API calls go through this single repository.
 *
 * Features:
 *   - Client-side throttle (per-key cooldown)
 *   - Server-side 429 rate-limit handling
 *   - CSRF token injection for POST requests
 *   - Consistent error/response formatting
 *   - Single source of truth for all API endpoints
 *
 * Usage:
 *   ApiRepository.formulir.getAll()
 *   ApiRepository.formulir.getByDay(5)
 *   ApiRepository.formulir.submit(dayNum, formData)
 *   ApiRepository.prayerCheckins.getFirstUnfilled()
 *   ApiRepository.prayerCheckins.getByDate('2026-02-20')
 *   ApiRepository.prayerCheckins.submit({ shalat, status, tanggal })
 *   ApiRepository.formSettings.get('Islam')
 *   ApiRepository.auth.changePassword(oldPw, newPw, confirmPw)
 * ══════════════════════════════════════════════════════════════════════
 */
var ApiRepository = (function () {
    "use strict";

    // ── Throttle State ──────────────────────────────────────────────
    var _lastCallTimestamps = {};

    // ── Config ──────────────────────────────────────────────────────
    var COOLDOWN_READ = 10000; // 10s for GET requests
    var COOLDOWN_WRITE = 3000; // 3s  for POST requests

    // ── Private Helpers ─────────────────────────────────────────────

    /**
     * Get CSRF token from <meta name="csrf-token">.
     */
    function _getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute("content") : "";
    }

    /**
     * Throttled fetch — prevents rapid duplicate calls.
     *
     * @param {string}  key        Unique throttle key
     * @param {string}  url        Request URL
     * @param {object}  options    fetch() options
     * @param {number}  cooldownMs Minimum interval between calls (ms)
     * @returns {Promise<Response>}
     */
    function _throttledFetch(key, url, options, cooldownMs) {
        cooldownMs = cooldownMs || COOLDOWN_READ;
        var now = Date.now();

        if (
            _lastCallTimestamps[key] &&
            now - _lastCallTimestamps[key] < cooldownMs
        ) {
            return Promise.reject({ throttled: true });
        }

        _lastCallTimestamps[key] = now;

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

    /**
     * Build headers for GET requests.
     */
    function _readHeaders() {
        return { Accept: "application/json" };
    }

    /**
     * Build headers for POST requests (with CSRF).
     */
    function _writeHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": _getCsrfToken(),
        };
    }

    /**
     * Perform a throttled GET request.
     *
     * @param {string} key       Throttle key
     * @param {string} url       Endpoint URL
     * @param {number} cooldown  Optional cooldown override
     * @returns {Promise<Response>}
     */
    function _get(key, url, cooldown) {
        return _throttledFetch(
            key,
            url,
            {
                headers: _readHeaders(),
            },
            cooldown || COOLDOWN_READ,
        );
    }

    /**
     * Perform a throttled POST request.
     *
     * @param {string} key       Throttle key
     * @param {string} url       Endpoint URL
     * @param {object} body      Request body (will be JSON.stringify'd)
     * @param {number} cooldown  Optional cooldown override
     * @returns {Promise<Response>}
     */
    function _post(key, url, body, cooldown) {
        return _throttledFetch(
            key,
            url,
            {
                method: "POST",
                headers: _writeHeaders(),
                body: JSON.stringify(body),
            },
            cooldown || COOLDOWN_WRITE,
        );
    }

    // ── Public API ──────────────────────────────────────────────────

    return {
        /**
         * Formulir Harian (Form Submission)
         */
        formulir: {
            /**
             * GET /api/formulir — Get all user submissions.
             * @returns {Promise<Response>}
             */
            getAll: function () {
                return _get("formulir_getAll", "/api/formulir");
            },

            /**
             * GET /api/formulir/{hariKe} — Get submission for specific day.
             * @param {number} hariKe
             * @returns {Promise<Response>}
             */
            getByDay: function (hariKe) {
                return _get(
                    "formulir_day_" + hariKe,
                    "/api/formulir/" + hariKe,
                );
            },

            /**
             * POST /api/formulir — Submit/update daily form.
             * @param {number} hariKe
             * @param {object} formData
             * @returns {Promise<Response>}
             */
            submit: function (hariKe, formData) {
                return _post("formulir_submit", "/api/formulir", {
                    hari_ke: hariKe,
                    data: formData,
                });
            },
        },

        /**
         * Prayer Check-in (Shalat)
         */
        prayerCheckins: {
            /**
             * GET /api/prayer-checkins/first-unfilled
             * @returns {Promise<Response>}
             */
            getFirstUnfilled: function () {
                return _get(
                    "prayer_firstUnfilled",
                    "/api/prayer-checkins/first-unfilled",
                );
            },

            /**
             * GET /api/prayer-checkins/date/{date}
             * @param {string} date  e.g. '2026-02-20'
             * @param {number} cooldown  Optional cooldown override
             * @returns {Promise<Response>}
             */
            getByDate: function (date, cooldown) {
                return _get(
                    "prayer_date_" + date,
                    "/api/prayer-checkins/date/" + date,
                    cooldown,
                );
            },

            /**
             * GET /api/prayer-checkins/today
             * @returns {Promise<Response>}
             */
            getToday: function () {
                return _get("prayer_today", "/api/prayer-checkins/today");
            },

            /**
             * POST /api/prayer-checkins — Submit a prayer check-in.
             * @param {object} payload  { shalat, status, tanggal? }
             * @returns {Promise<Response>}
             */
            submit: function (payload) {
                return _post("prayer_submit", "/api/prayer-checkins", payload);
            },
        },

        /**
         * Form Settings (dynamic form config per religion)
         */
        formSettings: {
            /**
             * GET /api/form-settings/{agama}
             * @param {string} agama  e.g. 'Islam', 'Kristen', 'Hindu'
             * @returns {Promise<Response>}
             */
            get: function (agama) {
                return _get(
                    "formSettings_" + agama,
                    "/api/form-settings/" + agama,
                );
            },
        },

        /**
         * Authentication
         */
        auth: {
            /**
             * POST /api/change-password
             * @param {string} currentPassword
             * @param {string} newPassword
             * @param {string} confirmPassword
             * @returns {Promise<Response>}
             */
            changePassword: function (
                currentPassword,
                newPassword,
                confirmPassword,
            ) {
                return _post("auth_changePw", "/api/change-password", {
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword,
                });
            },
        },

        /**
         * App Settings — dynamic configuration from Superadmin
         */
        appSettings: {
            /**
             * GET /api/app-settings
             * Returns Ramadhan schedule, API URLs, default location, etc.
             * @returns {Promise<Response>}
             */
            get: function () {
                return _get("appSettings", "/api/app-settings");
            },
        },

        /**
         * Utility: check if an error is a client-side throttle rejection.
         * @param {*} err
         * @returns {boolean}
         */
        isThrottled: function (err) {
            return err && err.throttled === true;
        },

        /**
         * Utility: check if an error is a server-side rate-limit (429).
         * @param {*} err
         * @returns {boolean}
         */
        isRateLimited: function (err) {
            return err && err.rateLimited === true;
        },

        /**
         * Utility: standard error handler for API calls.
         * Silently ignores throttled; logs rate-limited message.
         *
         * @param {*} err
         * @param {string} context  Description for console warning
         * @returns {boolean}  true if error was handled (throttle/rateLimit)
         */
        handleError: function (err, context) {
            if (this.isThrottled(err)) return true;
            if (this.isRateLimited(err)) {
                console.warn(
                    "[ApiRepository] Rate limited (" + context + "):",
                    err.message,
                );
                return true;
            }
            return false;
        },
    };
})();
