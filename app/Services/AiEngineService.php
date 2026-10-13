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

        // 1. فحص مفتاح Gemini مباشرة
        $geminiKey = env('GEMINI_API_KEY');
        if (empty($geminiKey)) {
            try {
                $geminiKey = Setting::get('ai_api_key', Setting::get('gemini_api_key'));
            } catch (\Throwable $e) {}
        }

        $openaiKey = env('OPENAI_API_KEY');
        if (empty($openaiKey) && empty($geminiKey)) {
            try {
                $openaiKey = Setting::get('openai_api_key');
            } catch (\Throwable $e) {}
        }

        // 1. محاولة استخدام Google Gemini (الجيل الأحدث 3.8 Flash)
        if (!empty($geminiKey)) {
            try {
                $response = $this->callGemini($geminiKey, $systemPrompt, $prompt, $imageBase64, $history, $modelKey, $attachments);
                if (!empty($response)) {
                    return [
                        'text' => $this->cleanAiResponse($response, $isFirstMessage),
                        'provider' => 'gemini',
                        'model' => 'gemini-3.8-flash',
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini AI Call Failed: ' . $e->getMessage());
            }
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

    /**
     * إرسال طلب للذكاء الاصطناعي وبث الرد بالوقت الفعلي عبر Server-Sent Events (SSE)
     *
     * @param string $prompt
     * @param string $symbolMode
     * @param string|null $imageBase64
     * @param array $history
     * @param bool $isFirstMessage
     * @param callable|null $onChunk
     * @param string|null $actorRole
     * @param string|null $modelKey
     * @param array $attachments
     * @return array
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

        $geminiKey = env('GEMINI_API_KEY');
        if (empty($geminiKey)) {
            try {
                $geminiKey = Setting::get('ai_api_key', Setting::get('gemini_api_key'));
            } catch (\Throwable $e) {}
        }

        if (!empty($geminiKey) && $onChunk !== null) {
            try {
                $streamedText = $this->callGeminiStream($geminiKey, $systemPrompt, $prompt, $imageBase64, $history, $onChunk, $modelKey, $attachments);
                if (!empty($streamedText)) {
                    return [
                        'text' => $this->cleanAiResponse($streamedText, $isFirstMessage),
                        'provider' => 'gemini',
                        'model' => 'gemini-3.8-flash',
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
        $model = ($modelKey === 'fast') ? 'gemini-3.8-flash-lite' : 'gemini-3.8-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:streamGenerateContent?alt=sse&key={$apiKey}";

        $contents = $this->buildGeminiContents($prompt, $imageBase64, $history, $attachments);

        $payload = json_encode([
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 3000,
            ],
        ]);

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

        // إذا كان النموذج الأول غير متاح أو به ضغط، تجربة gemini-3.8-flash البديل
        if ($httpCode !== 200 && $model !== 'gemini-3.8-flash') {
            return $this->callGeminiStream($apiKey, $systemPrompt, $prompt, $imageBase64, $history, $onChunk, 'smart', $attachments);
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
        $models = ($modelKey === 'fast')
            ? ['gemini-3.8-flash-lite', 'gemini-3.8-flash', 'gemini-3.5-flash']
            : ['gemini-3.8-flash', 'gemini-3.8-flash-lite', 'gemini-3.5-flash'];

        $contents = $this->buildGeminiContents($prompt, $imageBase64, $history, $attachments);

        $body = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 3000,
            ],
        ];

        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withoutVerifying()
                    ->timeout(20)
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
     * تجهيز سياق المحادثة والمرفقات لـ Gemini API
     */
    protected function buildGeminiContents(string $prompt, ?string $imageBase64, array $history, array $attachments = []): array
    {
        $contents = [];

        // إضافة سجل المحادثة السابقة
        foreach (array_slice($history, -8) as $msg) {
            $role = ($msg['role'] === 'user') ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content']]],
            ];
        }

        $currentParts = [];

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

        // معالجة أي مرفقات إضافية (مستندات PDF، صور، أو نصوص)
        foreach ($attachments as $att) {
            if (empty($att['data'])) continue;

            $mime = $att['mime'] ?? 'application/pdf';
            $data = $att['data'];
            if (preg_match('/^data:([a-zA-Z0-9\/\+]+);base64,(.+)$/', $data, $m)) {
                $mime = $m[1];
                $data = $m[2];
            }

            if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
                $currentParts[] = [
                    'inline_data' => [
                        'mime_type' => $mime,
                        'data' => $data,
                    ],
                ];
            } elseif (!empty($att['text_content'])) {
                $currentParts[] = [
                    'text' => "\n--- [محتوى الملف المرفق: " . ($att['name'] ?? 'مستند') . "] ---\n" .
                              $att['text_content'] .
                              "\n--- [نهاية الملف المرفق] ---\n"
                ];
            }
        }

        $currentParts[] = ['text' => $prompt ?: 'يرجى تحليل المرفق ومساعدتي بما يلزم.'];

        $contents[] = [
            'role' => 'user',
            'parts' => $currentParts,
        ];

        return $contents;
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
                'temperature' => 0.45,
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
     * بناء التوجيه النموذجي الصارم للذكاء الاصطناعي (System Prompt)
     */
    protected function buildSystemPrompt(string $symbolMode, bool $isFirstMessage = true, ?string $actorRole = 'student'): string
    {
        $roleTitle = match($actorRole) {
            'admin' => 'المدير العام للمنصة (Admin)',
            'teacher' => 'المعلم الأكاديمي (Teacher)',
            'videographer' => 'المصور ومحرر المحتوى (Videographer)',
            'guest' => 'زائر المنصة',
            default => 'طالب في المنصة (Student)',
        };

        $roleSpecificInstructions = match($actorRole) {
            'admin' => "أنت تتحدث حالياً مع **إدارة المنصة (Admin)**:\n- ساعد الإدارة في صياغة الإعلانات، القرارات، والتوجيهات الأكاديمية والتربوية.\n- قدم أفكاراً تنظيمية لتطوير المنصة، وتنسيق الفصول والمواد والاختبارات.",
            'teacher' => "أنت تتحدث حالياً مع **أحد المعلمين الأكاديميين (Teacher)**:\n- ساعد المعلم في صياغة أسئلة امتحانات مميزة (مقالية وموضوعية اختيار من متعدد) مع إجابات نموذجية وسلالم تصحيح (Rubrics).\n- ساعده في إعداد خطط الدروس والتحضير الأكاديمي وتلخيص الفصول لطلابه بطرق جذابة ومبسطة.",
            'videographer' => "أنت تتحدث حالياً مع **المصور ومحرر المحتوى المرئي (Videographer)**:\n- ساعده في كتابة نصوص تقديمية (Scripts) للمحاضرات ومقدمات وفواصل الدروس.\n- قدم نصائح حول تقطيع المحاضرات إلى أجزاء ذكية (Chapters) وضبط عناصر الإضاءة والصوت وتنسيق الشاشة الذكية لخدمة الشرح.",
            default => "أنت تتحدث حالياً مع **طالب في المنصة (Student)**:\n- كن معلماً صبوراً، مشجعاً، ومنظماً يوضح الأفكار خطوة بخطوة من البسيط إلى المعقد.\n- فكك المسائل الرياضية والفيزيائية إلى معطيات، ومطلوب، وقوانين، وخطوات تعويض، ونتيجة نهائية مع الوحدات.",
        };

        $greetingRule = $isFirstMessage
            ? "1. **الرسالة الأولى:** يمكنك الترحيب بلطف وبإيجاز شديد باسم المساعد 'Step by Step Ai'، ثم الدخول فوراً في الإجابة."
            : "1. **رسائل المتابعة:** ممنوع كتابة أي ديباجة ترحيبية أو تكرار لاسمك. ادخل فوراً وبشكل مباشر 100% في إجابة السؤال دون مقدمات.";

        return <<<PROMPT
أنت "Step by Step Ai"، المساعد الأكاديمي والذكي الشامل لمنصة "Step by Step" التعليمية الرائدة (ستيب / ستيبفورو - stepvoro.com)، ومقرها فلسطين الحبيبة وعاصمتها الأبدية القدس الشريف، بإشراف وتأسيس المشرف العام المهندس أحمد شمالي (م. أحمد شمالي).

المخاطَب الحالي: {$roleTitle}
{$roleSpecificInstructions}

معلوماتك العامة المعتمدة عن منصة Step by Step:
- منصة "Step by Step" هي المنصة التعليمية الرقمية الشاملة لطلبة الثانوية العامة (التوجيهي) وكافة المراحل التعليمية بفروعها (العلمي، الأدبي، الصناعي، التجاري والريادة، والشرعي).
- تقدم المنصة:
  1. شروحات مرئية تفصيلية للمنهاج الفلسطيني مع كوكبة من خيرة المعلمين.
  2. حاسبة معدل التوجيهي الوزارية المعتمدة الدقيقة المتاحة عبر المنصة لحساب المعدل وتنسيق القبول الجامعي.
  3. بنك الامتحانات الوزارية والتجريبية المحوسبة مع التصحيح الفوري والحل النموذجي.
  4. ميزة مشاهدة وتنزيل الفيديوهات أوفلاين (بدون إنترنت) داخل التطبيق والموقع لدراستها في أي وقت دون استهلاك باقة الإنترنت.
  5. بطاقات الاستذكار السريع (Flashcards) والدوسيات والملخصات ودليل القوانين الذهبية.
  6. التسجيل والتواصل الرسمي عبر صفحات المنصة الرسمية ومسارات الدعم.

قاعدة الخصوصية والأمان الصارمة والقطعية (Strict Zero Data Leakage):
- أنت مساعد أكاديمي وعلمي فقط، وليس لديك أي وصول إطلاقاً إلى قاعدة البيانات الخاصة بمستخدمي المنصة، أو أرقام الهواتف، أو عناوين البريد الإلكتروني، أو كلمات المرور، أو الحسابات والرسوم المالية، أو علامات وسجلات الطلاب الفردية.
- ممنوع منعاً باتاً وقطعياً محاولة تخمين أو إفشاء أي معلومة شخصية أو حساب لأي طالب أو مستخدم.
- إذا سأل أي مستخدم عن بيانات حساب شخصي أو أرقام هواتف أو استفسار مالي فردي، اعتذر بأدب واشرح أنك لا تملك صلاحية الوصول إلى البيانات الشخصية حرصاً على سرية وخصوصية الطلبة، ووجهه للتواصل مع إدارة المنصة الرسمية عبر صفحة الدعم والاتصال.

قواعد التحية والمقدمات:
{$greetingRule}

قواعد كتابة الرياضيات والفيزياء (معادلات LTR واضحة):
1. كل معادلة أو قانون رياضي أو فيزيائي أو كيميائي مستقل يجب أن يوضع في سطر خاص بين علامتي دولار مزدوجتين: `\$\$ ... \$\$` بصيغة LaTeX القياسية (مثل: `\$\$ F = m \cdot a \$\$` أو `\$\$ \int_a^b f(x)\,dx \$\$`).
2. الرموز الرياضية والأرقام المضمنة في السطر تُوضع بين علامتي دولار مفردتين: `\$x\$`, `\$y=5\$`.
3. المعادلات والصيغ الرياضية تُكتب دائماً بالاتجاه العلمي القياسي من اليسار إلى اليمين (LTR).
4. في اللغة العربية والنحو والبلاغة: ممنوع استخدام علامات الدولار \$ نهائياً لأي كلمة أو حرف عربي! تُكتب الكلمات والحروف العربية بصيغة نصية عادية بين أقواس مثل (و) أو علامات اقتباس.

قواعد الأكواد والبرمجة:
- عند كتابة أي كود برمجي، ضعه داخل Code Block مع تحديد اسم اللغة في البداية (مثل ```python أو ```javascript أو ```php أو ```cpp).
- قدم كوداً نظيفاً، موثقاً بالتعليقات عند الحاجة، مع شرح مختصر للخطوات.

المنهجية التعليمية:
- في حل المسائل: حدد المعطيات -> القوانين المستخدمة -> خطوات التعويض الحسابية -> النتيجة مع وحدات القياس.
- في التلخيص: رتب الأفكار بنقاط ورؤوس أقلام واضحة وجداول مقارنة أنيقة.
- في إنشاء الاختبارات: اعرض الأسئلة مقسمة، مع إمكانية إظهار الإجابات النموذجية بعد الحل لتثبيت الفهم.
- في حال عدم التأكد من معلومة، لا تخترع إجابات وهمية واذكر الدقة بموضوعية.
PROMPT;
    }

    /**
     * تنظيف وضبط استجابة الذكاء الاصطناعي وإزالة أي تكرار للديباجة بعد الرسالة الأولى
     */
    protected function cleanAiResponse(string $text, bool $isFirstMessage): string
    {
        $text = trim($text);
        if ($isFirstMessage) {
            return $text;
        }

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

        $result = trim(implode("\n", $cleanedLines));

        // إزالة أي علامات دولار وضعت بالخطأ حول كلمات عربية
        $result = preg_replace_callback('/\$\$\s*([\x{0600}-\x{06FF}\s\p{M}]+)\s*\$\$/u', function($m) {
            return trim($m[1]);
        }, $result);

        $result = preg_replace_callback('/\$([\x{0600}-\x{06FF}\s\p{M}]+)\$/u', function($m) {
            return trim($m[1]);
        }, $result);

        $result = preg_replace('/\(\s*([\x{0600}-\x{06FF}])\s*\)/u', '($1)', $result);

        return !empty($result) ? $result : $text;
    }

    /**
     * المحرك الأكاديمي الداخلي البديل في حال غياب الاتصال الخارجي بالكامل
     */
    protected function generateIntelligentFallbackResponse(string $prompt, string $symbolMode, bool $hasImage): string
    {
        $p = mb_strtolower(trim($prompt));

        if (str_contains($p, 'منصة') || str_contains($p, 'ستيب') || str_contains($p, 'step by step') || str_contains($p, 'احمد شمالي') || str_contains($p, 'شمالي')) {
            return "🎓 **منصة Step by Step التعليمية (ستيبفورو - stepvoro.com):**\n\n" .
                "منصة تعليمية رقمية رائدة متخصصة لطلبة الثانوية العامة (التوجيهي) وكافة المراحل في فلسطين، تحت إشراف وتأسيس **المهندس أحمد شمالي**.\n\n" .
                "**أبرز خدمات المنصة:**\n" .
                "- **الشروحات الأكاديمية:** لكافة الفروع (العلمي، الأدبي، الصناعي، التجاري والريادة، والشرعي).\n" .
                "- **حاسبة معدل التوجيهي الذكية:** لحساب المعدل الوزاري المعتمد بدقة واستكشاف التخصصات الجامعية المتاحة.\n" .
                "- **بنك الامتحانات الوزارية:** اختبارات تجريبية ومحوسبة مع التصحيح الفوري.\n" .
                "- **المشاهدة أوفلاين:** إمكانية تنزيل الدروس داخل التطبيق ودراستها دون إنترنت.\n" .
                "- **بطاقات الاستذكار وقوانين المنهاج:** دوسيات وملخصات وقواعد ذهبية.\n\n" .
                "💡 يمكنك سؤالي عن أي درس أو مسألة وسأفصلها لك خطوة بخطوة!";
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

        return "### 📌 التحليل والشرح الأكاديمي المنهجي:\n\n" .
            "1. **المفهوم العلمي والتربوي:** يرتبط موضوع سؤالك بالقواعد الأساسية للمنهاج الفلسطيني.\n" .
            "2. **خطوات الحل والمتابعة:**\n" .
            "   - حدد المعطيات بدقة وعيّن المطلوب.\n" .
            "   - طبّق القوانين المباشرة مع مراعاة الوحدات العلمية.\n" .
            "   - قارن النتيجة مع نماذج الامتحانات الوزارية المعتمدة.\n\n" .
            "🌟 أنا جاهز لتفصيل أي مسألة، تلخيص، أو كود برمجي تحتاجه فوراً!";
    }
}
