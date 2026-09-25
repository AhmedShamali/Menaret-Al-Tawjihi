<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\ContentAssignment;
use App\Models\EducationalContent;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\Message;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentApiController extends Controller
{
    /**
     * استخراج الطالب الموثق عبر Bearer Token أو الترويسة
     */
    protected function resolveStudent(Request $request): ?Student
    {
        $authHeader = $request->header('Authorization');
        if ($authHeader && preg_match('/Bearer\s+(\S+)/i', $authHeader, $m)) {
            $raw = base64_decode($m[1], true);
            if ($raw && str_contains($raw, '|')) {
                [$id, $hash] = explode('|', $raw, 2);
                $std = Student::find($id);
                if ($std && md5($std->password . config('app.key')) === $hash) {
                    return $std;
                }
            }
        }

        $studentId = $request->header('X-Student-Id') ?? $request->get('student_id');
        if ($studentId) {
            return Student::find($studentId);
        }

        return null;
    }

    /**
     * تسجيل دخول الطالب للتطبيق
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string', // بريد إلكتروني أو رقم هوية
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $login = trim($request->login);
        $student = Student::with('stage')
            ->where('email', $login)
            ->orWhere('nid', $login)
            ->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة، يرجى التحقق من البريد أو رقم الهوية وكلمة المرور.',
            ], 401);
        }

        if ($student->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'حساب الطالب معلّق أو قيد المراجعة، يرجى التواصل مع إدارة المنصة.',
            ], 403);
        }

        // إنشاء التوكن (يمكن استخدام Sanctum أو توكن مميز)
        $token = method_exists($student, 'createToken')
            ? $student->createToken('mobile-app')->plainTextToken
            : base64_encode($student->id . '|' . md5($student->password . config('app.key')));

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح 🎉',
            'token'   => $token,
            'student' => [
                'id'         => $student->id,
                'name_ar'    => $student->name_ar,
                'name_en'    => $student->name_en,
                'email'      => $student->email,
                'nid'        => $student->nid,
                'phone'      => $student->phone,
                'stage_id'   => $student->stage_id,
                'stage_name' => optional($student->stage)->label_ar ?? optional($student->stage)->name ?? 'ثانوية عامة',
                'photo_url'  => $student->photo ? asset('storage/' . $student->photo) : null,
            ]
        ]);
    }

    /**
     * تسجيل حساب طالب جديد من التطبيق
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar'  => 'required|string|max:255',
            'email'    => 'required|email|unique:students,email',
            'nid'      => 'required|string|unique:students,nid',
            'phone'    => 'required|string',
            'password' => 'required|string|min:6',
            'stage_id' => 'required|exists:stages,id',
            'gender'   => 'nullable|in:male,female',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $student = Student::create([
            'name_ar'  => $request->name_ar,
            'name_en'  => $request->name_en ?? $request->name_ar,
            'email'    => $request->email,
            'nid'      => $request->nid,
            'phone'    => $request->phone,
            'stage_id' => $request->stage_id,
            'gender'   => $request->gender ?? 'male',
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء حساب الطالب بنجاح! يمكنك الآن تسجيل الدخول.',
            'student_id' => $student->id
        ], 201);
    }

    /**
     * لوحة تحكم الطالب والملف الشخصي
     */
    public function dashboard(Request $request)
    {
        $student = $this->resolveStudent($request);

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 401);
        }

        $completedExams = ExamSubmission::where('student_id', $student->id)->count();
        $avgGrade = ExamSubmission::where('student_id', $student->id)->avg('total_earned_grade') ?? 0;
        $activeSubjects = Enrollment::where('student_id', $student->id)->where('status', 'active')->count();

        return response()->json([
            'success' => true,
            'stats' => [
                'completed_exams' => $completedExams,
                'avg_grade'       => round($avgGrade, 1),
                'active_subjects' => $activeSubjects,
            ],
            'stage' => [
                'id'    => $student->stage_id,
                'title' => optional($student->stage)->label_ar ?? optional($student->stage)->name ?? 'ثانوية عامة',
            ]
        ]);
    }

    /**
     * قائمة المواد الدراسية المتاحة للطالب مع تفاصيل الاشتراك
     */
    public function subjects(Request $request)
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 401);
        }

        $studentId = $student->id;
        $stageId = $student->stage_id;

        // حصر قطعي: فقط المواد المسجل بها باشتراك نشط وتتبع فرع الطالب الأصلي
        $enrolledSubjectIds = Enrollment::where('student_id', $studentId)
            ->where('status', 'active')
            ->pluck('subject_id')
            ->toArray();

        if (empty($enrolledSubjectIds)) {
            return response()->json([
                'success'  => true,
                'subjects' => [],
            ]);
        }

        $subjectsQuery = Subject::with(['teacher:id,name,phone'])
            ->whereIn('id', $enrolledSubjectIds);

        if ($stageId) {
            $subjectsQuery->where('stage_id', $stageId);
        }

        $subjects = $subjectsQuery->get()->map(function ($sub) use ($studentId) {
            $enrollment = Enrollment::where('student_id', $studentId)->where('subject_id', $sub->id)->where('status', 'active')->first();

            return [
                'id'             => $sub->id,
                'name_ar'        => $sub->name_ar ?? $sub->name,
                'color'          => $sub->color ?? '#0284c7',
                'lessons_count'  => EducationalContent::where('subject_id', $sub->id)->count(),
                'teacher'        => [
                    'id'   => optional($sub->teacher)->id,
                    'name' => optional($sub->teacher)->name ?? 'معلم المادة',
                ],
                'is_enrolled'    => true,
                'access_mode'    => $enrollment ? $enrollment->access_mode : 'none', // all, custom, none
            ];
        });

        return response()->json([
            'success'  => true,
            'subjects' => $subjects,
        ]);
    }

    /**
     * تفاصيل مادة معينة والدروس مع فحص الصلاحية لكل درس
     */
    public function subjectDetails(Request $request, $id)
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 401);
        }

        $studentId = $student->id;
        $subject = Subject::with(['stage', 'teacher'])->findOrFail($id);

        // حظر المادة إن كانت تتبع فرعاً دراسياً غير فرع الطالب الأصلي
        if ($student->stage_id && $subject->stage_id && (int)$subject->stage_id !== (int)$student->stage_id) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، هذه المادة لا تنتمي إلى فرعك الدراسي الأصلي.'
            ], 403);
        }

        // حظر المادة إن لم يكن الطالب مسجلاً بها باشتراك نشط معتمد
        $enrollment = Enrollment::where('student_id', $studentId)->where('subject_id', $id)->where('status', 'active')->first();
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، لا يمكنك عرض محتوى هذه المادة لأنك غير مسجل بها باشتراك نشط.'
            ], 403);
        }

        $isFullAccess = ($enrollment->access_mode === 'all');
        $allowedIds = [];

        if (!$isFullAccess) {
            $allowedIds = ContentAssignment::where('enrollment_id', $enrollment->id)
                ->where('is_visible', true)
                ->pluck('educational_content_id')
                ->toArray();
        }

        $contents = EducationalContent::where('subject_id', $id)
            ->orderBy('order', 'asc')
            ->get()
            ->map(function ($item) use ($isFullAccess, $allowedIds) {
                $isUnlocked = $isFullAccess || in_array($item->id, $allowedIds);

                return [
                    'id'           => $item->id,
                    'title'        => $item->title,
                    'type'         => $item->type,
                    'order'        => $item->order,
                    'channel_name' => $item->channel_name ?? 'عام',
                    'video_url'    => $isUnlocked ? (filter_var($item->url_path, FILTER_VALIDATE_URL) ? $item->url_path : asset('storage/' . $item->url_path)) : null,
                    'pdf_url'      => $isUnlocked ? (filter_var($item->pdf_path, FILTER_VALIDATE_URL) ? $item->pdf_path : asset('storage/' . $item->pdf_path)) : null,
                    'is_unlocked'  => (bool) $isUnlocked,
                ];
            });

        return response()->json([
            'success' => true,
            'subject' => [
                'id'          => $subject->id,
                'name'        => $subject->name_ar ?? $subject->name,
                'color'       => $subject->color ?? '#0284c7',
                'teacher'     => [
                    'id'   => optional($subject->teacher)->id,
                    'name' => optional($subject->teacher)->name,
                ],
                'is_enrolled' => true,
                'access_mode' => $enrollment->access_mode,
            ],
            'contents' => $contents,
        ]);
    }

    /**
     * نقطة مزامنة خاصة بتطبيق الجوال لتنزيل الدروس المصرح بها أوفلاين
     */
    public function offlineSync(Request $request)
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 401);
        }
        $studentId = $student->id;

        // جلب جميع المواد والدروس المفتوحة لهذا الطالب التابعة لفرعه الأصلي
        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'active')
            ->whereHas('subject', function($q) use ($student) {
                if ($student->stage_id) {
                    $q->where('stage_id', $student->stage_id);
                }
            })
            ->get();
        $allowedContents = [];

        foreach ($enrollments as $enr) {
            $query = EducationalContent::where('subject_id', $enr->subject_id);
            if ($enr->access_mode === 'custom') {
                $customIds = ContentAssignment::where('enrollment_id', $enr->id)->where('is_visible', true)->pluck('educational_content_id');
                $query->whereIn('id', $customIds);
            }
            $items = $query->orderBy('order')->get();
            foreach ($items as $item) {
                if (!empty($item->url_path)) {
                    $allowedContents[] = [
                        'content_id'   => $item->id,
                        'subject_id'   => $item->subject_id,
                        'title'        => $item->title,
                        'video_url'    => filter_var($item->url_path, FILTER_VALIDATE_URL) ? $item->url_path : asset('storage/' . $item->url_path),
                        'pdf_url'      => !empty($item->pdf_path) ? (filter_var($item->pdf_path, FILTER_VALIDATE_URL) ? $item->pdf_path : asset('storage/' . $item->pdf_path)) : null,
                        'sync_token'   => hash_hmac('sha256', $studentId . '-' . $item->id, config('app.key')),
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'total_offline_lessons' => count($allowedContents),
            'lessons' => $allowedContents,
        ]);
    }

    /**
     * تفعيل كود الشحن من تطبيق الجوال
     */
    public function redeemVoucher(Request $request)
    {
        $student = $this->resolveStudent($request);
        $targetStudentId = $student ? $student->id : $request->student_id;

        $validator = Validator::make($request->all(), [
            'student_id' => $student ? 'nullable|exists:students,id' : 'required|exists:students,id',
            'code'       => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $code = strtoupper(trim($request->code));
        $voucher = ActivationCode::with('subject')->where('code', $code)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'كود التفعيل غير صالح.'], 404);
        }

        if ($voucher->is_used) {
            return response()->json(['success' => false, 'message' => 'تم استخدام هذا الكود مسبقاً.'], 422);
        }

        Enrollment::updateOrCreate(
            [
                'student_id' => $targetStudentId,
                'subject_id' => $voucher->subject_id,
            ],
            [
                'status' => 'active',
                'access_mode' => $voucher->access_mode ?? 'all',
                'payment_status' => 'voucher',
                'activated_at' => now(),
            ]
        );

        $voucher->update([
            'is_used' => true,
            'used_by_student_id' => $targetStudentId,
            'used_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "تم تفعيل اشتراك مادة ({$voucher->subject->name_ar}) بنجاح!",
            'subject_id' => $voucher->subject_id,
        ]);
    }
}
