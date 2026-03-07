<?php

namespace App\Http\Controllers;

use App\Models\AiChatHistory;
use App\Services\AiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiChatController extends Controller
{
    private const MAX_PER_DAY = 20;

    private array $dailyLimitMessages = [
        'islam'    => "Jazakallah, istirahat dulu ya! Sisa kuota: {remaining}/20 🌙",
        'kristen'  => "Tuhan memberkati, istirahat dulu ya! Sisa kuota: {remaining}/20 ✝️",
        'katolik'  => "Terpujilah Tuhan, istirahat dulu ya! Sisa kuota: {remaining}/20 ⛪",
        'hindu'    => "Om Swastiastu, istirahat dulu ya! Sisa kuota: {remaining}/20 🕉️",
        'buddha'   => "Namo Buddhaya, istirahat dulu ya! Sisa kuota: {remaining}/20 ☸️",
        'konghucu' => "Wei De Dong Tian, istirahat dulu ya! Sisa kuota: {remaining}/20 🏮",
    ];

    private array $allFailedMessages = [
        'islam'    => "Bismillah, Ustadz AI sedang istirahat. Sementara bacalah Al-Quran. Insya Allah segera normal! 🤲",
        'kristen'  => "Puji Tuhan, Pastor AI sedang istirahat. Sementara bacalah Alkitab. Tuhan memberkati! 🙏",
        'katolik'  => "Terpujilah Tuhan, Romo AI istirahat. Sementara berdoa Rosario. Tuhan memberkati! 🙏",
        'hindu'    => "Om Swastiastu, Pandit AI istirahat. Sementara baca Bhagavad Gita. Om Shanti! 🙏",
        'buddha'   => "Namo Buddhaya, Bhikkhu AI istirahat. Sementara bermeditasi sejenak. Semoga berbahagia! 🙏",
        'konghucu' => "Wei De Dong Tian, Wenshi AI istirahat. Sementara renungkan ajaran Kongzi. Xian You Yi De! 🙏",
    ];

    private array $rateLimitMessages = [
        'islam'    => "Sabar ya, Ustadz AI sedang melayani banyak pertanyaan. Coba lagi dalam 1-2 menit! ⏳",
        'kristen'  => "Sabar ya, Pastor AI sedang melayani banyak pertanyaan. Coba lagi dalam 1-2 menit! ⏳",
        'katolik'  => "Sabar ya, Romo AI sedang melayani banyak pertanyaan. Coba lagi dalam 1-2 menit! ⏳",
        'hindu'    => "Sabar ya, Pandit AI sedang melayani banyak pertanyaan. Coba lagi dalam 1-2 menit! ⏳",
        'buddha'   => "Sabar ya, Bhikkhu AI sedang melayani banyak pertanyaan. Coba lagi dalam 1-2 menit! ⏳",
        'konghucu' => "Sabar ya, Wenshi AI sedang melayani banyak pertanyaan. Coba lagi dalam 1-2 menit! ⏳",
    ];

    public function chat(Request $request, AiChatService $service): JsonResponse
    {
        $request->validate([
            'message'  => 'required|string|max:500',
            'religion' => 'nullable|string|in:islam,kristen,katolik,hindu,buddha,konghucu',
            'history'  => 'nullable|array|max:10',
            'history.*.role' => 'required_with:history|string|in:user,assistant',
            'history.*.content' => 'required_with:history|string|max:5000',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $agama = $service->normalizeReligion($request->input('religion', $user->agama ?? 'islam'));

        // Anti-spam: max 3 messages per 5 minutes
        $fiveMinAgo = now()->subMinutes(5);
        $recentCount = AiChatHistory::where('user_id', $user->id)
            ->where('role', 'user')
            ->where('created_at', '>=', $fiveMinAgo)
            ->count();

        if ($recentCount >= 3) {
            $oldest = AiChatHistory::where('user_id', $user->id)
                ->where('role', 'user')
                ->where('created_at', '>=', $fiveMinAgo)
                ->orderBy('created_at')
                ->first();

            $waitSeconds = $oldest
                ? max(0, 300 - now()->diffInSeconds($oldest->created_at))
                : 60;

            return response()->json([
                'reply' => 'Pelan-pelan ya! Kamu sudah bertanya 3 kali dalam 5 menit terakhir. Tunggu sebentar agar bisa bertanya lagi. ⏳',
                'provider' => null,
                'success' => false,
                'error_type' => 'spam_limit',
                'wait_seconds' => (int) $waitSeconds,
            ]);
        }

        // Daily limit
        $todayCount = AiChatHistory::where('user_id', $user->id)
            ->where('role', 'user')
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $remaining = max(0, self::MAX_PER_DAY - $todayCount);

        // Call service (checks cache layers + AI fallback)
        $result = $service->chat(
            $request->input('message'),
            $request->input('history', []),
            $agama,
            $remaining,
            $user->id
        );

        $isCached = $result['is_cached'] ?? false;

        // Daily limit exhausted (no cache hit)
        if (!$isCached && !$result['success'] && ($result['error_type'] ?? '') === 'daily_limit') {
            $msg = $this->dailyLimitMessages[$agama] ?? $this->dailyLimitMessages['islam'];
            $msg = str_replace('{remaining}', '0', $msg);
            return response()->json([
                'reply' => $msg,
                'provider' => null,
                'success' => false,
                'error_type' => 'daily_limit',
                'remaining' => 0,
            ]);
        }

        // All providers failed
        if (!$result['success']) {
            $errorType = $result['error_type'] ?? 'all_failed';

            if ($errorType === 'rate_limit') {
                return response()->json([
                    'reply' => $this->rateLimitMessages[$agama] ?? $this->rateLimitMessages['islam'],
                    'provider' => null,
                    'success' => false,
                    'error_type' => 'rate_limit',
                    'remaining' => $remaining,
                ]);
            }

            return response()->json([
                'reply' => $this->allFailedMessages[$agama] ?? $this->allFailedMessages['islam'],
                'provider' => null,
                'success' => false,
                'error_type' => 'all_failed',
                'remaining' => $remaining,
            ]);
        }

        // Save messages to history
        AiChatHistory::create([
            'user_id' => $user->id,
            'role' => 'user',
            'content' => mb_substr($request->input('message'), 0, 2000),
            'religion' => $agama,
            'is_cached' => false,
        ]);

        AiChatHistory::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'content' => mb_substr($result['reply'], 0, 5000),
            'religion' => $agama,
            'is_cached' => $isCached,
        ]);

        // Cached responses don't decrement remaining
        $result['remaining'] = $isCached ? $remaining : max(0, $remaining - 1);
        return response()->json($result);
    }

    public function history(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $religion = $request->query('religion');

        $query = AiChatHistory::where('user_id', $user->id);
        if ($religion) {
            $query->where('religion', $religion);
        }

        $messages = $query->select('role', 'content', 'is_cached', 'created_at')
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->values()
            ->map(fn($m) => [
                'role' => $m->role,
                'content' => $m->content,
                'is_cached' => (bool) $m->is_cached,
                'date' => $m->created_at->format('Y-m-d'),
                'time' => $m->created_at->format('H:i'),
            ]);

        $todayCount = AiChatHistory::where('user_id', $user->id)
            ->where('role', 'user')
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $remaining = max(0, self::MAX_PER_DAY - $todayCount);

        return response()->json(['messages' => $messages, 'remaining' => $remaining]);
    }

    public function clearHistory(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        AiChatHistory::where('user_id', $user->id)->delete();

        return response()->json(['success' => true]);
    }
}
