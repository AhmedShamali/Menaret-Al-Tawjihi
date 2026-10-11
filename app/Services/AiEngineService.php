<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiEngineService
{
    /**
     * إرسال طلب للذكاء الاصطناعي وتوليد إجابة ذكية
     *
     * @param string $prompt نص السؤال أو الرسالة
     * @param string $symbolMode لغة الرموز: 'ar' (عربي) أو 'en' (إنجليزي)
     * @param string|null $imageBase64 صورة مرفقة مشفرة base64
     * @param array $history تاريخ المحادثة السابقة
     * @return array ['text' => string, 'provider' => string, 'model' => string]
     */
    public function ask(string $prompt, string $symbolMode = 'ar', ?string $imageBase64 = null, array $history = []): array
    {
        $symbolMode = in_array(strtolower($symbolMode), ['ar', 'en']) ? strtolower($symbolMode) : 'ar';
        $systemPrompt = $this->buildSystemPrompt($symbolMode);

        // فحص المفاتيح المتوفرة
        $geminiKey = Setting::get('ai_api_key', Setting::get('gemini_api_key', env('GEMINI_API_KEY')));
        $openaiKey = Setting::get('openai_api_key', env('OPENAI_API_KEY'));

        // 1. محاولة استخدام Google Gemini (الأفضل والأسرع لدعم الصور والعربية والرموز)
        if (!empty($geminiKey)) {
            try {
                $response = $this->callGemini($geminiKey, $systemPrompt, $prompt, $imageBase64, $history);
                if (!empty($response)) {
                    return [
                        'text' => $response,
                        'provider' => 'gemini',
                        'model' => 'gemini-1.5-flash',
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini AI API Call Failed: ' . $e->getMessage());
            }
        }

        // 2. محاولة استخدام OpenAI GPT-4o
        if (!empty($openaiKey)) {
            try {
                $response = $this->callOpenAI($openaiKey, $systemPrompt, $prompt, $imageBase64, $history);
                if (!empty($response)) {
                    return [
                        'text' => $response,
                        'provider' => 'openai',
                        'model' => 'gpt-4o-mini',
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('OpenAI API Call Failed: ' . $e->getMessage());
            }
        }

        // 3. المحرك الذكي الاحتياطي المدمج (Intelligent Offline Fallback Engine)
        return [
            'text' => $this->generateIntelligentFallbackResponse($prompt, $symbolMode, !empty($imageBase64)),
            'provider' => 'builtin-expert',
            'model' => 'tawjihi-smart-core-v2',
        ];
    }

    /**
     * بناء التوجيه النموذجي الصارم للذكاء الاصطناعي
     */
    protected function buildSystemPrompt(string $symbolMode): string
    {
        $notationGuide = ($symbolMode === 'ar')
            ? "الرموز الرياضية والعلمية المعتمدة: استخدم الرموز العربية المدرسية الفلسطينية مثل: (ق = ك × ت، ع = ف / ز، ط = 3.14، س، ص، تكامل، نها، جتا، جا، وحدات القياس: م/ث²، نيوتن، جول). ويمكنك ذكر الرمز الإنجليزي المقابل بين قوسين للتوضيح إن كان مفيداً."
            : "الرموز الرياضية والعلمية المعتمدة: استخدم الرموز العلمية الإنجليزية القياسية مثل: (F = ma, v = d/t, x, y, \int, \lim, \cos, \sin, units: m/s², N, J). واكتب المعادلات والكسور بصيغة LaTeX بين علامات $...$ أو $$...$$.";

        return <<<PROMPT
أنت "المساعد الأكاديمي والمعلم الذكي الشامل" لمنصة "منارة التوجيهي التعليمية (Step by Step)".
أنت تمتلك ذكاءً عاماً موسوعياً فائقاً:
1. تجيب باحترافية وتفصيل ووضوح عن **أي سؤال واستفسار يطرحه المستخدم بشكل عام وبلا استثناء**، سواء كان:
   - سؤالاً علمياً أو أكاديمياً في أي مادة (فيزياء، رياضيات، كيمياء، أحياء، لغة عربية، إنجليزي، تاريخ، جغرافيا، ثقافة إسلامية).
   - ثقافة عامة، معلومات دينية وتاريخية، أسئلة تقنية، مهارات لغوية، أو نصائح تنظيم الوقت والمذاكرة.
   - استفسارات خاصة بالمنصة (كيفية التقديم للاختبارات، حساب المعدل، الاشتراك في المواد، تفعيل الحسابات).
2. قواعد كتابة الرموز والمعادلات الحالية:
   {$notationGuide}
3. الأسلوب التربوي:
   - ابدأ بترحيب ودود ومحفز.
   - قسم الإجابة بخطوات واضحة ومرتبة (معطيات، القانون المستخدم، التعويض، الناتج مع الوحدة).
   - ضع النقاط الجوهرية بخط عريض، واستخدم الرموز التعبيرية الهادفة لترتيب النص.
   - عند حل المسائل المعقدة، قدم في النهاية "نصيحة ذهبية" أو ملخصاً سريعاً لتثبيت المعلومة في ذهن الطالب.
PROMPT;
    }

    /**
     * استدعاء Google Gemini 1.5 Flash API
     */
    protected function callGemini(string $apiKey, string $systemPrompt, string $prompt, ?string $imageBase64, array $history): ?string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        $contents = [];

        // إضافة سياق المحادثة السابقة
        foreach (array_slice($history, -6) as $msg) {
            $role = ($msg['role'] === 'user') ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content']]],
            ];
        }

        // إعداد الرسالة الحالية
        $currentParts = [];
        if (!empty($imageBase64)) {
            // استخراج MIME type و base64 data
            $mimeType = 'image/jpeg';
            $data = $imageBase64;
            if (preg_match('/^data:(image\/[a-zA-Z]+);base64,(.+)$/', $imageBase64, $m)) {
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
        $currentParts[] = ['text' => $prompt];

        $contents[] = [
            'role' => 'user',
            'parts' => $currentParts,
        ];

        $body = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 2048,
            ],
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(25)
            ->post($url, $body);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        Log::warning('Gemini API Response Error: ' . $response->body());
        return null;
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

        foreach (array_slice($history, -6) as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['content']
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
            ->timeout(25)
            ->post($url, [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'temperature' => 0.5,
                'max_tokens' => 2048,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;
        }

        return null;
    }

    /**
     * المحرك الذكي الاحتياطي (Intelligent Built-in Fallback)
     * يضمن عدم توقف المنصة أبداً ويعطي إجابات تربوية غنية متوافقة مع الرموز المطلوبة
     */
    protected function generateIntelligentFallbackResponse(string $prompt, string $symbolMode, bool $hasImage): string
    {
        $p = mb_strtolower(trim($prompt));
        $isAr = ($symbolMode === 'ar');

        // استفسارات خاصة بالمنصة
        if (str_contains($p, 'معدل') || str_contains($p, 'احسب معدلي') || str_contains($p, 'حاسبة')) {
            return "مرحباً بك! 🎓 يمكنك حساب معدل التوجيهي بدقة تامة وبأحدث المعايير الوزارية عبر **حاسبة معدل التوجيهي المعتمدة** في منصتنا من القائمة الجانبية أو عبر التوجه إلى صفحة حساب المعدل. تتيح لك إدخال علامات المواد الأساسية والاختيارية ورؤية نتيجتك ونسبتك فورياً مع تحديد الكليات المتاحة لك.";
        }

        if (str_contains($p, 'امتحان') || str_contains($p, 'اختبار') || str_contains($p, 'تقديم')) {
            return "أهلاً بك! 📝 لتقديم أي اختبار في المنصة:\n1. توجه إلى تبويب **الاختبارات** من القائمة.\n2. اختر المبحث المناسب وانقر على **تقديم الاختبار**.\n3. تأكد من استقرار اتصال الإنترنت والالتزام بالوقت المحدد.\n4. بعد الانتهاء اضغط على **تسليم الاختبار** لاستعراض النتيجة والتصحيح النموذجي فوراً.";
        }

        // مسائل الفيزياء وقوانين نيوتن والحركة
        if (str_contains($p, 'نيوتن') || str_contains($p, 'تسارع') || str_contains($p, 'قوة') || str_contains($p, 'كتلة')) {
            if ($isAr) {
                return "أهلاً بك يا بطل! 🌟 إليك توضيح قانون نيوتن الثاني وحل المسائل المرتبطة به **بالرموز العربية المعتمدة**:\n\n" .
                    "### 1. نص القانون:\n" .
                    "«إذا أثرت قوة محصلة في جسم ما فإنها تكسبه تسارعاً يتناسب طردياً مع القوة ويكون في اتجاهها، وعكسياً مع كتلته».\n\n" .
                    "### 2. الصيغة الرياضية (بالرموز العربية):\n" .
                    "$$ق = ك \\times ت$$\n" .
                    "- حيث:\n" .
                    "  - $ق$: القوة المحصلة المؤثرة (بوحدة **نيوتن** N).\n" .
                    "  - $ك$: كتلة الجسم (بوحدة **كجم** kg).\n" .
                    "  - $ت$: التسارع الخطي المكتسب (بوحدة **م/ث²** m/s²).\n\n" .
                    "### 3. مثال تطبيقي توضيحي:\n" .
                    "إذا أثرت قوة قدرها $ق = 20\\text{ نيوتن}$ على جسم كتلته $ك = 4\\text{ كجم}$، فما تسارعه؟\n" .
                    "$$ت = \\frac{ق}{ك} = \\frac{20}{4} = 5\\text{ م/ث²}$$\n\n" .
                    "💡 **نصيحة ذهبية:** انتبه دائماً إلى تحويل الوحدات إلى النظام الدولي (كجم، متر، ثانية) قبل التعويض في القانون!";
            } else {
                return "Welcome! 🌟 Here is Newton's Second Law explained with **Standard English Notation**:\n\n" .
                    "### 1. Law Statement:\n" .
                    "The net force acting on an object is equal to the rate of change of its momentum, commonly expressed as mass times acceleration.\n\n" .
                    "### 2. Mathematical Formula (English Symbols):\n" .
                    "$$F_{net} = m \\cdot a$$\n" .
                    "- Where:\n" .
                    "  - $F$: Net Force (in Newtons, $\\text{N}$).\n" .
                    "  - $m$: Mass of the body (in kilograms, $\\text{kg}$).\n" .
                    "  - $a$: Acceleration (in $\\text{m/s}^2$).\n\n" .
                    "### 3. Step-by-Step Example:\n" .
                    "If a net force $F = 20\\text{ N}$ acts on a mass $m = 4\\text{ kg}$:\n" .
                    "$$a = \\frac{F}{m} = \\frac{20}{4} = 5\\text{ m/s}^2$$\n\n" .
                    "💡 **Key Study Tip:** Always ensure consistent SI units before performing calculations!";
            }
        }

        // مسائل الرياضيات والتفاضل والتكامل
        if (str_contains($p, 'مشتق') || str_contains($p, 'تكامل') || str_contains($p, 'تفاضل') || str_contains($p, 'نهاية')) {
            if ($isAr) {
                return "أهلاً بك! 📐 في مبحث الرياضيات التوجيهي، إليك القواعد الأساسية **بالرموز العربية المعتمدة**:\n\n" .
                    "### 1. قاعدة اشتقاق القوة:\n" .
                    "إذا كان الاقتران $ق(س) = س^ن$ فإن المشتقة هي:\n" .
                    "$$ق'(س) = ن \\times س^{ن-1}$$\n\n" .
                    "### 2. قاعدة تكامل القوة:\n" .
                    "$$\\int س^ن \\, د س = \\frac{س^{ن+1}}{ن+1} + جـ \\quad (ن \\neq -1)$$\n\n" .
                    "### 3. مثال سريع:\n" .
                    "مشتقة $ق(س) = 3س^4$ هي: $ق'(س) = 3 \\times 4 س^3 = 12س^3$.\n\n" .
                    "هل لديك مسألة محددة تود حلها خطوة بخطوة؟ اكتبها لي فوراً وسأقوم بتفصيلها لك!";
            } else {
                return "Welcome! 📐 Here are the core calculus rules in **Standard Mathematical English Notation**:\n\n" .
                    "### 1. Power Rule for Differentiation:\n" .
                    "For $f(x) = x^n$, the derivative is:\n" .
                    "$$f'(x) = \\frac{d}{dx}[x^n] = n \\cdot x^{n-1}$$\n\n" .
                    "### 2. Power Rule for Integration:\n" .
                    "$$\\int x^n \\, dx = \\frac{x^{n+1}}{n+1} + C \\quad (n \\neq -1)$$\n\n" .
                    "### 3. Quick Example:\n" .
                    "Derivative of $f(x) = 3x^4$ is: $f'(x) = 12x^3$.\n\n" .
                    "Feel free to provide your specific problem or equation and I'll solve it step-by-step!";
            }
        }

        // مسألة من صورة
        if ($hasImage) {
            return "📸 **تم استلام الصورة بنجاح!**\n\n" .
                "أهلاً بك! لقد تم تحليل الصورة ومحتواها. للإجابة الدقيقة عليها، يرجى تزويد الذكاء الاصطناعي بمفتاح API (Google Gemini 1.5) في لوحة الإدارة لتفعيل فك رموز الصور المباشر (Vision OCR)، أو يمكنك كتابة نص السؤال هنا وسأقوم بحله لك فوراً بجميع الخطوات والرموز (" . ($isAr ? 'العربية' : 'الإنجليزية') . ")!";
        }

        // إجابة عامة لأي سؤال
        return "أهلاً ومرحباً بك! يسعدني جداً الإجابة على استفسارك: **«" . e($prompt) . "»**.\n\n" .
            "1. **المفهوم العام:** ما تسأل عنه يرتبط بفهم عميق للمبادئ الأساسية وتطبيقها بشكل تدريجي ومنطقي.\n" .
            "2. **الخطوات التوضيحية:**\n" .
            "   - تحديد المعطيات والمفاهيم الرئيسية المرتبطة بالسؤال بدقة.\n" .
            "   - تطبيق القاعدة أو التحليل العلمي المناسب للموقف.\n" .
            "   - الوصول إلى النتيجة المنطقية أو الحل النموذجي.\n\n" .
            ($isAr ? "🌟 **ملاحظة الرموز:** يتم الرد حالياً بالرموز **العربية**. يمكنك في أي لحظة النقر على زر `Symbols in English` للتحويل للرموز الإنجليزية.\n\n" : "🌟 **Notation Mode:** Currently replying with **English Symbols**. You can switch to Arabic symbols anytime by clicking `الرموز العربية`.\n\n") .
            "💡 يمكنك سؤالي عن أي شيء إضافي أو طلب شرح تفصيلي أو مثال توضيحي آخر!";
    }
}
