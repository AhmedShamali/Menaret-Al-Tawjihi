<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\AiEngineService;
use App\Support\CurrentActor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiTutorController extends Controller
{
    protected AiEngineService $aiEngine;

    public function __construct(AiEngineService $aiEngine)
    {
        $this->aiEngine = $aiEngine;
    }

    /**
     * جلب الممثل الحالي بدقة (طالب، مدير، معلم، مصور، أو زائر)
     */
    protected function getActor(): array
    {
        $student = CurrentActor::student() ?? auth('student')->user();
        if ($student) {
            return [
                'type' => 'student',
                'id' => $student->id,
                'name' => $student->name ?? 'طالب المنصة',
                'role' => 'student',
                'avatar' => $student->avatar ?? null,
            ];
        }

        $staff = CurrentActor::staff() ?? auth()->user();
        if ($staff) {
            $role = $staff->role ?? 'user';
            return [
                'type' => 'user',
                'id' => $staff->id,
                'name' => $staff->name ?? 'مستخدم المنصة',
                'role' => $role, // 'admin', 'teacher', 'videographer'
                'avatar' => $staff->photo ?? null,
            ];
        }

        // دعم الزوار قبل تسجيل الدخول
        $guestSessionId = session()->getId() ?: 'guest_' . (request()->ip() ?? 'local');
        $guestId = abs(crc32($guestSessionId));
        return [
            'type' => 'guest',
            'id' => $guestId,
            'name' => 'طالب / زائر المنصة',
            'role' => 'guest',
            'avatar' => null,
        ];
    }

    /**
     * فحص فوري وسريع لإمكانية الاتصال بقاعدة البيانات
     */
    protected function isDbAvailable(): bool
    {
        static $available = null;
        if ($available !== null) {
            return $available;
        }

        $pgsqlHost = config('database.connections.pgsql.host', '');
        if (str_starts_with($pgsqlHost, 'dpg-') && (!isset($_SERVER['RENDER']) || app()->isLocal() || !app()->isProduction())) {
            return $available = false;
        }

        try {
            DB::connection()->getPdo();
            return $available = true;
        } catch (\Throwable $e) {
            return $available = false;
        }
    }

    /**
     * الصفحة الرئيسية للمساعد الذكي Step by Step Ai
     */
    public function index(Request $request)
    {
        $actor = $this->getActor();
        $conversations = [];

        if ($this->isDbAvailable()) {
            try {
                $conversations = AiConversation::where('actor_type', $actor['type'])
                    ->where('actor_id', $actor['id'])
                    ->with('latestMessage')
                    ->latest()
                    ->take(50)
                    ->get();
            } catch (\Throwable $e) {
                Log::info('AI Conversations fetch note: ' . $e->getMessage());
            }
        }

        if (empty($conversations) && !$this->isDbAvailable()) {
            $sessionKey = 'ai_session_conversations_' . $actor['type'] . '_' . $actor['id'];
            $sessionList = session()->get($sessionKey, []);
            $conversations = array_values($sessionList);
        }

        // تصنيف المحادثات حسب التاريخ (Today, Yesterday, 7 Days, Older)
        $groupedConversations = $this->groupConversationsByDate($conversations);

        return view('student.ai.hub', compact('actor', 'conversations', 'groupedConversations'));
    }

    /**
     * تصنيف المحادثات زمنياً
     */
    protected function groupConversationsByDate($conversations): array
    {
        $grouped = [
            'today' => [],
            'yesterday' => [],
            'previous_7_days' => [],
            'older' => [],
        ];

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $yesterdayStart = $now->copy()->subDay()->startOfDay();
        $sevenDaysAgo = $now->copy()->subDays(7)->startOfDay();

        foreach ($conversations as $c) {
            $date = null;
            if (is_object($c) && isset($c->created_at)) {
                $date = $c->created_at instanceof Carbon ? $c->created_at : Carbon::parse($c->created_at);
            } elseif (is_array($c) && isset($c['created_at'])) {
                $date = Carbon::parse($c['created_at']);
            } else {
                $date = $now;
            }

            if ($date >= $todayStart) {
                $grouped['today'][] = $c;
            } elseif ($date >= $yesterdayStart) {
                $grouped['yesterday'][] = $c;
            } elseif ($date >= $sevenDaysAgo) {
                $grouped['previous_7_days'][] = $c;
            } else {
                $grouped['older'][] = $c;
            }
        }

        return $grouped;
    }

    /**
     * معالجة الملفات المرفقة واستخراج نصوصها وبياناتها Base64
     */
    protected function extractAttachments(Request $request): array
    {
        $attachments = [];

        // 1. ملف فردي عبر 'file' أو 'document'
        $singleFile = $request->file('file') ?? $request->file('document');
        if ($singleFile && $singleFile->isValid()) {
            $attachments[] = $this->processUploadedFile($singleFile);
        }

        // 2. ملفات متعددة عبر 'files'
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file && $file->isValid()) {
                    $attachments[] = $this->processUploadedFile($file);
                }
            }
        }

        // 3. مستند Base64 مرسل مباشرة
        if ($request->filled('attachment_base64')) {
            $attachments[] = [
                'name' => $request->input('attachment_name', 'مستند مرفق'),
                'mime' => $request->input('attachment_mime', 'application/pdf'),
                'data' => $request->input('attachment_base64'),
                'text_content' => null,
            ];
        }

        return array_filter($attachments);
    }

    /**
     * معالجة ملف مرفوع فردي
     */
    protected function processUploadedFile($file): ?array
    {
        $name = $file->getClientOriginalName();
        $mime = $file->getMimeType();
        $ext = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        // فحص الأحجام (الحد الأقصى 20 ميغابايت)
        if ($file->getSize() > 20 * 1024 * 1024) {
            return null;
        }

        // للملفات النصية والبرمجية، نقرأ المحتوى نصياً
        $textExtensions = ['txt', 'csv', 'json', 'py', 'js', 'php', 'html', 'css', 'md', 'sql', 'cpp', 'c', 'java', 'xml'];
        if (in_array($ext, $textExtensions) || str_starts_with($mime, 'text/')) {
            $textContent = @file_get_contents($path);
            if ($textContent !== false) {
                return [
                    'name' => $name,
                    'mime' => 'text/plain',
                    'data' => null,
                    'text_content' => mb_substr($textContent, 0, 50000), // أول 50 ألف حرف
                ];
            }
        }

        // للصور وملفات PDF، نشفرها Base64 لـ Gemini Multimodal
        $content = @file_get_contents($path);
        if ($content !== false) {
            $base64 = 'data:' . $mime . ';base64,' . base64_encode($content);
            return [
                'name' => $name,
                'mime' => $mime,
                'data' => $base64,
                'text_content' => null,
            ];
        }

        return null;
    }

    /**
     * إرسال رسالة واستقبال الرد المتزامن (Chat POST)
     */
    public function chat(Request $request)
    {
        $actor = $this->getActor();

        $request->validate([
            'prompt' => 'nullable|string|max:8000',
            'conversation_id' => 'nullable',
            'symbol_mode' => 'nullable|string|in:ar,en',
            'model' => 'nullable|string|in:smart,fast,balanced',
            'image' => 'nullable',
            'is_first_message' => 'nullable|boolean',
            'history' => 'nullable|array',
        ]);

        $prompt = trim((string)$request->input('prompt', ''));
        $symbolMode = $request->input('symbol_mode', 'ar');
        $modelKey = $request->input('model', 'smart');
        $imageBase64 = null;

        // معالجة الصورة المرفوعة
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid() && $file->getSize() <= 10 * 1024 * 1024) {
                $data = file_get_contents($file->getRealPath());
                $mime = $file->getMimeType();
                $imageBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        } elseif ($request->filled('image_base64')) {
            $imageBase64 = $request->input('image_base64');
        }

        // معالجة المرفقات الأخرى
        $attachments = $this->extractAttachments($request);

        if (empty($prompt) && empty($imageBase64) && empty($attachments)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى كتابة استفسار أو إرفاق ملف/صورة للمعاينة.',
            ], 422);
        }

        if (empty($prompt)) {
            $prompt = !empty($imageBase64)
                ? 'يرجى فحص الصورة المرفقة وتحليل محتواها وإجابتي بالخطوات التفصيلية.'
                : 'يرجى قراءة الملف المرفق وتلخيصه واستخراج أهم النقاط منه.';
        }

        $conversation = null;
        $history = [];
        $isFirstMessage = $request->boolean('is_first_message', true);

        // جلب أو إنشاء المحادثة الخاصة بهذا المستخدم تحديداً
        if ($this->isDbAvailable()) {
            try {
                if ($request->filled('conversation_id')) {
                    $conversation = AiConversation::where('id', $request->conversation_id)
                        ->where('actor_type', $actor['type'])
                        ->where('actor_id', $actor['id'])
                        ->first();
                }

                if (!$conversation) {
                    $title = $this->extractSmartTitle($prompt);
                    $conversation = AiConversation::create([
                        'actor_type' => $actor['type'],
                        'actor_id' => $actor['id'],
                        'title' => $title,
                        'symbol_mode' => $symbolMode,
                    ]);
                    $isFirstMessage = true;
                } else {
                    if ($request->filled('symbol_mode')) {
                        $conversation->update(['symbol_mode' => $symbolMode]);
                    }
                }

                $messages = $conversation->messages()->latest('id')->take(20)->get()->reverse();
                if ($messages->isNotEmpty()) {
                    $isFirstMessage = false;
                    foreach ($messages as $msg) {
                        $history[] = ['role' => $msg->role, 'content' => $msg->content];
                    }
                }

                AiMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'user',
                    'content' => $prompt,
                    'image_url' => $imageBase64 ? 'attached' : null,
                    'notation_mode' => $symbolMode,
                ]);
            } catch (\Throwable $e) {
                Log::info('AI Chat DB notice: ' . $e->getMessage());
            }
        }

        // السجل الاحتياطي من الجلسة في حال عدم توفر القاعدة
        if (empty($history)) {
            $clientHistory = $request->input('history');
            if (is_array($clientHistory) && !empty($clientHistory)) {
                $history = $clientHistory;
                $isFirstMessage = false;
            } else {
                $sessionKey = 'ai_chat_hist_' . $actor['type'] . '_' . ($request->input('conversation_id') ?: session()->getId());
                $sessHist = session()->get($sessionKey, []);
                if (!empty($sessHist)) {
                    $history = $sessHist;
                    $isFirstMessage = false;
                }
            }
        }

        // استدعاء محرك الذكاء مع تمرير دور المستخدم والنموذج والمرفقات
        $result = $this->aiEngine->ask(
            $prompt,
            $symbolMode,
            $imageBase64,
            $history,
            $isFirstMessage,
            $actor['role'],
            $modelKey,
            $attachments
        );
        $aiReply = $result['text'];

        // حفظ في الجلسة
        $convIdForSession = $conversation?->id ?: ($request->input('conversation_id') ?: time());
        $sessionKey = 'ai_chat_hist_' . $actor['type'] . '_' . $convIdForSession;
        $sessionHist = $history;
        $sessionHist[] = ['role' => 'user', 'content' => $prompt];
        $sessionHist[] = ['role' => 'assistant', 'content' => $aiReply];
        session()->put($sessionKey, array_slice($sessionHist, -20));

        // حفظ رد الذكاء بالقاعدة
        if ($conversation && $this->isDbAvailable()) {
            try {
                AiMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $aiReply,
                    'notation_mode' => $symbolMode,
                ]);
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'reply' => $aiReply,
            'conversation_id' => $conversation?->id ?: $convIdForSession,
            'conversation_title' => $conversation?->title ?: $this->extractSmartTitle($prompt),
            'symbol_mode' => $symbolMode,
            'provider' => $result['provider'],
            'model' => $result['model'],
        ]);
    }

    /**
     * إرسال وبث رد الذكاء الاصطناعي بالوقت الفعلي عبر Server-Sent Events (SSE)
     */
    public function chatStream(Request $request)
    {
        $actor = $this->getActor();
        $prompt = trim((string)$request->input('prompt', ''));
        $symbolMode = $request->input('symbol_mode', 'ar');
        $modelKey = $request->input('model', 'smart');
        $imageBase64 = $request->input('image_base64');
        $isFirstMessage = $request->boolean('is_first_message', true);

        $attachments = $this->extractAttachments($request);

        if (empty($prompt) && empty($imageBase64) && empty($attachments)) {
            return response()->json(['success' => false, 'message' => 'الرجاء إدخال نص السؤال أو إرفاق ملف/صورة'], 422);
        }

        if (empty($prompt)) {
            $prompt = !empty($imageBase64)
                ? 'يرجى فحص الصورة المرفقة وتحليل محتواها وإجابتي بالخطوات التفصيلية.'
                : 'يرجى قراءة الملف المرفق وتلخيصه واستخراج أهم النقاط منه.';
        }

        $conversation = null;
        $history = [];
        $conversationId = $request->input('conversation_id');

        if ($this->isDbAvailable()) {
            try {
                if ($conversationId) {
                    $conversation = AiConversation::where('id', $conversationId)
                        ->where('actor_type', $actor['type'])
                        ->where('actor_id', $actor['id'])
                        ->first();
                }

                if (!$conversation) {
                    $title = $this->extractSmartTitle($prompt);
                    $conversation = AiConversation::create([
                        'actor_type' => $actor['type'],
                        'actor_id' => $actor['id'],
                        'title' => $title,
                        'symbol_mode' => $symbolMode,
                    ]);
                    $isFirstMessage = true;
                } else {
                    if ($request->filled('symbol_mode')) {
                        $conversation->update(['symbol_mode' => $symbolMode]);
                    }
                }

                $messages = $conversation->messages()->latest('id')->take(20)->get()->reverse();
                if ($messages->isNotEmpty()) {
                    $isFirstMessage = false;
                    foreach ($messages as $msg) {
                        $history[] = ['role' => $msg->role, 'content' => $msg->content];
                    }
                }

                AiMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'user',
                    'content' => $prompt,
                    'image_url' => $imageBase64 ? 'attached' : null,
                    'notation_mode' => $symbolMode,
                ]);
            } catch (\Throwable $e) {
                Log::info('AI Stream DB notice: ' . $e->getMessage());
            }
        }

        if (empty($history)) {
            $clientHistory = $request->input('history');
            if (is_array($clientHistory) && !empty($clientHistory)) {
                $history = $clientHistory;
                $isFirstMessage = false;
            } else {
                $sessionKey = 'ai_chat_hist_' . $actor['type'] . '_' . ($conversationId ?: session()->getId());
                $sessHist = session()->get($sessionKey, []);
                if (!empty($sessHist)) {
                    $history = $sessHist;
                    $isFirstMessage = false;
                }
            }
        }

        $convIdForOutput = $conversation?->id ?: ($conversationId ?: 'conv_' . uniqid());
        $convTitle = $conversation?->title ?? $this->extractSmartTitle($prompt);

        return response()->stream(function() use ($prompt, $symbolMode, $imageBase64, $history, $isFirstMessage, $conversation, $convIdForOutput, $convTitle, $actor, $modelKey, $attachments) {
            // إرسال كائن التهيئة الأولية
            echo "data: " . json_encode([
                'type' => 'init',
                'conversation_id' => $convIdForOutput,
                'conversation_title' => $convTitle,
            ]) . "\n\n";
            if (ob_get_level()) { ob_flush(); }
            flush();

            $fullReply = '';

            $result = $this->aiEngine->askStream(
                $prompt,
                $symbolMode,
                $imageBase64,
                $history,
                $isFirstMessage,
                function($chunk) use (&$fullReply) {
                    $fullReply .= $chunk;
                    echo "data: " . json_encode([
                        'type' => 'chunk',
                        'chunk' => $chunk,
                    ]) . "\n\n";
                    if (ob_get_level()) { ob_flush(); }
                    flush();
                },
                $actor['role'],
                $modelKey,
                $attachments
            );

            $finalText = !empty($fullReply) ? $fullReply : ($result['text'] ?? '');

            // حفظ في الجلسة
            $sessionKey = 'ai_chat_hist_' . $actor['type'] . '_' . $convIdForOutput;
            $sessionHist = $history;
            $sessionHist[] = ['role' => 'user', 'content' => $prompt];
            $sessionHist[] = ['role' => 'assistant', 'content' => $finalText];
            session()->put($sessionKey, array_slice($sessionHist, -20));

            // حفظ رد الذكاء بالقاعدة
            if ($conversation && $this->isDbAvailable()) {
                try {
                    AiMessage::create([
                        'conversation_id' => $conversation->id,
                        'role' => 'assistant',
                        'content' => $finalText,
                        'notation_mode' => $symbolMode,
                    ]);
                } catch (\Throwable $e) {}
            }

            // إرسال إشعار الاكتمال النهائي
            echo "data: " . json_encode([
                'type' => 'done',
                'provider' => $result['provider'] ?? 'gemini',
                'model' => $result['model'] ?? 'gemini-3.8-flash',
                'final_text' => $finalText,
            ]) . "\n\n";
            if (ob_get_level()) { ob_flush(); }
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * جلب قائمة محادثات المستخدم الحالي والبحث فيها
     */
    public function getConversations(Request $request)
    {
        $actor = $this->getActor();
        $query = trim((string)$request->input('q', $request->input('search', '')));

        if ($this->isDbAvailable()) {
            try {
                $q = AiConversation::where('actor_type', $actor['type'])
                    ->where('actor_id', $actor['id']);

                if (!empty($query)) {
                    $q->where(function($sub) use ($query) {
                        $sub->where('title', 'like', "%{$query}%")
                            ->orWhereHas('messages', function($m) use ($query) {
                                $m->where('content', 'like', "%{$query}%");
                            });
                    });
                }

                $conversations = $q->latest()
                    ->take(50)
                    ->get(['id', 'title', 'symbol_mode', 'created_at']);

                return response()->json([
                    'conversations' => $conversations,
                    'grouped' => $this->groupConversationsByDate($conversations),
                ]);
            } catch (\Throwable $e) {}
        }

        $sessionKey = 'ai_session_conversations_' . $actor['type'] . '_' . $actor['id'];
        $convs = session()->get($sessionKey, []);
        if (!empty($query)) {
            $convs = array_filter($convs, function($c) use ($query) {
                return str_contains(mb_strtolower($c['title'] ?? ''), mb_strtolower($query));
            });
        }

        $convsList = array_values($convs);
        return response()->json([
            'conversations' => $convsList,
            'grouped' => $this->groupConversationsByDate($convsList),
        ]);
    }

    /**
     * إنشاء محادثة جديدة وحفظها
     */
    public function createConversation(Request $request)
    {
        $actor = $this->getActor();

        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'symbol_mode' => 'nullable|string|in:ar,en',
        ]);

        $title = trim($validated['title'] ?? '') ?: 'محادثة جديدة';
        $symbolMode = $validated['symbol_mode'] ?? 'ar';

        if ($this->isDbAvailable()) {
            try {
                $conversation = AiConversation::create([
                    'actor_type' => $actor['type'],
                    'actor_id' => $actor['id'],
                    'title' => $title,
                    'symbol_mode' => $symbolMode,
                ]);

                return response()->json([
                    'success' => true,
                    'conversation' => $conversation,
                    'message' => 'تم إنشاء المحادثة بنجاح'
                ]);
            } catch (\Throwable $e) {}
        }

        $newId = time();
        $convObj = [
            'id' => $newId,
            'title' => $title,
            'symbol_mode' => $symbolMode,
            'created_at' => now()->toIso8601String()
        ];
        $sessionKey = 'ai_session_conversations_' . $actor['type'] . '_' . $actor['id'];
        $convs = session()->get($sessionKey, []);
        $convs[$newId] = $convObj;
        session()->put($sessionKey, $convs);

        return response()->json([
            'success' => true,
            'conversation' => $convObj,
            'message' => 'تم إنشاء المحادثة بنجاح'
        ]);
    }

    /**
     * إعادة تسمية المحادثة
     */
    public function renameConversation(Request $request, $id)
    {
        $actor = $this->getActor();

        $validated = $request->validate([
            'title' => 'required|string|max:150',
        ]);

        $newTitle = trim($validated['title']);

        if ($this->isDbAvailable()) {
            try {
                $conversation = AiConversation::where('id', $id)
                    ->where('actor_type', $actor['type'])
                    ->where('actor_id', $actor['id'])
                    ->first();

                if ($conversation) {
                    $conversation->update(['title' => $newTitle]);
                    return response()->json([
                        'success' => true,
                        'title' => $conversation->title,
                        'message' => 'تم تغيير اسم المحادثة بنجاح'
                    ]);
                }
            } catch (\Throwable $e) {}
        }

        $sessionKey = 'ai_session_conversations_' . $actor['type'] . '_' . $actor['id'];
        $convs = session()->get($sessionKey, []);
        if (isset($convs[$id])) {
            $convs[$id]['title'] = $newTitle;
            session()->put($sessionKey, $convs);
        }

        return response()->json([
            'success' => true,
            'title' => $newTitle,
            'message' => 'تم تغيير اسم المحادثة بنجاح'
        ]);
    }

    /**
     * جلب رسائل محادثة معينة
     */
    public function getMessages($id)
    {
        $actor = $this->getActor();

        if ($this->isDbAvailable()) {
            try {
                $conversation = AiConversation::where('id', $id)
                    ->where('actor_type', $actor['type'])
                    ->where('actor_id', $actor['id'])
                    ->first();

                if ($conversation) {
                    $messages = $conversation->messages()->get(['id', 'role', 'content', 'notation_mode', 'created_at']);
                    return response()->json([
                        'conversation' => $conversation,
                        'messages' => $messages,
                    ]);
                }
            } catch (\Throwable $e) {}
        }

        $sessionKey = 'ai_chat_hist_' . $actor['type'] . '_' . $id;
        $sessionHistory = session()->get($sessionKey, []);
        $sessionListKey = 'ai_session_conversations_' . $actor['type'] . '_' . $actor['id'];
        $convs = session()->get($sessionListKey, []);
        $conv = $convs[$id] ?? ['id' => $id, 'title' => 'محادثة سابقة', 'symbol_mode' => 'ar'];

        $formattedMessages = [];
        foreach ($sessionHistory as $index => $msg) {
            $formattedMessages[] = [
                'id' => $index + 1,
                'role' => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? '',
                'notation_mode' => 'ar',
                'created_at' => now()->toIso8601String(),
            ];
        }

        return response()->json([
            'conversation' => $conv,
            'messages' => $formattedMessages,
        ]);
    }

    /**
     * حذف محادثة خاصة بالمستخدم
     */
    public function deleteConversation($id)
    {
        $actor = $this->getActor();

        if ($this->isDbAvailable()) {
            try {
                $conversation = AiConversation::where('id', $id)
                    ->where('actor_type', $actor['type'])
                    ->where('actor_id', $actor['id'])
                    ->first();

                if ($conversation) {
                    $conversation->delete();
                }
            } catch (\Throwable $e) {}
        }

        $sessionKey = 'ai_session_conversations_' . $actor['type'] . '_' . $actor['id'];
        $convs = session()->get($sessionKey, []);
        if (isset($convs[$id])) {
            unset($convs[$id]);
            session()->put($sessionKey, $convs);
        }
        session()->forget('ai_chat_hist_' . $actor['type'] . '_' . $id);

        return response()->json(['success' => true]);
    }

    /**
     * مسح كافة محادثات المستخدم الحالي
     */
    public function clearHistory()
    {
        $actor = $this->getActor();

        if ($this->isDbAvailable()) {
            try {
                AiConversation::where('actor_type', $actor['type'])
                    ->where('actor_id', $actor['id'])
                    ->delete();
            } catch (\Throwable $e) {}
        }

        $sessionKey = 'ai_session_conversations_' . $actor['type'] . '_' . $actor['id'];
        session()->forget($sessionKey);

        return response()->json(['success' => true]);
    }

    /**
     * تسجيل تقييم أو ملاحظة على إجابة الذكاء الاصطناعي (Like/Dislike)
     */
    public function feedback(Request $request)
    {
        $actor = $this->getActor();
        $type = $request->input('type', 'like'); // 'like' or 'dislike'
        $messageId = $request->input('message_id');
        $comment = $request->input('comment');

        Log::info("AI Feedback from [{$actor['type']}:{$actor['id']}]: type={$type}, msgId={$messageId}");

        return response()->json([
            'success' => true,
            'message' => 'شكراً لمشاركتك تقييمك! نسعى دائماً لتطوير الإجابات.'
        ]);
    }

    /**
     * استخراج عنوان ذكي ومختصر للمحادثة من السؤال الأول
     */
    protected function extractSmartTitle(string $prompt): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $prompt));
        $clean = preg_replace('/^(مرحبا|أهلاً|السلام عليكم|لو سمحت|ممكن|عايز|بدي|اشرح لي|حل لي|وضح لي)\s+/u', '', $clean);
        $title = mb_substr($clean, 0, 38);
        if (mb_strlen($clean) > 38) {
            $title .= '...';
        }
        return !empty($title) ? $title : 'محادثة تعليمية جديدة';
    }
}
