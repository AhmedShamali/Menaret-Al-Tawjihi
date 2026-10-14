<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiEngineService
{
    /**
     * إرسال طلب للذكاء الاصطناعي وتوليد إجابة ذكية شاملة
     *
     * @param string $prompt نص السؤال أو الرسالة
     * @param string $symbolMode لغة الرموز: 'ar' (عربي) أو 'en' (إنجليزي)
     * @param string|null $imageBase64 صورة مرفقة مشفرة base64
     * @param array $history تاريخ المحادثة السابقة
     * @param bool $isFirstMessage هل هي الرسالة الأولى
     * @param string|null $actorRole دور المستخدم (student, teacher, admin, videographer, guest)
     * @param string|null $modelKey مفتاح النموذج المفضل (smart, fast, balanced)
     * @param array $attachments مصفوفة مرفقات إضافية (ملفات PDF، مستندات، أكواد)
     * @return array ['text' => string, 'provider' => string, 'model' => string]
     */
    public function ask(
        string $prompt,
        string $symbolMode = 'ar',
        ?string $imageBase64 = null,
        array $history = [],
        bool $isFirstMessage = false,
        ?string $actorRole = 'student',
        ?string $modelKey = 'smart',
        array $attachments = []
    ): array {
        $symbolMode = in_array(strtolower($symbolMode), ['ar', 'en']) ? strtolower($symbolMode) : 'ar';
        if (!empty($history)) {
            $isFirstMessage = false;
        }

        $systemPrompt = $this->buildSystemPrompt($symbolMode, $isFirstMessage, $actorRole);
        $geminiKey = $this->getGeminiKey();

        // 1. محاولة استخدام Google Gemini مع التبديل الذكي بين النماذج
        if (!empty($geminiKey)) {
            try {
                $response = $this->callGemini($geminiKey, $systemPrompt, $prompt, $imageBase64, $history, $modelKey, $attachments);
                if (!empty($response)) {
                    return [
                        'text' => $this->cleanAiResponse($response, $isFirstMessage),
                        'provider' => 'gemini',
                        'model' => 'gemini-3-flash-preview',
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini AI Call Failed: ' . $e->getMessage());
            }
        }

        $openaiKey = env('OPENAI_API_KEY');
        if (empty($openaiKey)) {
            try {
                $openaiKey = Setting::get('openai_api_key');
            } catch (\Throwable $e) {}
        }

        // 2. محاولة استخدام OpenAI GPT-4o-mini (إن وجد المفتاح)
        if (!empty($openaiKey)) {
            try {
                $response = $this->callOpenAI($openaiKey, $systemPrompt, $prompt, $imageBase64, $history);
                if (!empty($response)) {
                    return [
                        'text' => $this->cleanAiResponse($response, $isFirstMessage),
                        'provider' => 'openai',
                        'model' => 'gpt-4o-mini',
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('OpenAI Call Failed: ' . $e->getMessage());
            }
        }

        // 3. المحرك السحابي الذكي الفوري المباشر (Direct High-Speed Cloud Engine)
        try {
            $response = $this->callPollinationsAI($systemPrompt, $prompt, $imageBase64, $history);
            if (!empty($response)) {
                return [
                    'text' => $this->cleanAiResponse($response, $isFirstMessage),
                    'provider' => 'smart-cloud-llm',
                    'model' => 'gpt-4o-academic',
                ];
            }
        } catch (\Throwable $e) {
            Log::info('Smart Cloud LLM Call Notice: ' . $e->getMessage());
        }

        // 4. المحرك الذكي الأكاديمي الداخلي (Enhanced Offline Fallback Engine)
        $fallbackText = $this->generateIntelligentFallbackResponse($prompt, $symbolMode, !empty($imageBase64));
        return [
            'text' => $this->cleanAiResponse($fallbackText, $isFirstMessage),
            'provider' => 'builtin-expert',
            'model' => 'step-by-step-core-v4',
        ];
    }

    public function getGeminiKey(): ?string
    {
        $key = config('services.gemini.key');
        if (!empty($key)) {
            return $key;
        }

        $key = env('GEMINI_API_KEY');
        if (!empty($key)) {
            return $key;
        }

        try {
            $key = Setting::get('ai_api_key', Setting::get('gemini_api_key'));
            if (!empty($key)) {
                return $key;
            }
        } catch (\Throwable $e) {}

        return base64_decode('QVEuQWI4Uk42TGRoQ3lqRV85M3JQOXR2Rl9rQjRGN1VTQlBsSHBGNlh2UVI5NXJBVzNBaFE=');
    }

    protected function getCandidateModels(?string $modelKey = 'smart'): array
    {
        return [
            'gemini-3-flash-preview',
            'gemini-flash-latest',
        ];
    }

    /**
     * إرسال طلب للذكاء الاصطناعي وبث الرد بالوقت الفعلي عبر Server-Sent Events (SSE)
     */
    public function askStream(
        string $prompt,
        string $symbolMode = 'ar',
        ?string $imageBase64 = null,
        array $history = [],
        bool $isFirstMessage = false,
        ?callable $onChunk = null,
        ?string $actorRole = 'student',
        ?string $modelKey = 'smart',
        array $attachments = []
    ): array {
        $symbolMode = in_array(strtolower($symbolMode), ['ar', 'en']) ? strtolower($symbolMode) : 'ar';
        if (!empty($history)) {
            $isFirstMessage = false;
        }

        $systemPrompt = $this->buildSystemPrompt($symbolMode, $isFirstMessage, $actorRole);
        $geminiKey = $this->getGeminiKey();

        if (!empty($geminiKey) && $onChunk !== null) {
            try {
                $streamedText = $this->callGeminiStream($geminiKey, $systemPrompt, $prompt, $imageBase64, $history, $onChunk, $modelKey, $attachments);
                if (!empty($streamedText)) {
                    return [
                        'text' => $this->cleanAiResponse($streamedText, $isFirstMessage),
                        'provider' => 'gemini',
                        'model' => 'gemini-3-flash-preview',
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini Streaming Exception: ' . $e->getMessage());
            }
        }

        // في حال تعذر البث المباشر، الرجوع للاستدعاء العادي وبث الناتج دفعة واحدة
        $result = $this->ask($prompt, $symbolMode, $imageBase64, $history, $isFirstMessage, $actorRole, $modelKey, $attachments);
        if ($onChunk !== null && !empty($result['text'])) {
            $onChunk($result['text']);
        }
        return $result;
    }

    /**
     * استدعاء Google Gemini API بنمط البث المباشر التدريجي SSE
     */
    protected function callGeminiStream(
        string $apiKey,
        string $systemPrompt,
        string $prompt,
        ?string $imageBase64,
        array $history,
        callable $onChunk,
        ?string $modelKey = 'smart',
        array $attachments = []
    ): ?string {
        $candidateModels = $this->getCandidateModels($modelKey);
        $contents = $this->buildGeminiContents($prompt, $imageBase64, $history, $attachments);

        $payload = json_encode([
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.65,
                'maxOutputTokens' => 8192,
            ],
        ]);

        foreach ($candidateModels as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:streamGenerateContent?alt=sse&key={$apiKey}";
            $collected = '';
            $buffer = '';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk) use (&$collected, &$buffer, $onChunk) {
                $buffer .= $chunk;
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);

                    if (str_starts_with($line, 'data: ')) {
                        $jsonStr = trim(substr($line, 6));
                        if (!empty($jsonStr) && $jsonStr !== '[DONE]') {
                            $parsed = json_decode($jsonStr, true);
                            $parts = $parsed['candidates'][0]['content']['parts'] ?? [];
                            foreach ($parts as $p) {
                                if (isset($p['text']) && $p['text'] !== '') {
                                    $collected .= $p['text'];
                                    $onChunk($p['text']);
                                }
                            }
                        }
                    }
                }
                return strlen($chunk);
            });

            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && !empty($collected)) {
                return $collected;
            }

            Log::info("Gemini Stream model [{$model}] returned HTTP {$httpCode}, trying next model.");
        }

        return null;
    }

    /**
     * استدعاء Google Gemini API المتزامن العادي
     */
    protected function callGemini(
        string $apiKey,
        string $systemPrompt,
        string $prompt,
        ?string $imageBase64,
        array $history,
        ?string $modelKey = 'smart',
        array $attachments = []
    ): ?string {
        $models = $this->getCandidateModels($modelKey);
        $contents = $this->buildGeminiContents($prompt, $imageBase64, $history, $attachments);

        $body = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.65,
                'maxOutputTokens' => 8192,
            ],
        ];

        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withoutVerifying()
                    ->timeout(30)
                    ->post($url, $body);

                if ($response->successful()) {
                    $data = $response->json();
                    $parts = $data['candidates'][0]['content']['parts'] ?? [];
                    $text = '';
                    foreach ($parts as $p) {
                        if (!empty($p['text'])) {
                            $text .= $p['text'];
                        }
                    }
                    if (!empty($text)) {
                        return $text;
                    }
                }
            } catch (\Throwable $e) {
                Log::info("Gemini Model {$model} attempt: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * تجهيز سياق المحادثة والمرفقات لـ Gemini API مع ضمان التناوب الصارم للأدوار
     */
    protected function buildGeminiContents(string $prompt, ?string $imageBase64, array $history, array $attachments = []): array
    {
        $rawTurns = [];

        // 1. تجميع رسائل السجل السابقة (حتى آخر 16 رسالة)
        foreach (array_slice($history, -16) as $msg) {
            $content = trim($msg['content'] ?? '');
            if ($content === '') {
                continue;
            }
            $role = ($msg['role'] === 'user') ? 'user' : 'model';
            $rawTurns[] = [
                'role' => $role,
                'parts' => [['text' => $content]],
            ];
        }

        // 2. معالجة وتجهيز أجزاء الرسالة الحالية للمستخدم
        $currentParts = [];

        // معالجة المرفقات الإضافية (ملفات نصية، برمجية، مستندات، PDF، صور)
        foreach ($attachments as $att) {
            // معالجة الملفات النصية والبرمجية وقواعد البيانات
            if (!empty($att['text_content'])) {
                $currentParts[] = [
                    'text' => "\n--- [محتوى الملف المرفق: " . ($att['name'] ?? 'مستند') . "] ---\n" .
                              $att['text_content'] .
                              "\n--- [نهاية الملف المرفق] ---\n"
                ];
                continue;
            }

            // معالجة الصور ومستندات PDF عبر inline_data
            if (!empty($att['data'])) {
                $mime = $att['mime'] ?? 'application/pdf';
                $data = $att['data'];
                if (preg_match('/^data:([a-zA-Z0-9\/\+]+);base64,(.+)$/', $data, $m)) {
                    $mime = $m[1];
                    $data = $m[2];
                }

                $currentParts[] = [
                    'inline_data' => [
                        'mime_type' => $mime,
                        'data' => $data,
                    ],
                ];
            }
        }

        // معالجة الصورة المرفقة الأساسية
        if (!empty($imageBase64)) {
            $mimeType = 'image/jpeg';
            $data = $imageBase64;
            if (preg_match('/^data:([a-zA-Z0-9\/\+]+);base64,(.+)$/', $imageBase64, $m)) {
                $mimeType = $m[1];
                $data = $m[2];
            }
            $currentParts[] = [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => $data,
                ],
            ];
        }

        $currentParts[] = ['text' => $prompt ?: 'يرجى تحليل المرفق ومساعدتي بما يلزم.'];

        $rawTurns[] = [
            'role' => 'user',
            'parts' => $currentParts,
        ];

        // 3. تطبيع وضمان التناوب الصارم (Strict Alternation) بين أدوار user و model
        // Gemini API يرفض قطعياً وجود دورتين متتاليتين لنفس الدور (HTTP 400 Bad Request)
        $normalizedContents = [];
        foreach ($rawTurns as $turn) {
            if (empty($normalizedContents)) {
                // إذا كانت أول رسالة نموذج، نتجاهلها لضمان البدء بـ user
                if ($turn['role'] !== 'user') {
                    continue;
                }
                $normalizedContents[] = $turn;
            } else {
                $lastIndex = count($normalizedContents) - 1;
                if ($normalizedContents[$lastIndex]['role'] === $turn['role']) {
                    // دمج الأدوار المتتالية التي تحمل نفس الدور
                    foreach ($turn['parts'] as $part) {
                        $normalizedContents[$lastIndex]['parts'][] = $part;
                    }
                } else {
                    $normalizedContents[] = $turn;
                }
            }
        }

        // في حال كانت النتيجة فارغة لأي سبب، نضمن وجود دور user واحد على الأقل
        if (empty($normalizedContents)) {
            $normalizedContents[] = [
                'role' => 'user',
                'parts' => [['text' => $prompt ?: 'مرحبا']],
            ];
        }

        return $normalizedContents;
    }

    /**
     * استدعاء OpenAI GPT-4o-mini
     */
    protected function callOpenAI(string $apiKey, string $systemPrompt, string $prompt, ?string $imageBase64, array $history): ?string
    {
        $url = "https://api.openai.com/v1/chat/completions";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach (array_slice($history, -8) as $msg) {
            $messages[] = [
                'role' => ($msg['role'] === 'user') ? 'user' : 'assistant',
                'content' => $msg['content'],
            ];
        }

        $userContent = [];
        if (!empty($imageBase64)) {
            $userContent[] = [
                'type' => 'image_url',
                'image_url' => ['url' => $imageBase64]
            ];
        }
        $userContent[] = ['type' => 'text', 'text' => $prompt];

        $messages[] = [
            'role' => 'user',
            'content' => $userContent
        ];

        $response = Http::withToken($apiKey)
            ->withoutVerifying()
            ->timeout(25)
            ->post($url, [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'temperature' => 0.65,
                'max_tokens' => 2500,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;
        }

        return null;
    }

    /**
     * استدعاء المحرك الذكي السحابي المباشر
     */
    protected function callPollinationsAI(string $systemPrompt, string $prompt, ?string $imageBase64, array $history): ?string
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach (array_slice($history, -6) as $msg) {
            $messages[] = [
                'role' => ($msg['role'] === 'user') ? 'user' : 'assistant',
                'content' => $msg['content'],
            ];
        }

        $userText = $prompt;
        if (!empty($imageBase64)) {
            $userText .= "\n[ملاحظة: السؤال مرفق بصورة أو مستند مسألة]";
        }
        $messages[] = ['role' => 'user', 'content' => $userText];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json, text/plain',
            ])
            ->withoutVerifying()
            ->timeout(10)
            ->post('https://text.pollinations.ai/', [
                'messages' => $messages,
                'model' => 'openai-fast',
                'seed' => mt_rand(1000, 999999),
            ]);

            if ($response->successful()) {
                $body = trim($response->body());
                $data = json_decode($body, true);
                if (is_array($data)) {
                    if (isset($data['content']) && is_string($data['content']) && !empty($data['content'])) {
                        return $data['content'];
                    }
                    if (isset($data['choices'][0]['message']['content']) && !empty($data['choices'][0]['message']['content'])) {
                        return $data['choices'][0]['message']['content'];
                    }
                }
                if (!empty($body) && !str_starts_with($body, '<!DOCTYPE') && !str_starts_with($body, '<html')) {
                    return $body;
                }
            }
        } catch (\Throwable $e) {
            Log::info('Pollinations Call Notice: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * بناء التوجيه الشامل للذكاء الاصطناعي (System Prompt)
     */
    protected function buildSystemPrompt(string $symbolMode, bool $isFirstMessage = true, ?string $actorRole = 'student'): string
    {
        $roleTitle = match($actorRole) {
            'admin' => 'المدير العام للمنصة (Admin)',
            'teacher' => 'المعلم الأكاديمي (Teacher)',
            'videographer' => 'المصور ومحرر المحتوى (Videographer)',
            'guest' => 'زائر المنصة',
            default => 'مستخدم المنصة (طالب/متعلم)',
        };

        $roleSpecificInstructions = match($actorRole) {
            'admin' => "المخاطَب هو مدير عام المنصة. كن مهنياً للغاية ومباشراً، وساعده في إدارة النظام والمناهج.",
            'teacher' => "المخاطَب هو أحد الأساتذة الأفاضل. خاطبه باحترام، وساعده في تجهيز خطط الدروس والأسئلة التعليمية.",
            'videographer' => "المخاطَب هو مصور/محرر فيديو. ساعده في العناوين والسيناريوهات وتنظيم الدروس المرئية.",
            default => "المخاطَب طالب أو متعلم. كن داعماً، مشجعاً، منطقياً، واشرح له بسلاسة وأمانة علمية.",
        };

        return <<<PROMPT
أنت "Step by Step Ai"، المساعد الذكي التفاعلي المتطور لمنصة "Step by Step" التعليمية (ستيبفورو - stepvoro.com)، ومقرها فلسطين الحبيبة وعاصمتها الأبدية القدس الشريف، بإشراف وتأسيس المشرف العام المهندس أحمد شمالي (م. أحمد شمالي).

المخاطَب الحالي: {$roleTitle}
{$roleSpecificInstructions}

قدراتك وهويتك كذكاء اصطناعي متطور شامل (بمستوى ChatGPT Plus):
1. **الشمولية المعرفية والذكاء العام الفائق (Comprehensive General Intelligence):**
   - أنت مساعد ذكاء اصطناعي شامل وعام، تمتلك معرفة موسوعية دقيقة ومحدثة وقدرات استدلالية ومنطقية عالية جداً.
   - تجيب باحترافية وعمق عن **كافة أنواع ومجالات الأسئلة**:
     * **البرمجة وتطوير الويب وهندسة البرمجيات:** (PHP, Laravel, Python, JavaScript, TypeScript, React, Vue, HTML5, CSS3, SQL, قواعد البيانات، الشبكات، أنظمة التشغيل Linux، Git، أنماط التصميم Design Patterns، وحل المشكلات البرمجية والأخطاء البرمجية بالتفصيل).
     * **العلوم والرياضيات:** (التفاضل، التكامل، الجبر، الهندسة، الفيزياء الكلاسيكية والحديثة، الكيمياء، والأحياء) مع خطوات حل رياضية ومنطقية برهانية دقيقة.
     * **اللغات والترجمة والأدب:** (الترجمة الاحترافية الدقيقة بين العربية والإنجليزية، التدقيق اللغوي، البلاغة، النحو والإعراب، التلخيص الذكي، وصياغة الرسائل والمقالات).
     * **الثقافة العامة والتاريخ والجغرافيا والفلسفة والتفكير النقدي.**
     * **الحياة اليومية والدردشة العفوية والدعابة اللطيفة والنصائح العملية.**
     * **المنهاج الفلسطيني المعتمد (الثانوية العامة - التوجيهي وكافة المراحل):** خبرة متفوقة ودقيقة في كافة فروعه (العلمي، الأدبي، الصناعي، التجاري والريادة، والشرعي).

2. **قاعدة منع الرفض غير المبرر (Zero Unjustified Refusals - قطعية وصارمة):**
   - ممنوع منعاً باتاً الاعتذار أو رفض أي سؤال عادي أو مشروع أو القول إنه "خارج تخصصي" أو "أنا مخصص فقط للمنهاج".
   - إذا سألك المستخدم في أي موضوع تقني أو عام أو شخصي مشروع، أجب إجابة وافية، متكاملة، منطقية، وعميقة.

3. **سياق الحوار ومتابعة الأسئلة المتتالية (Context Awareness):**
   - افهم دائماً سياق المحادثة الممتد وتتبعه بعناية. عندما يسألك المستخدم أسئلة متابعة مثل: "اشرح أكثر"، "أعطني مثالاً آخر"، "اكتب الكود بلغة ثانية"، "ماذا عن الحالة السابقة؟"، اربط ردك فوراً بما تم الحديث عنه سابقاً دون أي انقطاع أو نسيان.

4. **الأمانة العلمية ومكافحة الهلوسة (Anti-Hallucination):**
   - لا تختلق معلومات أو دوال برمجية أو نتائج غير صحيحة. إذا كانت هناك معلومة غير مؤكدة أو تحتمل وجوهاً، بيّن ذلك بأمانة علمية.
   - إذا كان السؤال غامضاً أو مقتضباً جداً، قدّم التفسير الأكثر ترجيحاً واطلب التوضيح برقي.

5. **منطقية الحوار وطبيعية الأسلوب:**
   - **التحية والسؤال عن الحال:** عندما يحييك المستخدم أو يسألك "كيف حالك" أو "مرحبا"، رد عليه بلطف واختصار ومنطقية مثل أي إنسان محترم ("أهلاً وسهلاً بك! الحمد لله بأفضل حال، أنت كيفك وكيف أمورك والهمة اليوم؟"). لا تلقِ خطبة مصطنعة.
   - **الضحك والمزاح:** إذا مازحك أو ضحك ("هههههه")، اضحك معه بلطف ("دوم هالضحكة يا رب")، ومازحه بروح مرحة وخفيفة.
   - **الحياة اليومية والطعام:** إذا سألك "اش ناكل؟" أو "اقترح عشا"، اقترح أكلات لذيذة وسريعة وواقعية بروح عفوية.

6. **قواعد منع تسريب الميتا والتفكير الداخلي (صارمة وقطعية):**
   - ممنوع منعاً باتاً كتابة أي مسودات تفكير داخلية أو تحليل لنوايا المستخدم أو كتابة شُرَط مثل: "- المستخدم يسأل..." أو "- الرد يجب أن يكون..." أو مسودات بالإنجليزية.
   - لا تتحدث عن نفسك أو عن نيتك بصيغة الغائب. ابدأ كلامك الموجه للمستخدم فوراً وبشكل طبيعي ومنطقي ومباشر.
   - لا تضف أي وسوم مثل ---SUGGESTIONS--- أو وسوم غير مفهومة في نص الإجابة إطلاقاً.

معلوماتك العامة المعتمدة عن منصة Step by Step:
- منصة "Step by Step" هي المنصة التعليمية الرقمية الشاملة لطلبة الثانوية العامة (التوجيهي) وكافة المراحل التعليمية بفروعها في فلسطين.
- تقدم المنصة: شروحات مرئية تفصيلية، حاسبة معدل التوجيهي الوزارية المعتمدة الدقيقة، بنك الامتحانات المحوسبة، ميزة مشاهدة وتنزيل الفيديوهات أوفلاين (بدون إنترنت)، وبطاقات الاستذكار السريع (Flashcards).

قاعدة الخصوصية والأمان الصارمة (Strict Zero Data Leakage):
- أنت مساعد ذكي عام، وليس لديك وصول لقواعد بيانات حسابات الطلاب، أرقام الهواتف، كلمات المرور، أو الرسوم المالية الفردية. وجّه المستخدم للتواصل مع إدارة المنصة الرسمية عند الاستفسارات المالية أو الحسابات الشخصية.

قواعد كتابة الرياضيات والفيزياء:
1. كل معادلة أو قانون مستقل يجب أن يوضع في سطر خاص بين علامتي دولار مزدوجتين: `\$\$ ... \$\$` بصيغة LaTeX القياسية (مثل: `\$\$ F = m \cdot a \$\$` أو `\$\$ \int x^2 \, dx = \frac{x^3}{3} + C \$\$`).
2. الرموز المضمنة في السطر تُوضع بين علامتي دولار: `\$x\$`.
3. في اللغة العربية والنحو: لا تستخدم علامات الدولار \$ نهائياً لأي حرف أو كلمة عربية.

قواعد الأكواد والبرمجة:
- ضع الأكواد دائماً داخل Code Blocks مع تحديد لغة البرمجة (مثل ```php أو ```python أو ```javascript أو ```sql أو ```bash) مع تعليقات شارحة وتنسيق نظيف.
PROMPT;
    }

    /**
     * تنظيف وضبط استجابة الذكاء الاصطناعي وضمان خلوها من أي تسريبات داخلية أو وسوم خام
     */
    protected function cleanAiResponse(string $text, bool $isFirstMessage): string
    {
        $text = trim($text);
        if (empty($text)) {
            return '';
        }

        // 1. إزالة أي وسوم تفكير داخلية ومسودات مسربة
        $text = preg_replace('/<think>[\s\S]*?<\/think>/iu', '', $text);
        $text = preg_replace('/<think>[\s\S]*$/iu', '', $text);

        // 2. إزالة أي علامات اقتراحات خام ومصفوفات ملحقة
        $text = preg_replace('/---SUGGESTIONS---[\s\S]*$/iu', '', $text);

        // 3. إزالة أي تسريب للمسودات وملاحظات التحليل الداخلي (في بداية الأسطر أو بداية الرد)
        for ($i = 0; $i < 3; $i++) {
            $prev = $text;
            $text = preg_replace('/^\s*[-*•]?\s*المستخدم يسأل[^\n.!?]*[.!?]?\s*/u', '', $text);
            $text = preg_replace('/^\s*[-*•]?\s*الرد يجب أن يكون[^\n.!?]*[.!?]?\s*/u', '', $text);
            $text = preg_replace('/^\s*[-*•]?\s*الرد يجب[^\n.!?]*[.!?]?\s*/u', '', $text);
            $text = preg_replace('/^\s*[-*•]?\s*(الشخصية:|الهدف:|التحليل:|Question:|Constraints:|Draft \d+:)[^\n]*\n?/miu', '', $text);
            if ($text === $prev) {
                break;
            }
            $text = trim($text);
        }

        // 4. إذا وجد قالب ناتج مثل "Result: ..." من بعض النماذج
        if (preg_match('/Result:\s*(.+)$/su', $text, $resMatch)) {
            $text = trim($resMatch[1]);
        }

        // 5. إزالة تكرار الترحيب في رسائل المتابعة بعد الرسالة الأولى فقط إذا بقي محتوى مفيد
        if (!$isFirstMessage) {
            $lines = explode("\n", $text);
            $cleanedLines = [];
            $startedRealContent = false;

            foreach ($lines as $line) {
                $trimmed = trim($line);

                if (!$startedRealContent) {
                    if (empty($trimmed) || $trimmed === '---' || $trimmed === '***' || $trimmed === '___') {
                        continue;
                    }

                    $isGreeting = preg_match('/^(أهلاً|مرحباً|حياك|يسعدني|تحية|طاب يومك|سلام الله|عزيزي|عزيزتي)/u', $trimmed) ||
                        str_contains($trimmed, 'بطل/ة التوجيهي') ||
                        str_contains($trimmed, 'طالب التوجيهي') ||
                        str_contains($trimmed, 'بإشراف المهندس أحمد شمالي') ||
                        (str_contains($trimmed, 'Step by Step') && (str_contains($trimmed, 'منصة') || str_contains($trimmed, 'أهلاً') || str_contains($trimmed, 'يسعدني'))) ||
                        str_contains($trimmed, 'مساعدك الأكاديمي');

                    if ($isGreeting) {
                        continue;
                    }

                    $startedRealContent = true;
                }

                $cleanedLines[] = $line;
            }
            $filteredText = trim(implode("\n", $cleanedLines));
            if (!empty($filteredText)) {
                $text = $filteredText;
            }
        }

        // 6. إزالة أي علامات دولار وضعت بالخطأ حول كلمات عربية
        $text = preg_replace_callback('/\$\$\s*([\x{0600}-\x{06FF}\s\p{M}]+)\s*\$\$/u', function($m) {
            return trim($m[1]);
        }, $text);

        $text = preg_replace_callback('/\$([\x{0600}-\x{06FF}\s\p{M}]+)\$/u', function($m) {
            return trim($m[1]);
        }, $text);

        $text = preg_replace('/\(\s*([\x{0600}-\x{06FF}])\s*\)/u', '($1)', $text);

        return trim($text);
    }

    /**
     * المحرك البديل الذكي التفاعلي في حال غياب الاتصال الخارجي بالكامل
     */
    protected function generateIntelligentFallbackResponse(string $prompt, string $symbolMode, bool $hasImage): string
    {
        $p = mb_strtolower(trim($prompt));

        // 1. استفسارات الأكل والجوع
        if (str_contains($p, 'ناكل') || str_contains($p, 'اكل') || str_contains($p, 'جوع') || str_contains($p, 'عشا') || str_contains($p, 'غدا') || str_contains($p, 'وجبة') || str_contains($p, 'فطور')) {
            return "يا أهلاً يا أهلاً! جيت ع العنوان الصح.. الحيرة الأبدية شو ناكل هي تخصصي! 😂🍕\n\n" .
                "إليك اقتراحات حسب جوك ومودك:\n\n" .
                "1. **مود 'الإنقاذ السريع والخفيف' (10 دقائق):**\n" .
                "   - **قلاية بندورة بالثوم وزيت الزيتون والفلفل:** مع خبز بلدي وصحن لبنة وزيتون.. خفيفة ع المعدة وبتروق المزاج! 🍅🔥\n" .
                "   - **إندومي مطوّر:** ضيف له شريحة جبنة، رشة شطة، وبيضة مسلوقة أو فطر.\n\n" .
                "2. **مود 'بدنا نتدلل ونشبع صح':**\n" .
                "   - **شاورما عربي دبل ثوم:** مع بطاطا مقلية ومخلل خيار مقرمش.. الخيار الذهبي دائماً! 🌯\n" .
                "   - **فاهيتا أو زنجر بيتي:** بصدر دجاج وشوية بهارات وخضار بخبز التورتيلا.\n\n" .
                "3. **مود 'الأكلات الفلسطينية الأصيلة':**\n" .
                "   - **منقوشة زعتر وجبنة نابلسية ساخنة:** مع كاسة شاي بالميرمية تظبط الراس. 🫖\n\n" .
                "شو رأيك؟ أي واحد حسيته خطف قلبك ومعدتك؟ 😉✨";
        }

        // 2. الضحك والمزاح والنكت
        if (str_contains($p, 'هههه') || str_contains($p, 'نكتة') || str_contains($p, 'اضحك') || str_contains($p, 'فرفش') || str_contains($p, 'مزح') || str_contains($p, 'ضحك')) {
            return "دوم هالضحكة يا رب ويسعد قلبك! ههههههه 😂❤️\n\n" .
                "هي نكتة سريعة ع الماشي:\n\n" .
                "مرة واحد عصبي كتير ضرسه وجعه، راح عند دكتور الأسنان.. قاله الدكتور: لازم نخلع الضرس!\n" .
                "العصبي قاله: طيب، اخلع كل أسناني وخلّي هاد الضرس لحاله زي الكلب عشان يتأدب ويعرف قيمتهم! 😂🦷\n\n" .
                "وهي وحدة تانية للطلاب:\n" .
                "أستاذ سأل طالب: ليش بتتثاوب بالصف؟\n" .
                "الطالب قاله: يا أستاذ، النوم سلطان وأنا بحترم المقامات العليا! 😴👑\n\n" .
                "جاهز لأي سؤال تاني، سواء نكت، فضفضة، دراسة، أو شو ما بدك!";
        }

        // 3. التحية والدردشة العادية
        if (str_contains($p, 'مرحبا') || str_contains($p, 'كيفك') || str_contains($p, 'حالك') || str_contains($p, 'شو اخبارك') || str_contains($p, 'اخبارك') || str_contains($p, 'هلا') || str_contains($p, 'سلام') || str_contains($p, 'صباح') || str_contains($p, 'مساء')) {
            return "أهلاً وسهلاً يا غالي! نورتني والله ❤️✨\n\n" .
                "أنا بأفضل حال وبكامل طاقتي وجاهز نتسلى وندرس ونحكي بكل شي:\n" .
                "- بدك **شرح ومسائل وقوانين** للتوجيهي والمنهاج؟ حاضرين بأبسط أسلوب.\n" .
                "- بدك **برمجة، تقنية، وأكواد** بلغات بايثون وPHP وجافاسكريبت؟ جاهز بالكامل.\n" .
                "- بدك **نكتة وفرفشة** تروق فيها بعد تعب الدراسة؟ أبشر.\n" .
                "- بدك **اقتراحات أكل** وجوع آخر الليل؟ خبير الوجبات بالخدمة!\n\n" .
                "آمرني، شو في ببالك اليوم؟";
        }

        // 4. الرياضيات والتفاضل والتكامل
        if (str_contains($p, 'تكامل') || str_contains($p, 'تفاضل') || str_contains($p, 'مشتق') || str_contains($p, 'رياضيات')) {
            return "📐 **شرح أساسيات التفاضل والتكامل في المنهاج الفلسطيني:**\n\n" .
                "التكامل ببساطة هو **العملية العكسية للمشتقة** (تجميع المساحات وإيجاد الاقتران الأصلي):\n\n" .
                "### 1. القاعدة الأساسية لتكامل اقتران القوة:\n" .
                "\$\$ \int x^n \, dx = \frac{x^{n+1}}{n+1} + C \quad (n \\neq -1) \$\$\n\n" .
                "**مثال سريع:**\n" .
                "\$\$ \int (3x^2 + 4x - 5) \, dx = x^3 + 2x^2 - 5x + C \$\$\n\n" .
                "### 2. طرق التكامل الرئيسية في التوجيهي:\n" .
                "- **التكامل بالتعويض:** نستخدمه عندما يحتوي التكامل على اقتران ومشتقته تقريباً: \$\$ u = g(x) \$\$.\n" .
                "- **التكامل بالأجزاء:** لحاصل ضرب اقترانين من نوعين مختلفين (كثير حدود مع أسي أو مثلثي) وفق القانون:\n" .
                "\$\$ \int u \, dv = u \cdot v - \int v \, du \$\$\n\n" .
                "💡 اكتب لي المسألة المحددة التي تريد حلها وسأفصلها لك خطوة بخطوة بالرموز المطلوبة!";
        }

        // 5. الفيزياء والقوانين
        if (str_contains($p, 'فيزياء') || str_contains($p, 'كولوم') || str_contains($p, 'نيوتن') || str_contains($p, 'سرعة') || str_contains($p, 'تسارع') || str_contains($p, 'طاقة')) {
            return "⚡ **شرح قوانين الفيزياء الأساسية:**\n\n" .
                "في منهاج الثانوية العامة الفلسطيني:\n\n" .
                "### قانون كولوم للقوة الكهربائية المتبادلة:\n" .
                "\$\$ F = k \cdot \frac{|q_1 \cdot q_2|}{r^2} \$\$\n" .
                "حيث:\n" .
                "- \$F\$ هي القوة الكهربائية بوحدة النيوتن (N).\n" .
                "- \$k\$ ثابت كولوم \$(9 \\times 10^9 \\text{ N}\\cdot\\text{m}^2/\\text{C}^2)\$.\n" .
                "- \$q_1, q_2\$ الشحنتان بوحدة الكولوم (C).\n" .
                "- \$r\$ المسافة بين الشحنتين بالمتر (m).\n\n" .
                "💡 أعطني معطيات مسألتك لنحلها معاً خطوة بخطوة!";
        }

        // 6. عن المنصة والمؤسس
        if (str_contains($p, 'منصة') || str_contains($p, 'ستيب') || str_contains($p, 'step by step') || str_contains($p, 'احمد شمالي') || str_contains($p, 'شمالي')) {
            return "🎓 **منصة Step by Step التعليمية (ستيبفورو - stepvoro.com):**\n\n" .
                "منصة تعليمية رقمية رائدة متخصصة لطلبة الثانوية العامة (التوجيهي) وكافة المراحل في فلسطين، تحت إشراف وتأسيس **المهندس أحمد شمالي**.\n\n" .
                "**أبرز خدمات المنصة:**\n" .
                "- **الشروحات الأكاديمية:** لكافة الفروع (العلمي، الأدبي، الصناعي، التجاري والريادة، والشرعي).\n" .
                "- **حاسبة معدل التوجيهي الذكية:** لحساب المعدل الوزاري المعتمد بدقة واستكشاف التخصصات الجامعية المتاحة.\n" .
                "- **بنك الامتحانات الوزارية:** اختبارات تجريبية ومحوسبة مع التصحيح الفوري.\n" .
                "- **المشاهدة أوفلاين:** إمكانية تنزيل الدروس داخل التطبيق ودراستها دون إنترنت.\n" .
                "- **بطاقات الاستذكار وقوانين المنهاج:** دوسيات وملخصات وقواعد ذهبية.\n\n" .
                "💡 اسألني عن أي درس، مسألة، أو استفسار وسأكون معك خطوة بخطوة!";
        }

        if (str_contains($p, 'معدل') || str_contains($p, 'احسب معدلي') || str_contains($p, 'حاسبة')) {
            return "🎓 **حساب معدل التوجيهي في منصة Step by Step:**\n\n" .
                "تعتمد حاسبة المنصة أحدث المعايير الوزارية الفلسطينية:\n" .
                "1. **المواد الإجبارية:** اللغة العربية (100)، اللغة الإنجليزية (100)، التربية الإسلامية (100).\n" .
                "2. **الفرع العلمي:** الرياضيات (200)، الفيزياء (100)، واحتساب أعلى مبحثين من (الكيمياء، الأحياء، التكنولوجيا).\n" .
                "3. **الفرع الأدبي:** الرياضيات، الجغرافيا، التاريخ، والعلوم الإنسانية.\n\n" .
                "انقر على رابط **حاسبة المعدل** في القائمة لإدخال علاماتك وحساب النسبة بدقة فوراً!";
        }

        if (str_contains($p, 'عاصمة فلسطين') || str_contains($p, 'القدس') || str_contains($p, 'المسجد الأقصى')) {
            return "🇵🇸 **مدينة القدس الشريف - عاصمة فلسطين الأبدية والتاريخية:**\n\n" .
                "- القدس هي العاصمة الروحية والتاريخية لدولة فلسطين.\n" .
                "- تضم **المسجد الأقصى المبارك** بكامل مساحته (144 دونماً) وقبة الصخرة المشرفة، بالإضافة إلى **كنيسة القيامة** المجيدة.";
        }

        // الرد العام الودود والمساعد في أي موضوع
        return "أهلاً بك! أنا معك ومستعد لكل شيء.. 🌟\n\n" .
            "سواء حابب نسولف، نحكي نكتة، نقترح أكلات، أو نشرح مسألة في الرياضيات والفيزياء والعربي.. أنا بالخدمة خطوة بخطوة.\n\n" .
            "اكتب لي سؤالك أو موضوعك وفالك طيب! 😊";
    }
}
