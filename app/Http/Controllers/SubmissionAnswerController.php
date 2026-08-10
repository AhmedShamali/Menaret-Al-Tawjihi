<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamSubmission;
use App\Models\Submission_Answer;
use Illuminate\Support\Facades\DB;

class SubmissionAnswerController extends Controller
{
    public function submit(Request $request, $id)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'answers' => 'required|array',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $exam = Exam::with('questions')->findOrFail($id);

        try {
            return DB::transaction(function () use ($request, $exam, $id) {
                // 2. إنشاء سجل التسليم
                $submission = ExamSubmission::create([
                    'exam_id' => $id,
                    'student_id' => auth()->id() ?? 1,
                    'total_earned_grade' => 0, // سيتم تحديثه بعد قليل
                ]);

                $totalScore = 0;

                // 3. معالجة كل سؤال
                foreach ($exam->questions as $question) {
                    $qId = $question->id;
                    $studentAnswer = $request->answers[$qId] ?? null;
                    $pointsAwarded = 0;

                    // منطق التصحيح التلقائي للأسئلة الاختيارية فقط
                    if ($question->type == 'mcq') {
                        if (strtolower(trim($studentAnswer)) == strtolower(trim($question->correct_answer))) {
                            $pointsAwarded = $question->points;
                        } else {
                            $pointsAwarded = 0;
                        }
                    } else {
                        // الأسئلة المقالية أو التي تتطلب ملف تترك درجتها 0 حالياً ليصححها المعلم
                        $pointsAwarded = 0;
                    }

                    // معالجة الملف المرفق
                    $path = null;
                    if ($request->hasFile("files.$qId")) {
                        $path = $request->file("files.$qId")->store('exam_files', 'public');
                    }

                    // حفظ الإجابة
                    Submission_Answer::create([
                        'exam_submission_id' => $submission->id,
                        'question_id' => $qId,
                        'answer_text' => is_array($studentAnswer) ? json_encode($studentAnswer) : $studentAnswer,
                        'file_path' => $path,
                        'points_awarded' => $pointsAwarded,
                    ]);

                    $totalScore += $pointsAwarded;
                }

                // 4. تحديث مجموع الدرجات النهائي مرة واحدة فقط
                $submission->update(['total_earned_grade' => $totalScore]);

                return response()->json([
                    'success' => true,
                    'submission_id' => $submission->id,
                    'message' => 'تم حفظ إجاباتك بنجاح'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }
}
