<?php

namespace App\Services;

use App\Models\AiChatHistory;
use App\Models\FaqCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiChatService
{
    private array $models = [
        'groq' => [
            'moonshotai/kimi-k2-instruct',
            'qwen/qwen3-32b',
            'meta-llama/llama-4-maverick-17b-128e-instruct',
            'meta-llama/llama-4-scout-17b-16e-instruct',
            'llama-3.3-70b-versatile',
            'llama-3.1-8b-instant',
        ],
        'gemini' => ['gemini-1.5-flash'],
        'nvidia' => [
            'moonshotai/kimi-k2-instruct',
            'deepseek-ai/deepseek-v3.2',
            'meta-llama/llama-3.3-70b-instruct',
        ],
        'openrouter' => [
            'meta-llama/llama-3.1-8b-instruct:free',
            'mistralai/mistral-7b-instruct:free',
        ],
        'cloudflare' => [
            '@cf/meta/llama-3.1-8b-instruct',
            '@cf/mistral/mistral-7b-instruct-v0.1',
        ],
    ];

    private array $systemPrompts = [
        'islam' => 'Kamu "Ustadz AI". HANYA jawab tentang Islam (Al-Quran, Hadith, Fiqih, Ibadah, sejarah Islam, Halal/Haram). Sertakan ayat/hadist. Non-Islam: "Maaf, saya hanya membantu seputar Islam." Jawab bahasa user. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3, ingatkan. PENTING: Jawab RINGKAS maksimal 150 kata. Selesaikan kalimat terakhir dengan sempurna.',

        'kristen' => 'Kamu "Pastor AI". HANYA jawab tentang Kristen (Alkitab, teologi, Yesus, doa, etika). Sertakan ayat Alkitab. Non-Kristen: "Maaf, saya hanya membantu seputar Kristen." Jawab bahasa user. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3, ingatkan. PENTING: Jawab RINGKAS maksimal 150 kata. Selesaikan kalimat terakhir dengan sempurna.',

        'katolik' => 'Kamu "Romo AI". HANYA jawab tentang Katolik (Alkitab, teologi, Sakramen, Katekismus). Sertakan ayat/Katekismus. Non-Katolik: "Maaf, saya hanya membantu seputar Katolik." Jawab bahasa user. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3, ingatkan. PENTING: Jawab RINGKAS maksimal 150 kata. Selesaikan kalimat terakhir dengan sempurna.',

        'hindu' => 'Kamu "Pandit AI". HANYA jawab tentang Hindu (Veda, Upanishad, Bhagavad Gita, Dharma, Karma, Moksha). Sertakan referensi kitab. Non-Hindu: "Maaf, saya hanya membantu seputar Hindu." Jawab bahasa user. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3, ingatkan. PENTING: Jawab RINGKAS maksimal 150 kata. Selesaikan kalimat terakhir dengan sempurna.',

        'buddha' => 'Kamu "Bhikkhu AI". HANYA jawab tentang Buddha (Tripitaka, Dhammapada, Empat Kebenaran, meditasi). Sertakan referensi kitab. Non-Buddha: "Maaf, saya hanya membantu seputar Buddha." Jawab bahasa user. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3, ingatkan. PENTING: Jawab RINGKAS maksimal 150 kata. Selesaikan kalimat terakhir dengan sempurna.',

        'konghucu' => 'Kamu "Wenshi AI". HANYA jawab tentang Konghucu (Si Shu, Wu Jing, ajaran Kongzi/Mengzi, MATAKIN). Sertakan referensi kitab. Non-Konghucu: "Maaf, saya hanya membantu seputar Konghucu." Jawab bahasa user. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3, ingatkan. PENTING: Jawab RINGKAS maksimal 150 kata. Selesaikan kalimat terakhir dengan sempurna.',
    ];

    /**
     * Main chat method with 3-layer cache + AI fallback chain.
     *
     * Flow: Own History → Cache (Redis/File) → MySQL FAQ → AI API (5 providers)
     */
    public function chat(string $message, array $history, string $agama, int $remaining = 20, ?string $userId = null): array
    {
        $religion = $this->normalizeReligion($agama);
        $normalized = strtolower(trim($message));
        $hash = md5($normalized);

        // Layer 0: Own history (exact text match for this user)
        if ($userId) {
            $ownReply = $this->findOwnHistory($userId, $message);
            if ($ownReply) {
                Log::info('[AiChat] Own history hit');
                return [
                    'reply' => $ownReply,
                    'provider' => 'cache',
                    'success' => true,
                    'is_cached' => true,
                    'cache_type' => 'own_history',
                ];
            }
        }

        // Layer 1: Cache driver (Redis/File) — fastest
        try {
            $cacheKey = "faq_{$religion}_{$hash}";
            $cachedAnswer = Cache::get($cacheKey);
            if ($cachedAnswer) {
                FaqCache::where('religion', $religion)
                    ->where('question_hash', $hash)
                    ->increment('hit_count');
                Log::info('[AiChat] Cache Layer 1 hit');
                return [
                    'reply' => $cachedAnswer,
                    'provider' => 'cache',
                    'success' => true,
                    'is_cached' => true,
                    'cache_type' => 'exact',
                ];
            }
        } catch (\Exception $e) {
            Log::warning('[AiChat] Cache Layer 1 error: ' . $e->getMessage());
        }

        // Layer 2: MySQL FAQ Cache (exact hash + fuzzy similarity)
        $mysqlHit = $this->findMysqlCache($normalized, $hash, $religion);
        if ($mysqlHit) {
            try {
                Cache::put("faq_{$religion}_{$hash}", $mysqlHit['answer'], 86400);
            } catch (\Exception $e) {}
            return [
                'reply' => $mysqlHit['answer'],
                'provider' => 'cache',
                'success' => true,
                'is_cached' => true,
                'cache_type' => $mysqlHit['type'],
            ];
        }

        // No cache — check daily quota before calling API
        if ($remaining <= 0) {
            return [
                'reply' => null,
                'provider' => null,
                'success' => false,
                'is_cached' => false,
                'error_type' => 'daily_limit',
            ];
        }

        // Layer 3: AI API with fallback chain (Groq → Gemini → NVIDIA → OpenRouter → Cloudflare)
        $systemPrompt = $this->getSystemPrompt($religion);
        $systemPrompt = str_replace('{remaining}', (string) $remaining, $systemPrompt);
        $maxTokens = $this->getMaxTokens($message);
        $result = $this->callAiWithFallback($systemPrompt, $message, $history, $maxTokens);

        // Cache successful AI response for future requests
        if ($result['success'] && !empty($result['reply'])) {
            $this->saveFaqCache($message, $religion, $result['reply']);
            try {
                Cache::put("faq_{$religion}_{$hash}", $result['reply'], 86400);
            } catch (\Exception $e) {}
        }

        return $result;
    }

    // ─── Cache Methods ───────────────────────────────────────────────────

    private function findOwnHistory(string $userId, string $message): ?string
    {
        $userMsg = AiChatHistory::where('user_id', $userId)
            ->where('role', 'user')
            ->where('content', $message)
            ->latest()
            ->first();

        if (!$userMsg) return null;

        return AiChatHistory::where('user_id', $userId)
            ->where('role', 'assistant')
            ->where('created_at', '>', $userMsg->created_at)
            ->orderBy('created_at')
            ->value('content');
    }

    private function findMysqlCache(string $normalized, string $hash, string $religion): ?array
    {
        $exact = FaqCache::select('id', 'answer', 'hit_count')
            ->where('religion', $religion)
            ->where('question_hash', $hash)
            ->first();

        if ($exact) {
            $exact->increment('hit_count');
            Log::info('[AiChat] MySQL cache hit (exact)');
            return ['answer' => $exact->answer, 'type' => 'exact'];
        }

        $recent = FaqCache::where('religion', $religion)
            ->select('id', 'question', 'answer')
            ->latest()
            ->take(10)
            ->get();

        foreach ($recent as $item) {
            similar_text($normalized, strtolower($item->question), $percent);
            if ($percent >= 80) {
                $item->increment('hit_count');
                Log::info('[AiChat] MySQL cache hit (fuzzy)', ['similarity' => round($percent, 1)]);
                return ['answer' => $item->answer, 'type' => 'similar'];
            }
        }

        return null;
    }

    private function saveFaqCache(string $message, string $religion, string $answer): void
    {
        $normalized = strtolower(trim($message));
        $hash = md5($normalized);

        FaqCache::updateOrCreate(
            ['religion' => $religion, 'question_hash' => $hash],
            ['question' => $message, 'answer' => $answer, 'hit_count' => 0]
        );
    }

    // ─── AI Fallback Chain ───────────────────────────────────────────────

    private function callAiWithFallback(string $systemPrompt, string $message, array $history, int $maxTokens): array
    {
        $providers = ['groq', 'gemini', 'nvidia', 'openrouter', 'cloudflare'];
        $allRateLimited = true;

        foreach ($providers as $provider) {
            try {
                $result = match ($provider) {
                    'groq' => $this->callGroq($systemPrompt, $message, $history, $maxTokens),
                    'gemini' => $this->callGemini($systemPrompt, $message, $history, $maxTokens),
                    'nvidia' => $this->callNvidia($systemPrompt, $message, $history, $maxTokens),
                    'openrouter' => $this->callOpenRouter($systemPrompt, $message, $history, $maxTokens),
                    'cloudflare' => $this->callCloudflare($systemPrompt, $message, $history, $maxTokens),
                };

                if ($result !== null) {
                    $content = $result['content'] ?? '';
                    $finishReason = $result['finish_reason'] ?? null;
                    $cleaned = $this->cleanReply($content);

                    if ($cleaned !== '') {
                        if ($finishReason === 'length') {
                            $cleaned = $this->gracefulEnding($cleaned);
                        }
                        return ['reply' => $cleaned, 'provider' => $provider, 'success' => true, 'is_cached' => false];
                    }
                    Log::info("[AiChat] {$provider} returned empty, trying next");
                    $allRateLimited = false;
                }
            } catch (\Exception $e) {
                Log::warning("[AiChat] {$provider} failed: {$e->getMessage()}");
                if (!str_contains($e->getMessage(), '429')) {
                    $allRateLimited = false;
                }
            }
        }

        return [
            'reply' => null,
            'provider' => null,
            'success' => false,
            'is_cached' => false,
            'error_type' => $allRateLimited ? 'rate_limit' : 'all_failed',
        ];
    }

    // ─── Provider Methods ────────────────────────────────────────────────

    private function callGroq(string $systemPrompt, string $message, array $history, int $maxTokens): ?array
    {
        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) return null;

        $messages = $this->buildOpenAiMessages($systemPrompt, $message, $history);

        foreach ($this->models['groq'] as $model) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                ])->timeout(15)->retry(1, 500)->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => $maxTokens,
                ]);

                if ($response->successful()) {
                    return [
                        'content' => $response->json('choices.0.message.content'),
                        'finish_reason' => $response->json('choices.0.finish_reason'),
                    ];
                }

                if (in_array($response->status(), [429, 500, 502, 503])) {
                    Log::info("[AiChat] Groq {$model} HTTP {$response->status()}, trying next");
                    continue;
                }

                Log::warning("[AiChat] Groq error", ['model' => $model, 'status' => $response->status()]);
            } catch (\Exception $e) {
                Log::warning("[AiChat] Groq {$model}: {$e->getMessage()}");
            }
        }

        return null;
    }

    private function callGemini(string $systemPrompt, string $message, array $history, int $maxTokens): ?array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) return null;

        $model = $this->models['gemini'][0];
        $contents = [];
        $recent = array_slice($history, -5);
        foreach ($recent as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $contents[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => mb_substr($msg['content'], 0, 1000)]],
                ];
            }
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        try {
            $response = Http::timeout(15)->retry(1, 500)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => $maxTokens,
                    ],
                ]
            );

            if ($response->successful()) {
                $finishReason = $response->json('candidates.0.finishReason');
                return [
                    'content' => $response->json('candidates.0.content.parts.0.text'),
                    'finish_reason' => $finishReason === 'MAX_TOKENS' ? 'length' : $finishReason,
                ];
            }

            if (in_array($response->status(), [429, 500, 502, 503])) {
                Log::info("[AiChat] Gemini HTTP {$response->status()}");
                return null;
            }

            Log::warning('[AiChat] Gemini error', ['status' => $response->status()]);
        } catch (\Exception $e) {
            Log::warning("[AiChat] Gemini: {$e->getMessage()}");
        }

        return null;
    }

    private function callNvidia(string $systemPrompt, string $message, array $history, int $maxTokens): ?array
    {
        $apiKey = env('NVIDIA_API_KEY');
        if (!$apiKey) return null;

        $messages = $this->buildOpenAiMessages($systemPrompt, $message, $history);

        foreach ($this->models['nvidia'] as $model) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                ])->timeout(15)->retry(1, 500)->post('https://integrate.api.nvidia.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => $maxTokens,
                ]);

                if ($response->successful()) {
                    return [
                        'content' => $response->json('choices.0.message.content'),
                        'finish_reason' => $response->json('choices.0.finish_reason'),
                    ];
                }

                if (in_array($response->status(), [429, 500, 502, 503])) {
                    Log::info("[AiChat] NVIDIA {$model} HTTP {$response->status()}, trying next");
                    continue;
                }

                Log::warning("[AiChat] NVIDIA error", ['model' => $model, 'status' => $response->status()]);
            } catch (\Exception $e) {
                Log::warning("[AiChat] NVIDIA {$model}: {$e->getMessage()}");
            }
        }

        return null;
    }

    private function callOpenRouter(string $systemPrompt, string $message, array $history, int $maxTokens): ?array
    {
        $apiKey = env('OPENROUTER_API_KEY');
        if (!$apiKey) return null;

        $messages = $this->buildOpenAiMessages($systemPrompt, $message, $history);

        foreach ($this->models['openrouter'] as $model) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'HTTP-Referer' => config('app.url', 'http://localhost'),
                    'X-Title' => 'Calakan App',
                ])->timeout(15)->retry(1, 500)->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => $maxTokens,
                ]);

                if ($response->successful()) {
                    return [
                        'content' => $response->json('choices.0.message.content'),
                        'finish_reason' => $response->json('choices.0.finish_reason'),
                    ];
                }

                if (in_array($response->status(), [429, 500, 502, 503])) {
                    Log::info("[AiChat] OpenRouter {$model} HTTP {$response->status()}, trying next");
                    continue;
                }

                Log::warning("[AiChat] OpenRouter error", ['model' => $model, 'status' => $response->status()]);
            } catch (\Exception $e) {
                Log::warning("[AiChat] OpenRouter {$model}: {$e->getMessage()}");
            }
        }

        return null;
    }

    private function callCloudflare(string $systemPrompt, string $message, array $history, int $maxTokens): ?array
    {
        $accountId = env('CLOUDFLARE_ACCOUNT_ID');
        $apiToken = env('CLOUDFLARE_API_TOKEN');
        if (!$accountId || !$apiToken) return null;

        $messages = $this->buildOpenAiMessages($systemPrompt, $message, $history);

        foreach ($this->models['cloudflare'] as $model) {
            try {
                $url = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/ai/run/{$model}";
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiToken}",
                ])->timeout(15)->retry(1, 500)->post($url, [
                    'messages' => $messages,
                ]);

                if ($response->successful() && $response->json('success')) {
                    return [
                        'content' => $response->json('result.response'),
                        'finish_reason' => 'stop',
                    ];
                }

                if (in_array($response->status(), [429, 500, 502, 503])) {
                    Log::info("[AiChat] Cloudflare {$model} HTTP {$response->status()}, trying next");
                    continue;
                }

                Log::warning("[AiChat] Cloudflare error", ['model' => $model, 'status' => $response->status()]);
            } catch (\Exception $e) {
                Log::warning("[AiChat] Cloudflare {$model}: {$e->getMessage()}");
            }
        }

        return null;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────

    private function getSystemPrompt(string $religion): string
    {
        return $this->systemPrompts[$religion] ?? $this->systemPrompts['islam'];
    }

    public function normalizeReligion(string $agama): string
    {
        $key = strtolower(trim($agama));
        return match ($key) {
            'muslim' => 'islam',
            'protestan' => 'kristen',
            'budha' => 'buddha',
            'khonghucu' => 'konghucu',
            default => $key,
        };
    }

    private function getMaxTokens(string $message): int
    {
        $wordCount = str_word_count($message);
        $lower = strtolower($message);

        $keywords = [
            'perbedaan', 'jelaskan', 'sebutkan', 'apa saja', 'bagaimana',
            'list', 'compare', 'explain', 'sejarah',
        ];

        $isComplex = $wordCount > 100 || Str::contains($lower, $keywords);

        if ($isComplex) return 1024;
        if ($wordCount > 50) return 512;
        return 256;
    }

    private function buildOpenAiMessages(string $systemPrompt, string $message, array $history): array
    {
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        $recent = array_slice($history, -5);
        foreach ($recent as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                    'content' => mb_substr($msg['content'], 0, 1000),
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $message];
        return $messages;
    }

    private function cleanReply(string $reply): string
    {
        $reply = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $reply);
        $reply = preg_replace('/<think>[\s\S]*/i', '', $reply);
        return trim($reply);
    }

    private function gracefulEnding(string $text): string
    {
        if (preg_match('/^(.+[.!?\)\]\}])\s/s', $text, $matches)) {
            return trim($matches[1]);
        }

        $lastDot = mb_strrpos($text, '.');
        $lastExcl = mb_strrpos($text, '!');
        $lastQ = mb_strrpos($text, '?');
        $lastEnd = max($lastDot ?: 0, $lastExcl ?: 0, $lastQ ?: 0);

        if ($lastEnd > 0) {
            return trim(mb_substr($text, 0, $lastEnd + 1));
        }

        return trim($text) . '...';
    }
}
