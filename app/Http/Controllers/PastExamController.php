<?php

namespace App\Http\Controllers;

use App\Models\PastExam;
use Illuminate\Http\Request;

class PastExamController extends Controller
{
    /**
     * عرض أرشيف الامتحانات الوزارية مع التصفية والبحث
     */
    public function index(Request $request)
    {
        $query = PastExam::query();

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('branch') && $request->branch !== 'all') {
            $query->where('branch', $request->branch);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject_name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $exams = $query->orderBy('year', 'desc')->paginate(12)->withQueryString();

        // سنوات الأرشيف المتاحة
        $years = PastExam::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        if ($years->isEmpty()) {
            $years = collect([2024, 2023, 2022, 2021, 2020]);
        }

        return view('public.past_exams.index', compact('exams', 'years'));
    }

    /**
     * تحميل ورقة الامتحان
     */
    public function downloadPaper($id)
    {
        $exam = PastExam::findOrFail($id);
        $exam->increment('downloads_count');

        if ($exam->exam_paper_url) {
            return redirect($exam->exam_paper_url);
        }

        return back()->with('info', 'ملف ورقة الامتحان قيد التجهيز من قبل وزارة التربية والتعليم.');
    }

    /**
     * تحميل نموذج الإجابة المعتمد
     */
    public function downloadAnswerKey($id)
    {
        $exam = PastExam::findOrFail($id);
        $exam->increment('downloads_count');

        if ($exam->answer_key_url) {
            return redirect($exam->answer_key_url);
        }

        return back()->with('info', 'نموذج الإجابة الرسمي قيد التدقيق وسيتاح قريباً.');
    }
}
