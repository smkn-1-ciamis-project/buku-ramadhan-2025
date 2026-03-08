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
            'llama-3.3-70b-versatile',
            'meta-llama/llama-4-maverick-17b-128e-instruct',
            'meta-llama/llama-4-scout-17b-16e-instruct',
            'llama-3.1-8b-instant',
            'qwen/qwen3-32b',
            'moonshotai/kimi-k2-instruct',
        ],
        'gemini' => ['gemini-3-flash-preview', 'gemini-2.5-flash'],
        'nvidia' => [
            'moonshotai/kimi-k2-instruct',
            'deepseek-ai/deepseek-v3.2',
            'meta/llama-4-maverick-17b-128e-instruct',
            'mistralai/mistral-small-3.1-24b-instruct-2503',
            'meta/llama-4-scout-17b-16e-instruct',
            'aisingapore/sea-lion-7b-instruct',
        ],
        'openrouter' => [
            'meta-llama/llama-3.3-70b-instruct:free',
            'mistral/mistral-small-3.1-24b:free',
            'google/gemma-3-27b-it:free',
            'qwen/qwen3-4b:free',
            'openrouter/free',
        ],
        'cloudflare' => [
            '@cf/meta/llama-3.3-70b-instruct-fp8-fast',
            '@cf/meta/llama-4-scout-17b-16e-instruct',
            '@cf/meta/llama-3.1-8b-instruct-fast',
            '@cf/mistral/mistral-small-3.1-24b-instruct',
            '@cf/aisingapore/gemma-sea-lion-v4-27b-it',
        ],
    ];

    private array $systemPrompts = [
        'islam' => 'Kamu asisten Islam bernama "Ustadz AI". HANYA jawab tentang Islam: Al-Quran, Hadith, Fiqih, sejarah Islam, Ibadah, etika Islam, Halal/Haram, dan gaya hidup Muslim. Di luar Islam: "Maaf, saya hanya dapat membantu pertanyaan seputar Islam." Aturan: jawab bahasa user; selalu sertakan ayat Al-Quran atau Hadith sebagai referensi; jangan mengarang ayat/hadith; bersikap hormat dan rendah hati; mulai dengan Bismillah jika sesuai; JANGAN beri lebih dari 150 kata. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3 sisa, ingatkan dengan: "Sisa pertanyaanmu hari ini: {remaining}/20, kuota akan reset tengah malam ya! 🌙". Akhiri dengan satu saran pertanyaan lanjutan yang relevan.',

        'kristen' => 'Kamu asisten Kristen bernama "Pastor AI". HANYA jawab tentang Kristen: Alkitab, teologi Kristen, kehidupan Yesus, doa, etika, sejarah gereja, hari raya Kristen. Di luar Kristen: "Maaf, saya hanya dapat membantu pertanyaan seputar agama Kristen." Aturan: jawab bahasa user; selalu sertakan ayat Alkitab sebagai referensi; jangan mengarang ayat; bersikap hangat dan penuh kasih; gunakan "God bless you" jika sesuai; JANGAN beri lebih dari 150 kata. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3 sisa, ingatkan dengan: "Sisa pertanyaanmu hari ini: {remaining}/20, kuota akan reset tengah malam ya! ✝️". Akhiri dengan satu saran pertanyaan lanjutan yang relevan.',

        'katolik' => 'Kamu asisten Katolik bernama "Romo AI". HANYA jawab tentang Katolik: Alkitab, teologi Katolik, Sakramen, Santo/Santa, doa Rosario, sejarah Gereja, ajaran moral, Katekismus. Di luar Katolik: "Maaf, saya hanya dapat membantu pertanyaan seputar agama Katolik." Aturan: jawab bahasa user; selalu sertakan ayat Alkitab atau referensi Katekismus; jangan mengarang kitab suci/doktrin; bersikap hormat dan khidmat; gunakan "Terpujilah Tuhan" jika sesuai; JANGAN beri lebih dari 150 kata. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3 sisa, ingatkan dengan: "Sisa pertanyaanmu hari ini: {remaining}/20, kuota akan reset tengah malam ya! ⛪". Akhiri dengan satu saran pertanyaan lanjutan yang relevan.',

        'hindu' => 'Kamu asisten Hindu bernama "Pandit AI". HANYA jawab tentang Hindu: Veda, Upanishad, Bhagavad Gita, dewa-dewi Hindu, Dharma, Karma, Moksha, ritual, festival Hindu. Di luar Hindu: "Maaf, saya hanya dapat membantu pertanyaan seputar agama Hindu." Aturan: jawab bahasa user; selalu sertakan referensi kitab suci Hindu; jangan mengarang ajaran; gunakan istilah Sansekerta jika sesuai; gunakan "Om Swastiastu" jika sesuai; JANGAN beri lebih dari 150 kata. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3 sisa, ingatkan dengan: "Sisa pertanyaanmu hari ini: {remaining}/20, kuota akan reset tengah malam ya! 🕉️". Akhiri dengan satu saran pertanyaan lanjutan yang relevan.',

        'buddha' => 'Kamu asisten Buddha bernama "Bhikkhu AI". HANYA jawab tentang Buddha: Tripitaka, Dhammapada, kehidupan Buddha, Empat Kebenaran Mulia, Jalan Mulia Berunsur Delapan, meditasi, etika Buddha, festival. Di luar Buddha: "Maaf, saya hanya dapat membantu pertanyaan seputar agama Buddha." Aturan: jawab bahasa user; selalu sertakan referensi kitab suci Buddha; jangan mengarang ajaran; bersikap tenang dan penuh welas asih; gunakan "Namo Buddhaya" jika sesuai; JANGAN beri lebih dari 150 kata. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3 sisa, ingatkan dengan: "Sisa pertanyaanmu hari ini: {remaining}/20, kuota akan reset tengah malam ya! ☸️". Akhiri dengan satu saran pertanyaan lanjutan yang relevan.',

        'konghucu' => 'Kamu asisten Konghucu bernama "Wenshi AI". HANYA jawab tentang Konghucu: Si Shu, Wu Jing, ajaran Kongzi dan Mengzi, nilai-nilai Konghucu, ritual, festival, tradisi Konghucu Indonesia (MATAKIN, Kelenteng). Di luar Konghucu: "Maaf, saya hanya dapat membantu pertanyaan seputar agama Khonghucu." Aturan: jawab bahasa user; selalu sertakan referensi kitab suci Konghucu; jangan mengarang ajaran; bersikap bijak, hormat, dan rendah hati; gunakan "Wei De Dong Tian" jika sesuai; JANGAN beri lebih dari 150 kata. Sisa kuota: {remaining}/20 (reset tengah malam). Jika ≤3 sisa, ingatkan dengan: "Sisa pertanyaanmu hari ini: {remaining}/20, kuota akan reset tengah malam ya! 🏮". Akhiri dengan satu saran pertanyaan lanjutan yang relevan.',
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
            } catch (\Exception $e) {
            }
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
            } catch (\Exception $e) {
            }
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
        $providers = ['groq', 'gemini', 'openrouter', 'cloudflare', 'nvidia'];
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
                ])->timeout(8)->connectTimeout(3)->retry(1, 500)->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'                  => $model,
                    'messages'               => $messages,
                    'temperature'            => 0.7,
                    'top_p'                  => 0.9,
                    'max_completion_tokens'  => $maxTokens,
                    'stop'                   => null,
                    'service_tier'           => 'auto',
                    'user'                   => (string) \Illuminate\Support\Facades\Auth::id(),
                    'stream'                 => false,
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

        $contents = [];
        $recent = collect($history)
            ->take(-5)
            ->values();
        foreach ($recent as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $contents[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => Str::limit($msg['content'], 500)]],
                ];
            }
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        foreach ($this->models['gemini'] as $model) {
            try {
                $response = Http::timeout(8)->connectTimeout(3)->retry(1, 500)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    [
                        'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                        'contents'           => $contents,
                        'generation_config'  => [
                            'temperature'      => 0.7,
                            'max_output_tokens' => $maxTokens,
                            'thinking_level'   => 'low',
                        ],
                    ]
                );

                if ($response->successful()) {
                    $finishReason = $response->json('candidates.0.finishReason');
                    $content = $response->json('candidates.0.content.parts.0.text');
                    if ($content !== null) {
                        return [
                            'content'       => $content,
                            'finish_reason' => $finishReason === 'MAX_TOKENS' ? 'length' : $finishReason,
                        ];
                    }
                    Log::info("[AiChat] Gemini {$model} returned empty content, trying next");
                    continue;
                }

                if (in_array($response->status(), [404, 429, 500, 502, 503])) {
                    Log::info("[AiChat] Gemini {$model} HTTP {$response->status()}, trying next");
                    continue;
                }

                Log::warning('[AiChat] Gemini error', ['model' => $model, 'status' => $response->status()]);
            } catch (\Exception $e) {
                Log::warning("[AiChat] Gemini {$model}: {$e->getMessage()}");
            }
        }

        return null;
    }

    private function callNvidia(string $systemPrompt, string $message, array $history, int $maxTokens): ?array
    {
        $apiKey = env('NVIDIA_API_KEY');
        if (!$apiKey) return null;

        Log::info('[AiChat] NVIDIA called as last resort — all other providers failed');

        $messages = $this->buildOpenAiMessages($systemPrompt, $message, $history);

        foreach ($this->models['nvidia'] as $model) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                ])->timeout(8)->connectTimeout(3)->retry(1, 500)->post('https://integrate.api.nvidia.com/v1/chat/completions', [
                    'model'       => $model,
                    'messages'    => $messages,
                    'temperature' => 0.7,
                    'max_tokens'  => min($maxTokens, 4096),
                    'stop'        => null,
                    'stream'      => false,
                ]);

                if ($response->successful()) {
                    Log::info('[AiChat] NVIDIA credit used', ['model' => $model]);
                    return [
                        'content'       => $response->json('choices.0.message.content'),
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
                    'Authorization'  => "Bearer {$apiKey}",
                    'HTTP-Referer'   => 'https://calakan.smkn1ciamis.id',
                    'X-Title'        => 'Calakan AI',
                ])->timeout(8)->connectTimeout(3)->retry(1, 500)->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model'                 => $model,
                    'messages'              => $messages,
                    'max_completion_tokens' => max(512, $maxTokens),
                    'temperature'           => 0.7,
                    'top_p'                 => 0.9,
                    'stream'                => false,
                    'stop'                  => null,
                    'user'                  => (string) \Illuminate\Support\Facades\Auth::id(),
                    'provider'              => ['data_collection' => 'deny'],
                ]);

                if ($response->successful()) {
                    return [
                        'content'       => $response->json('choices.0.message.content'),
                        'finish_reason' => $response->json('choices.0.finish_reason'),
                    ];
                }

                if (in_array($response->status(), [402, 429, 502, 503])) {
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
                $url = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/ai/v1/chat/completions";
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiToken}",
                ])->timeout(8)->connectTimeout(3)->retry(1, 500)->post($url, [
                    'model'       => $model,
                    'messages'    => $messages,
                    'max_tokens'  => $maxTokens,
                    'temperature' => 0.7,
                    'stream'      => false,
                ]);

                if ($response->successful()) {
                    return [
                        'content'       => $response->json('choices.0.message.content'),
                        'finish_reason' => $response->json('choices.0.finish_reason'),
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
            'perbedaan',
            'jelaskan',
            'sebutkan',
            'apa saja',
            'bagaimana',
            'list',
            'compare',
            'explain',
            'sejarah',
        ];

        $isComplex = $wordCount > 100 || Str::contains($lower, $keywords);

        if ($isComplex) return 1024;
        if ($wordCount > 50) return 512;
        return 256;
    }

    private function buildOpenAiMessages(string $systemPrompt, string $message, array $history): array
    {
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        $recent = collect($history)
            ->take(-5)
            ->values();
        foreach ($recent as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                    'content' => Str::limit($msg['content'], 500),
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $message];
        return $messages;
    }

    private function cleanReply(string $reply): string
    {
        // Extract think content as fallback before stripping
        $thinkContent = '';
        if (preg_match('/<think>([\.\s\S]*?)<\/think>/i', $reply, $thinkMatch)) {
            $thinkContent = trim($thinkMatch[1]);
        }

        $reply = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $reply);
        $reply = preg_replace('/<think>[\s\S]*/i', '', $reply);
        $reply = trim($reply);

        // If nothing left after stripping think tags, use think content as answer
        if ($reply === '' && $thinkContent !== '') {
            return $thinkContent;
        }

        return $reply;
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
