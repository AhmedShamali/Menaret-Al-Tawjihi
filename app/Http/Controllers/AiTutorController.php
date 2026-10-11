<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\AiEngineService;
use App\Support\CurrentActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiTutorController extends Controller
{
    protected AiEngineService $aiEngine;

    public function __construct(AiEngineService $aiEngine)
    {
        $this->aiEngine = $aiEngine;
    }

    /**
     * جلب الممثل الحالي (طالب أو مستخدم إدارة/معلم)
     */
    protected function getActor(): ?array
    {
        $student = CurrentActor::student();
        if ($student) {
            return [
                'type' => 'student',
                'id' => $student->id,
                'name' => $student->name,
                'avatar' => $student->avatar ?? null,
            ];
        }

        $staff = CurrentActor::staff() ?? auth()->user();
        if ($staff) {
            return [
                'type' => 'user',
                'id' => $staff->id,
                'name' => $staff->name,
                'avatar' => $staff->avatar ?? null,
            ];
        }

        return null;
    }

    /**
     * الصفحة الرئيسية للمركز الأكاديمي الشامل للذكاء الاصطناعي
     */
    public function index(Request $request)
    {
        $actor = $this->getActor();
        if (!$actor) {
            return redirect()->route('login')->with('warning', 'يرجى تسجيل الدخول للوصول إلى مركز الذكاء الاصطناعي الأكاديمي.');
        }

        $conversations = [];
        try {
            $conversations = AiConversation::where('actor_type', $actor['type'])
                ->where('actor_id', $actor['id'])
                ->with('latestMessage')
                ->latest()
                ->take(30)
                ->get();
        } catch (\Throwable $e) {
            Log::info('AI Conversations fetch note: ' . $e->getMessage());
        }

        return view('student.ai.hub', compact('actor', 'conversations'));
    }

    /**
     * إرسال رسالة والحصول على رد الذكاء الاصطناعي
     */
    public function chat(Request $request)
    {
        $actor = $this->getActor();
        if (!$actor) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، المساعد الذكي متاح حصرياً للأعضاء والطلبة المسجلين في المنصة. يرجى تسجيل الدخول أولاً.',
                'require_login' => true,
            ], 401);
        }

        $request->validate([
            'prompt' => 'nullable|string|max:5000',
            'conversation_id' => 'nullable|integer',
            'symbol_mode' => 'nullable|string|in:ar,en',
            'image' => 'nullable',
        ]);

        $prompt = trim((string)$request->input('prompt', ''));
        $symbolMode = $request->input('symbol_mode', 'ar');
        $imageBase64 = null;

        // معالجة الصورة المرفوعة إن وجدت
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                $data = file_get_contents($file->getRealPath());
                $mime = $file->getMimeType();
                $imageBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        } elseif ($request->filled('image_base64')) {
            $imageBase64 = $request->input('image_base64');
        }

        if (empty($prompt) && empty($imageBase64)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى كتابة سؤال أو إرفاق صورة مسألة للتحليل.',
            ], 422);
        }

        if (empty($prompt) && !empty($imageBase64)) {
            $prompt = 'يرجى تحليل صورة هذه المسألة وحلها بالخطوات التفصيلية.';
        }

        // جلب أو إنشاء محادثة خاصة بالمستخدم
        $conversation = null;
        $history = [];
        try {
            if ($request->filled('conversation_id')) {
                $conversation = AiConversation::where('id', $request->conversation_id)
                    ->where('actor_type', $actor['type'])
                    ->where('actor_id', $actor['id'])
                    ->first();
            }

            if (!$conversation) {
                // استخراج عنوان مختصر ومفهوم للمحادثة
                $shortTitle = mb_substr($prompt, 0, 45);
                if (mb_strlen($prompt) > 45) $shortTitle .= '...';

                $conversation = AiConversation::create([
                    'actor_type' => $actor['type'],
                    'actor_id' => $actor['id'],
                    'title' => $shortTitle ?: 'استفسار جديد',
                    'symbol_mode' => $symbolMode,
                ]);
            } else {
                if ($request->filled('symbol_mode')) {
                    $conversation->update(['symbol_mode' => $symbolMode]);
                }
            }

            // جلب سجل المحادثة السابقة لتغذية الذكاء بالسياق
            $messages = $conversation->messages()->take(8)->get();
            foreach ($messages as $msg) {
                $history[] = [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ];
            }

            // حفظ رسالة المستخدم
            AiMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'content' => $prompt,
                'image_url' => $imageBase64 ? 'image_attached' : null,
                'notation_mode' => $symbolMode,
            ]);
        } catch (\Throwable $e) {
            Log::info('AI Conversation DB bypass/fallback: ' . $e->getMessage());
        }

        // استدعاء محرك الذكاء الاصطناعي
        $result = $this->aiEngine->ask($prompt, $symbolMode, $imageBase64, $history);
        $aiReply = $result['text'];

        // حفظ رد الذكاء في المحادثة
        try {
            if ($conversation) {
                AiMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $aiReply,
                    'notation_mode' => $symbolMode,
                ]);
            }
        } catch (\Throwable $e) {
            Log::info('AI Message save note: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'reply' => $aiReply,
            'conversation_id' => $conversation?->id,
            'conversation_title' => $conversation?->title,
            'symbol_mode' => $symbolMode,
            'provider' => $result['provider'],
            'model' => $result['model'],
        ]);
    }

    /**
     * جلب قائمة محادثات المستخدم الحالي
     */
    public function getConversations()
    {
        $actor = $this->getActor();
        if (!$actor) {
            return response()->json(['conversations' => []]);
        }

        try {
            $conversations = AiConversation::where('actor_type', $actor['type'])
                ->where('actor_id', $actor['id'])
                ->latest()
                ->take(30)
                ->get(['id', 'title', 'symbol_mode', 'created_at']);

            return response()->json(['conversations' => $conversations]);
        } catch (\Throwable $e) {
            return response()->json(['conversations' => []]);
        }
    }

    /**
     * جلب رسائل محادثة معينة
     */
    public function getMessages($id)
    {
        $actor = $this->getActor();
        if (!$actor) {
            return response()->json(['messages' => []], 401);
        }

        try {
            $conversation = AiConversation::where('id', $id)
                ->where('actor_type', $actor['type'])
                ->where('actor_id', $actor['id'])
                ->firstOrFail();

            $messages = $conversation->messages()->get(['id', 'role', 'content', 'notation_mode', 'created_at']);

            return response()->json([
                'conversation' => $conversation,
                'messages' => $messages,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['messages' => []], 404);
        }
    }

    /**
     * حذف محادثة خاصة بالمستخدم
     */
    public function deleteConversation($id)
    {
        $actor = $this->getActor();
        if (!$actor) {
            return response()->json(['success' => false], 401);
        }

        try {
            $conversation = AiConversation::where('id', $id)
                ->where('actor_type', $actor['type'])
                ->where('actor_id', $actor['id'])
                ->first();

            if ($conversation) {
                $conversation->delete();
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false]);
        }
    }

    /**
     * مسح كافة محادثات المستخدم
     */
    public function clearHistory()
    {
        $actor = $this->getActor();
        if (!$actor) {
            return response()->json(['success' => false], 401);
        }

        try {
            AiConversation::where('actor_type', $actor['type'])
                ->where('actor_id', $actor['id'])
                ->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false]);
        }
    }
}
