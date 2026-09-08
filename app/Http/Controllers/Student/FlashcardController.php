<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Flashcard;
use Illuminate\Http\Request;

class FlashcardController extends Controller
{
    /**
     * عرض بطاقات الاستذكار السريع مع فلترة حسب المادة والفرع
     */
    public function index(Request $request)
    {
        $subjects = Flashcard::select('subject_name')->distinct()->pluck('subject_name');
        if ($subjects->isEmpty()) {
            $subjects = collect(['فيزياء', 'رياضيات', 'كيمياء', 'تاريخ', 'لغة عربية']);
        }

        $activeSubject = $request->input('subject', $subjects->first() ?? 'فيزياء');

        $query = Flashcard::where('subject_name', $activeSubject);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $flashcards = $query->get();

        $categories = Flashcard::where('subject_name', $activeSubject)
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('student.flashcards.index', compact('flashcards', 'subjects', 'activeSubject', 'categories'));
    }
}
