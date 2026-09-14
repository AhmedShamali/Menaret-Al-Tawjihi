<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Flashcard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FlashcardController extends Controller
{
    /**
     * عرض بطاقات الاستذكار السريع مع فلترة حسب المادة والفرع
     */
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user() ?? Auth::user();

        $subjects = Flashcard::select('subject_name')->distinct()->pluck('subject_name');
        if ($subjects->isEmpty()) {
            $subjects = collect(['فيزياء', 'رياضيات', 'كيمياء', 'تاريخ', 'لغة عربية', 'أحياء']);
        }

        $activeSubject = $request->input('subject', $subjects->first() ?? 'فيزياء');

        $query = Flashcard::where('subject_name', $activeSubject);

        // إخفاء البطاقات المستثناة أو المحجوبة ما لم يُطلب عرضها
        if (Schema::hasColumn('flashcards', 'is_hidden')) {
            if (!$request->boolean('show_hidden')) {
                $query->where('is_hidden', 0);
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $flashcards = $query->latest()->get();

        $categories = Flashcard::where('subject_name', $activeSubject)
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('student.flashcards.index', compact('flashcards', 'subjects', 'activeSubject', 'categories'));
    }

    /**
     * إضافة بطاقة استذكار جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => 'required|string|max:100',
            'category'     => 'nullable|string|max:100',
            'front_text'   => 'required|string',
            'back_text'    => 'required|string',
            'difficulty'   => 'nullable|string|max:20',
        ]);

        $student = Auth::guard('student')->user() ?? Auth::user();

        $card = Flashcard::create([
            'subject_name' => $validated['subject_name'],
            'category'     => $validated['category'] ?: 'مفاهيم وقوانين',
            'front_text'   => $validated['front_text'],
            'back_text'    => $validated['back_text'],
            'difficulty'   => $validated['difficulty'] ?? 'medium',
            'student_id'   => $student?->id,
            'is_custom'    => 1,
            'is_hidden'    => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء بطاقة الاستذكار بنجاح! ✨',
            'card'    => $card
        ]);
    }

    /**
     * تعديل بطاقة استذكار
     */
    public function update(Request $request, $id)
    {
        $card = Flashcard::findOrFail($id);

        $validated = $request->validate([
            'subject_name' => 'sometimes|required|string|max:100',
            'category'     => 'nullable|string|max:100',
            'front_text'   => 'required|string',
            'back_text'    => 'required|string',
            'difficulty'   => 'nullable|string|max:20',
        ]);

        $card->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل البطاقة بنجاح! 📝',
            'card'    => $card
        ]);
    }

    /**
     * حذف بطاقة استذكار
     */
    public function destroy($id)
    {
        $card = Flashcard::findOrFail($id);
        $card->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف البطاقة بنجاح 🗑️'
        ]);
    }

    /**
     * إخفاء أو إظهار البطاقة من المراجعة
     */
    public function toggleHide($id)
    {
        $card = Flashcard::findOrFail($id);
        $newVal = $card->is_hidden ? 0 : 1;
        $card->update(['is_hidden' => $newVal]);

        return response()->json([
            'success'   => true,
            'is_hidden' => $newVal,
            'message'   => $newVal ? 'تم إخفاء البطاقة عن جلسات المراجعة 👁️‍🗨️' : 'تمت استعادة وإظهار البطاقة في المراجعة ✅'
        ]);
    }
}
