{{-- AI Chat Widget — religion-specific assistant (blue theme) --}}
@auth
@php
    $user = auth()->user();
    $agama = strtolower(trim($user->agama ?? 'islam'));

    // Detect agama from URL for superadmin viewing other dashboards
    $path = request()->path();
    if (str_contains($path, 'home-kristen')) $agama = 'kristen';
    elseif (str_contains($path, 'home-hindu')) $agama = 'hindu';
    elseif (str_contains($path, 'home-buddha')) $agama = 'buddha';
    elseif (str_contains($path, 'home-konghucu')) $agama = 'konghucu';

    $aiConfigs = [
        'islam'    => ['name' => 'Ustadz AI',  'initials' => 'UA', 'welcome' => 'Assalamualaikum! Saya Ustadz AI, siap membantu pertanyaan seputar Islam, Al-Quran, dan Hadist.'],
        'kristen'  => ['name' => 'Pastor AI',  'initials' => 'PA', 'welcome' => 'Puji Tuhan! Saya Pastor AI, siap membantu pertanyaan seputar iman Kristen dan Alkitab.'],
        'katolik'  => ['name' => 'Romo AI',    'initials' => 'RA', 'welcome' => 'Terpujilah Tuhan! Saya Romo AI, siap membantu pertanyaan seputar iman Katolik dan ajaran Gereja.'],
        'hindu'    => ['name' => 'Pandit AI',  'initials' => 'PA', 'welcome' => 'Om Swastiastu! Saya Pandit AI, siap membantu pertanyaan seputar Hindu Dharma.'],
        'buddha'   => ['name' => 'Bhikkhu AI', 'initials' => 'BA', 'welcome' => 'Namo Buddhaya! Saya Bhikkhu AI, siap membantu pertanyaan seputar ajaran Buddha.'],
        'konghucu' => ['name' => 'Wenshi AI',  'initials' => 'WA', 'welcome' => 'Wei De Dong Tian! Saya Wenshi AI, siap membantu pertanyaan seputar ajaran Konghucu.'],
    ];

    if (in_array($agama, ['muslim'])) $agama = 'islam';
    if (in_array($agama, ['protestan'])) $agama = 'kristen';
    if (in_array($agama, ['budha'])) $agama = 'buddha';
    if (in_array($agama, ['khonghucu'])) $agama = 'konghucu';

    $ai = $aiConfigs[$agama] ?? $aiConfigs['islam'];
    $ai['religion'] = $agama;
    $ai['color'] = '#1d4ed8';
    $ai['bg'] = '#dbeafe';
@endphp

{{-- Floating Action Button --}}
<button id="ai-fab" onclick="toggleAiChat()" aria-label="Buka AI Chat">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    <svg class="ai-fab-sparkle" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l2.5 9.5L24 12l-9.5 2.5L12 24l-2.5-9.5L0 12l9.5-2.5z"/></svg>
</button>

{{-- Chat Panel --}}
<div id="ai-chat-panel">
    {{-- Header --}}
    <div class="ai-chat-header">
        <div class="ai-chat-header-info">
            <div class="ai-chat-avatar">{{ $ai['initials'] }}</div>
            <div>
                <div class="ai-chat-name">{{ $ai['name'] }}</div>
                <div class="ai-chat-status">Asisten Keagamaan</div>
            </div>
        </div>
        <div class="ai-chat-header-actions">
            <span id="ai-quota-badge" class="ai-quota-badge" title="Sisa pertanyaan hari ini">20/20</span>
            <button class="ai-chat-header-btn" onclick="clearAiHistory()" title="Hapus Riwayat">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
            <button class="ai-chat-header-btn" onclick="toggleAiChat()" aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    {{-- Messages --}}
    <div class="ai-chat-messages" id="ai-chat-messages">
        <div class="ai-msg">
            <div class="ai-msg-avatar">{{ $ai['initials'] }}</div>
            <div class="ai-msg-bubble">{{ $ai['welcome'] }}</div>
        </div>
    </div>

    {{-- Input --}}
    <form class="ai-chat-input-bar" onsubmit="sendAiMsg(event)">
        <input type="text" id="ai-chat-input" placeholder="Ketik pertanyaan..." maxlength="500" autocomplete="off">
        <button type="submit" id="ai-chat-send" aria-label="Kirim">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
        </button>
    </form>
</div>

{{-- Custom Confirm Modal --}}
<div id="ai-confirm-modal" class="ai-confirm-overlay">
    <div class="ai-confirm-box">
        <div class="ai-confirm-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        </div>
        <div class="ai-confirm-title">Hapus Riwayat Chat?</div>
        <div class="ai-confirm-desc">Semua riwayat percakapan akan dihapus permanen dan tidak dapat dikembalikan.</div>
        <div class="ai-confirm-actions">
            <button class="ai-confirm-cancel" onclick="closeAiConfirm()">Batal</button>
            <button class="ai-confirm-delete" onclick="doDeleteHistory()">Hapus</button>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="ai-toast" class="ai-toast"></div>

<style>
/* === FAB === */
#ai-fab {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 100000;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #fff;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(29,78,216,0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, box-shadow 0.2s;
}
#ai-fab:hover { transform: scale(1.08); box-shadow: 0 6px 24px rgba(29,78,216,0.4); }
#ai-fab:active { transform: scale(0.95); }
.ai-fab-sparkle {
    position: absolute;
    top: 5px;
    right: 3px;
    color: #fbbf24;
    animation: ai-sparkle 2s ease-in-out infinite;
}
@keyframes ai-sparkle {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.7); }
}
#ai-fab.ai-fab-hidden { display: none; }

/* === Panel === */
#ai-chat-panel {
    position: fixed;
    z-index: 100001;
    background: #fff;
    display: none;
    flex-direction: column;
    overflow: hidden;
    transform: translateY(10px) scale(0.95);
    opacity: 0;
    transition: transform 0.25s cubic-bezier(.22,1,.36,1), opacity 0.25s ease;
    /* Mobile: compact popup bottom-right */
    bottom: 78px;
    right: 12px;
    width: calc(100vw - 24px);
    max-width: 360px;
    height: 55vh;
    max-height: 440px;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.18), 0 0 0 1px rgba(0,0,0,0.06);
}
#ai-chat-panel.ai-panel-open {
    display: flex;
    transform: translateY(0) scale(1);
    opacity: 1;
}

@media (min-width: 640px) {
    #ai-chat-panel {
        bottom: 80px;
        right: 20px;
        width: 380px;
        max-width: 380px;
        height: 520px;
        max-height: 520px;
        border-radius: 16px;
    }
}

/* === Header === */
.ai-chat-header {
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    border-radius: 16px 16px 0 0;
}
.ai-chat-header-info {
    display: flex;
    align-items: center;
    gap: 10px;
}
.ai-chat-avatar {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
    background: rgba(255,255,255,0.2);
    color: #fff;
}
.ai-chat-name {
    font-size: 14px;
    font-weight: 700;
    color: #fff;
}
.ai-chat-status {
    font-size: 11px;
    color: rgba(255,255,255,0.75);
}
.ai-chat-header-actions {
    display: flex;
    gap: 4px;
    align-items: center;
}
.ai-quota-badge {
    background: rgba(255,255,255,0.2);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 10px;
    white-space: nowrap;
}
.ai-quota-badge.ai-quota-low {
    background: rgba(239,68,68,0.8);
}
.ai-chat-header-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    color: #fff;
    border-radius: 8px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}
.ai-chat-header-btn:hover { background: rgba(255,255,255,0.25); }

/* === Messages === */
.ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #f8fafc;
}
.ai-msg, .user-msg {
    display: flex;
    gap: 8px;
    max-width: 90%;
    animation: ai-msg-in 0.25s ease;
}
@keyframes ai-msg-in {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.user-msg {
    align-self: flex-end;
    flex-direction: row-reverse;
}
.ai-msg-avatar {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 800;
    flex-shrink: 0;
    margin-top: 2px;
    background: #dbeafe;
    color: #1d4ed8;
}
.ai-msg-bubble {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 4px 12px 12px 12px;
    padding: 8px 12px;
    font-size: 13px;
    line-height: 1.6;
    color: #1e293b;
    word-break: break-word;
}
.ai-msg-bubble p { margin: 0 0 5px; }
.ai-msg-bubble p:last-child { margin-bottom: 0; }
.ai-msg-bubble strong { font-weight: 700; }
.ai-msg-bubble em { font-style: italic; }
.ai-msg-bubble ul, .ai-msg-bubble ol { margin: 3px 0; padding-left: 16px; }
.ai-msg-bubble li { margin-bottom: 2px; }

.ai-source-badge {
    display: inline-block;
    font-size: 10px;
    padding: 1px 6px;
    border-radius: 8px;
    margin-bottom: 4px;
    font-weight: 600;
}
.ai-badge-cache {
    background: #d1fae5;
    color: #065f46;
}
.ai-badge-ai {
    background: #dbeafe;
    color: #1d4ed8;
}
.ai-badge-history {
    background: #fef3c7;
    color: #92400e;
}

.user-msg-bubble {
    background: #1d4ed8;
    color: #fff;
    border-radius: 12px 4px 12px 12px;
    padding: 8px 12px;
    font-size: 13px;
    line-height: 1.5;
    word-break: break-word;
}

/* Date separator */
.ai-date-sep {
    text-align: center;
    font-size: 11px;
    color: #94a3b8;
    padding: 4px 0;
    font-weight: 600;
}
.ai-date-sep span {
    background: #f1f5f9;
    padding: 2px 10px;
    border-radius: 10px;
}

/* Typing indicator */
.ai-typing {
    display: flex;
    gap: 4px;
    padding: 8px 12px;
    align-items: center;
}
.ai-typing-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #94a3b8;
    animation: ai-typing-bounce 1.4s ease-in-out infinite;
}
.ai-typing-dot:nth-child(2) { animation-delay: 0.16s; }
.ai-typing-dot:nth-child(3) { animation-delay: 0.32s; }
@keyframes ai-typing-bounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-5px); }
}

/* Loading spinner */
.ai-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    color: #94a3b8;
    font-size: 12px;
    gap: 8px;
}
.ai-loading-spin {
    width: 16px;
    height: 16px;
    border: 2px solid #e2e8f0;
    border-top-color: #1d4ed8;
    border-radius: 50%;
    animation: ai-spin 0.6s linear infinite;
}
@keyframes ai-spin {
    to { transform: rotate(360deg); }
}

/* === Input Bar === */
.ai-chat-input-bar {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 12px;
    border-top: 1px solid #e2e8f0;
    background: #fff;
    flex-shrink: 0;
    border-radius: 0 0 16px 16px;
}
.ai-chat-input-bar input {
    flex: 1;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 13px;
    outline: none;
    background: #f8fafc;
    color: #111;
    font-family: inherit;
    transition: border-color 0.2s;
}
.ai-chat-input-bar input:focus {
    border-color: #1d4ed8;
    background: #fff;
}
#ai-chat-send {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: none;
    background: #1d4ed8;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: opacity 0.2s;
}
#ai-chat-send:hover { opacity: 0.85; }
#ai-chat-send:disabled { opacity: 0.5; cursor: not-allowed; }

/* === Confirm Modal === */
.ai-confirm-overlay {
    position: fixed;
    inset: 0;
    z-index: 100010;
    background: rgba(0,0,0,0.45);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: ai-fade-in 0.2s ease;
}
.ai-confirm-overlay.ai-confirm-open {
    display: flex;
}
@keyframes ai-fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
.ai-confirm-box {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    max-width: 300px;
    width: 100%;
    text-align: center;
    box-shadow: 0 16px 48px rgba(0,0,0,0.2);
    animation: ai-confirm-pop 0.25s cubic-bezier(.22,1,.36,1);
}
@keyframes ai-confirm-pop {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.ai-confirm-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #fef2f2;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}
.ai-confirm-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
}
.ai-confirm-desc {
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 20px;
}
.ai-confirm-actions {
    display: flex;
    gap: 8px;
}
.ai-confirm-cancel {
    flex: 1;
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.ai-confirm-cancel:hover { background: #f8fafc; }
.ai-confirm-delete {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 10px;
    background: #ef4444;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}
.ai-confirm-delete:hover { opacity: 0.9; }

/* === Toast === */
.ai-toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    z-index: 100020;
    background: #1e293b;
    color: #fff;
    font-size: 13px;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s, transform 0.3s;
    max-width: 320px;
    text-align: center;
    line-height: 1.5;
}
.ai-toast.ai-toast-show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

/* === Retry Button === */
.ai-retry-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 8px;
    padding: 6px 14px;
    background: #1d4ed8;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}
.ai-retry-btn:hover { opacity: 0.85; }
.ai-error-info {
    margin-top: 6px;
    font-size: 11px;
    color: #94a3b8;
}
</style>

<script>
(function() {
    'use strict';

    const AI_CONFIG = @json($ai);
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
    let chatHistory = [];
    let chatOpen = false;
    let sending = false;
    let historyLoaded = false;
    let dailyRemaining = 20;
    let spamTimestamps = []; // Track last message timestamps for client-side spam guard

    function updateQuotaBadge(remaining) {
        dailyRemaining = remaining;
        const badge = document.getElementById('ai-quota-badge');
        if (!badge) return;
        badge.textContent = remaining + '/20';
        if (remaining <= 3) {
            badge.classList.add('ai-quota-low');
        } else {
            badge.classList.remove('ai-quota-low');
        }
    }

    window.toggleAiChat = function() {
        const panel = document.getElementById('ai-chat-panel');
        const fab = document.getElementById('ai-fab');
        if (!panel || !fab) return;

        chatOpen = !chatOpen;
        if (chatOpen) {
            panel.style.display = 'flex';
            fab.classList.add('ai-fab-hidden');
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    panel.classList.add('ai-panel-open');
                });
            });
            if (!historyLoaded) {
                loadHistory();
            }
            setTimeout(() => document.getElementById('ai-chat-input')?.focus(), 300);
        } else {
            panel.classList.remove('ai-panel-open');
            fab.classList.remove('ai-fab-hidden');
            setTimeout(() => { panel.style.display = 'none'; }, 250);
        }
    };

    async function loadHistory() {
        const container = document.getElementById('ai-chat-messages');
        // Show loading
        const loader = document.createElement('div');
        loader.className = 'ai-loading';
        loader.innerHTML = '<div class="ai-loading-spin"></div> Memuat riwayat...';
        container.appendChild(loader);

        try {
            const res = await fetch('/api/ai-chat/history?religion=' + encodeURIComponent(AI_CONFIG.religion), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            const data = await res.json();
            loader.remove();

            if (typeof data.remaining === 'number') {
                updateQuotaBadge(data.remaining);
            }

            if (data.messages && data.messages.length > 0) {
                let lastDate = null;
                data.messages.forEach(msg => {
                    // Date separator
                    if (msg.date !== lastDate) {
                        lastDate = msg.date;
                        addDateSeparator(formatDate(msg.date));
                    }
                    if (msg.role === 'user') {
                        addUserMsg(msg.content, false);
                    } else {
                        addAiMsg(msg.content, false, msg.is_cached ? 'cache' : 'ai');
                    }
                    chatHistory.push({ role: msg.role, content: msg.content });
                });
                scrollToBottom();
            }
        } catch (e) {
            loader.remove();
            if (!navigator.onLine) {
                showAiToast('Koneksi internetmu terputus. Periksa koneksi dan coba lagi ya! 📶');
            }
        }
        historyLoaded = true;
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr + 'T00:00:00');
        const today = new Date();
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);

        if (d.toDateString() === today.toDateString()) return 'Hari ini';
        if (d.toDateString() === yesterday.toDateString()) return 'Kemarin';

        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    function addDateSeparator(label) {
        const container = document.getElementById('ai-chat-messages');
        const div = document.createElement('div');
        div.className = 'ai-date-sep';
        div.innerHTML = '<span>' + escapeHtml(label) + '</span>';
        container.appendChild(div);
    }

    let lastFailedMsg = null;

    function getMidnightResetText() {
        const now = new Date();
        const midnight = new Date(now);
        midnight.setHours(24, 0, 0, 0);
        const diffMs = midnight - now;
        const hours = Math.floor(diffMs / 3600000);
        const mins = Math.floor((diffMs % 3600000) / 60000);
        if (hours > 0) return 'Kuota reset dalam ' + hours + ' jam ' + mins + ' menit (tengah malam WIB)';
        return 'Kuota reset dalam ' + mins + ' menit (tengah malam WIB)';
    }

    window.sendAiMsg = async function(e) {
        e.preventDefault();
        const input = document.getElementById('ai-chat-input');
        const msg = input.value.trim();
        if (!msg || sending) return;

        if (dailyRemaining <= 0) {
            addAiMsg('Maaf, kuota pertanyaanmu hari ini sudah habis (0/20). ' + getMidnightResetText() + ' ⏰', true);
            return;
        }

        // Client-side anti-spam: max 3 per 5 minutes
        const now5 = Date.now();
        spamTimestamps = spamTimestamps.filter(t => now5 - t < 300000);
        if (spamTimestamps.length >= 3) {
            const waitMs = 300000 - (now5 - spamTimestamps[0]);
            const waitMin = Math.ceil(waitMs / 60000);
            addAiMsg('Pelan-pelan ya! Tunggu sekitar ' + waitMin + ' menit lagi sebelum bertanya. ⏳', true);
            return;
        }

        // Check offline
        if (!navigator.onLine) {
            showAiToast('Koneksi internetmu terputus. Periksa koneksi dan coba lagi ya! 📶');
            return;
        }

        input.value = '';
        sending = true;
        lastFailedMsg = msg;
        document.getElementById('ai-chat-send').disabled = true;

        // Date separator for today if needed
        const seps = document.querySelectorAll('.ai-date-sep');
        const lastSep = seps.length > 0 ? seps[seps.length - 1].textContent.trim() : '';
        if (lastSep !== 'Hari ini') {
            addDateSeparator('Hari ini');
        }

        addUserMsg(msg, true);
        const typingEl = addTypingIndicator();
        showAiToast('Sedang memproses pertanyaanmu... 🤔');

        try {
            const response = await fetch('/api/ai-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: msg,
                    religion: AI_CONFIG.religion,
                    history: chatHistory.slice(-10).map(h => ({
                        role: h.role,
                        content: h.content.length > 4900 ? h.content.substring(0, 4900) : h.content,
                    })),
                }),
            });

            const data = await response.json();
            typingEl.remove();

            if (typeof data.remaining === 'number') {
                updateQuotaBadge(data.remaining);
            }

            // Rate limit (per-minute) — short wait, auto-retry friendly
            if (data.error_type === 'rate_limit') {
                showAiToast('Sedang mencari jawaban terbaik... ⏳');
                addAiMsgWithRetry(data.reply || 'Sabar ya, coba lagi dalam 1-2 menit! ⏳', msg, 'rate_limit');
                chatHistory.push({ role: 'user', content: msg });
                lastFailedMsg = msg;
                return;
            }

            // All providers failed (non-rate-limit)
            if (data.error_type === 'all_failed') {
                showAiToast('Layanan sedang istirahat sejenak. Coba lagi dalam beberapa menit 🙏');
                addAiMsgWithRetry(data.reply || 'Maaf, layanan sedang tidak tersedia.', msg, 'all_failed');
                chatHistory.push({ role: 'user', content: msg });
                lastFailedMsg = msg;
                return;
            }

            // Anti-spam limit hit
            if (data.error_type === 'spam_limit') {
                const waitMin = Math.ceil((data.wait_seconds || 60) / 60);
                showAiToast('Tunggu ' + waitMin + ' menit lagi ya! ⏳');
                addAiMsg(data.reply || 'Pelan-pelan ya! Tunggu sebentar. ⏳', true);
                return;
            }

            // Daily limit hit (from server)
            if (data.error_type === 'daily_limit') {
                updateQuotaBadge(0);
                addAiMsg((data.reply || 'Kuota hari ini habis.') + ' ' + getMidnightResetText() + ' ⏰', true);
                return;
            }

            if (data.reply) {
                const isCached = data.is_cached || false;
                const cacheType = data.cache_type || null;
                if (cacheType === 'own_history') {
                    showAiToast('📖 Kamu pernah menanyakan ini! Lihat jawaban sebelumnya.');
                } else if (cacheType === 'exact') {
                    showAiToast('💡 Pertanyaan ini sudah pernah dijawab! Ini jawabannya:');
                } else if (cacheType === 'similar') {
                    showAiToast('💡 Pertanyaan serupa ditemukan!');
                } else {
                    showAiToast('Jawaban diterima! ✅');
                }
                const badgeSource = cacheType === 'own_history' ? 'own_history' : (isCached ? 'cache' : 'ai');
                chatHistory.push({ role: 'user', content: msg });
                chatHistory.push({ role: 'assistant', content: data.reply });
                addAiMsg(data.reply, true, badgeSource);
                spamTimestamps.push(Date.now());
                lastFailedMsg = null;
            } else {
                showAiToast('Layanan sedang istirahat sejenak. Coba lagi dalam beberapa menit 🙏');
                addAiMsgWithRetry('Maaf, terjadi kendala. Silakan coba lagi ya!', msg);
            }
        } catch (err) {
            typingEl.remove();
            if (!navigator.onLine) {
                showAiToast('Koneksi internetmu terputus. Periksa koneksi dan coba lagi ya! 📶');
                addAiMsgWithRetry('Koneksi internetmu terputus. Periksa koneksi dan coba lagi ya! 📶', msg);
            } else {
                showAiToast('Layanan sedang istirahat sejenak. Coba lagi dalam beberapa menit 🙏');
                addAiMsgWithRetry('Maaf, terjadi kendala koneksi. Silakan coba lagi ya!', msg);
            }
        } finally {
            sending = false;
            document.getElementById('ai-chat-send').disabled = false;
            document.getElementById('ai-chat-input')?.focus();
        }
    };

    function addAiMsgWithRetry(text, originalMsg, errorType) {
        const container = document.getElementById('ai-chat-messages');
        const div = document.createElement('div');
        div.className = 'ai-msg';

        let infoText = '';
        if (errorType === 'rate_limit') {
            infoText = 'Coba lagi dalam 1-2 menit';
        } else {
            infoText = getMidnightResetText();
        }

        div.innerHTML =
            '<div class="ai-msg-avatar">' + AI_CONFIG.initials + '</div>' +
            '<div class="ai-msg-bubble">' + renderMarkdown(text) +
            '<br><button class="ai-retry-btn" onclick="retryAiMsg()">🔄 Coba Lagi</button>' +
            '<div class="ai-error-info">' + escapeHtml(infoText) + '</div></div>';
        container.appendChild(div);
        scrollToBottom();
    }

    window.retryAiMsg = function() {
        if (!lastFailedMsg || sending) return;
        // Remove last retry message
        const container = document.getElementById('ai-chat-messages');
        const retryBtns = container.querySelectorAll('.ai-retry-btn');
        retryBtns.forEach(btn => {
            const msgEl = btn.closest('.ai-msg');
            if (msgEl) msgEl.remove();
        });
        // Also remove the last user message that was for this retry
        const userMsgs = container.querySelectorAll('.user-msg');
        if (userMsgs.length > 0) {
            const lastUserMsg = userMsgs[userMsgs.length - 1];
            const bubble = lastUserMsg.querySelector('.user-msg-bubble');
            if (bubble && bubble.textContent === lastFailedMsg) {
                lastUserMsg.remove();
            }
        }
        // Re-send
        const input = document.getElementById('ai-chat-input');
        input.value = lastFailedMsg;
        sendAiMsg(new Event('submit'));
    };

    window.clearAiHistory = function() {
        document.getElementById('ai-confirm-modal')?.classList.add('ai-confirm-open');
    };

    window.closeAiConfirm = function() {
        document.getElementById('ai-confirm-modal')?.classList.remove('ai-confirm-open');
    };

    window.doDeleteHistory = async function() {
        closeAiConfirm();
        try {
            await fetch('/api/ai-chat/history', {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
        } catch(e) {}

        chatHistory = [];
        historyLoaded = false;
        updateQuotaBadge(10);
        const container = document.getElementById('ai-chat-messages');
        container.innerHTML = '';
        const div = document.createElement('div');
        div.className = 'ai-msg';
        div.innerHTML =
            '<div class="ai-msg-avatar">' + AI_CONFIG.initials + '</div>' +
            '<div class="ai-msg-bubble">' + escapeHtml(AI_CONFIG.welcome) + '</div>';
        container.appendChild(div);

        showAiToast('Riwayat chat berhasil dihapus');
    };

    function showAiToast(msg, duration) {
        const t = document.getElementById('ai-toast');
        if (!t) return;
        t.textContent = msg;
        t.classList.remove('ai-toast-show');
        void t.offsetWidth;
        t.classList.add('ai-toast-show');
        clearTimeout(t._timer);
        t._timer = setTimeout(() => t.classList.remove('ai-toast-show'), duration || 3000);
    }

    function addUserMsg(text, animate) {
        const container = document.getElementById('ai-chat-messages');
        const div = document.createElement('div');
        div.className = 'user-msg';
        if (!animate) div.style.animation = 'none';
        div.innerHTML = '<div class="user-msg-bubble">' + escapeHtml(text) + '</div>';
        container.appendChild(div);
        scrollToBottom();
    }

    function addAiMsg(text, animate, source) {
        const container = document.getElementById('ai-chat-messages');
        const div = document.createElement('div');
        div.className = 'ai-msg';
        if (!animate) div.style.animation = 'none';

        let badgeHtml = '';
        if (source === 'own_history') {
            badgeHtml = '<div class="ai-source-badge ai-badge-history">📖 Pernah Ditanya</div>';
        } else if (source === 'cache') {
            badgeHtml = '<div class="ai-source-badge ai-badge-cache">💾 Dari Cache</div>';
        } else if (source === 'ai') {
            badgeHtml = '<div class="ai-source-badge ai-badge-ai">🤖 AI</div>';
        }

        div.innerHTML =
            '<div class="ai-msg-avatar">' + AI_CONFIG.initials + '</div>' +
            '<div class="ai-msg-bubble">' + badgeHtml + '</div>';
        container.appendChild(div);

        const bubble = div.querySelector('.ai-msg-bubble');
        if (animate) {
            typeWriter(bubble, text, 0, badgeHtml);
        } else {
            bubble.innerHTML = badgeHtml + renderMarkdown(text);
        }
    }

    function typeWriter(el, text, idx, prefixHtml) {
        if (idx === 0) {
            el._fullText = text;
            el._charIdx = 0;
            el._prefix = prefixHtml || '';
        }
        const full = el._fullText;
        const len = full.length;
        const step = Math.max(1, Math.floor(len / 200));
        el._charIdx = Math.min(el._charIdx + step, len);
        el.innerHTML = el._prefix + renderMarkdown(full.substring(0, el._charIdx));
        scrollToBottom();
        if (el._charIdx < len) {
            setTimeout(() => typeWriter(el, text, el._charIdx), 12);
        }
    }

    function addTypingIndicator() {
        const container = document.getElementById('ai-chat-messages');
        const div = document.createElement('div');
        div.className = 'ai-msg';
        div.innerHTML =
            '<div class="ai-msg-avatar">' + AI_CONFIG.initials + '</div>' +
            '<div class="ai-msg-bubble"><div class="ai-typing"><div class="ai-typing-dot"></div><div class="ai-typing-dot"></div><div class="ai-typing-dot"></div></div></div>';
        container.appendChild(div);
        scrollToBottom();
        return div;
    }

    function scrollToBottom() {
        const container = document.getElementById('ai-chat-messages');
        if (container) container.scrollTop = container.scrollHeight;
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function renderMarkdown(text) {
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/^\s*[-*]\s+(.+)/gm, '<li>$1</li>')
            .replace(/^\s*(\d+)\.\s+(.+)/gm, '<li>$2</li>')
            .replace(/(<li>[\s\S]*?<\/li>)/g, '<ul>$1</ul>')
            .replace(/<\/ul>\s*<ul>/g, '')
            .replace(/\n{2,}/g, '</p><p>')
            .replace(/\n/g, '<br>')
            .replace(/^/, '<p>')
            .replace(/$/, '</p>')
            .replace(/<p><\/p>/g, '');
    }
})();
</script>
@endauth
