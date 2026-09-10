<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    AuthController,
    ExamController,
    StudentController,
    EducationalContentController,
    CommunicationController,
    AdminManagerController,
    PublicController,
    PlacementController,
    DashboardController,
    VideoController,
    ChannelController,
    StageController,
    SubjectController,
    ExamSubmissionController
};

/*
|--------------------------------------------------------------------------
| 1. الروابط العامة (Public Routes)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'handleLogin'])->name('login.post');
    Route::get('/register', [StudentController::class, 'create'])->name('students.create');
    Route::post('/register', [StudentController::class, 'store'])->name('students.store');
    Route::post('/forgot-password', [AuthController::class, 'handleForgot'])->name('password.forgot');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// لوحة التحكم الموحدة للتوجيه التلقائي حسب نوع الحساب
Route::get('/dashboard', function () {
    if (Auth::guard('student')->check()) {
        return redirect()->route('student.dashboard');
    }
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'teacher') {
            return redirect()->route('teacher.dashboard');
        }
    }
    return redirect()->route('login');
})->name('dashboard');

Route::get('/faq', [PublicController::class, 'faq'])->name('public.faq');
Route::get('/contact', [PublicController::class, 'contact'])->name('public.contact');
Route::get('/terms', [PublicController::class, 'terms'])->name('public.terms');
Route::get('/privacy', [PublicController::class, 'privacy'])->name('public.privacy');

Route::get('/stages', [StageController::class, 'index'])->name('stages.index');
Route::get('/stages/{id}', [StageController::class, 'show'])->name('stages.show');
Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
Route::get('/subject/{id}', [SubjectController::class, 'show'])->name('subject.show');
Route::get('/subjects/{id}/files', [SubjectController::class, 'files'])->name('subject.files');

Route::get('/video-stream/{filename}', [VideoController::class, 'stream'])
    ->where('filename', '.*')
    ->name('video.stream');

Route::get('/placement', [PlacementController::class, 'index'])->name('placement.index');
Route::post('/placement/save', [PlacementController::class, 'store'])->name('placement.store');

Route::get('/students', function () {
    return view('visitor');
});

Route::get('/educational-contents/{id}/download', [EducationalContentController::class, 'downloadFile'])->name('content.download');
Route::resource('educational_contents', EducationalContentController::class);

// حاسبة معدل التوجيهي ودليل التنسيق والقبول الجامعي
Route::get('/tawjihi-calculator', [\App\Http\Controllers\TawjihiCalculatorController::class, 'index'])->name('tawjihi.calculator');
Route::post('/tawjihi-calculator/calculate', [\App\Http\Controllers\TawjihiCalculatorController::class, 'calculate'])->name('tawjihi.calculate');

// أرشيف الامتحانات الوزارية ونماذج الإجابة الرسمية
Route::get('/tawjihi-archive', [\App\Http\Controllers\PastExamController::class, 'index'])->name('tawjihi.archive');
Route::get('/past-exams', [\App\Http\Controllers\PastExamController::class, 'index'])->name('past-exams.index');
Route::get('/tawjihi-archive/paper/{id}', [\App\Http\Controllers\PastExamController::class, 'downloadPaper'])->name('tawjihi.download.paper');
Route::get('/tawjihi-archive/answer-key/{id}', [\App\Http\Controllers\PastExamController::class, 'downloadAnswerKey'])->name('tawjihi.download.key');

// بطاقات الاستذكار السريع والقوانين (Flashcards) للعامة والطلاب
Route::get('/public-flashcards', [\App\Http\Controllers\Student\FlashcardController::class, 'index'])->name('smart.learning.flashcards');
Route::get('/catalog', [\App\Http\Controllers\Student\CourseEnrollmentController::class, 'catalog'])->name('courses.catalog');
Route::get('/checkout', [\App\Http\Controllers\Student\PaymentGatewayController::class, 'showCheckout'])->name('checkout.show');
Route::get('/checkout/receipt/{id}', [\App\Http\Controllers\Student\PaymentGatewayController::class, 'successReceipt'])->name('checkout.receipt');

// مسارات بديلة ومساعدة للروابط العامة
Route::get('/contact-us', [PublicController::class, 'contact'])->name('contact');
Route::get('/privacy-policy', [PublicController::class, 'privacy'])->name('privacy');
Route::get('/student-register', [StudentController::class, 'create'])->name('student.create');

// دليل القوانين والقواعد الذهبية للتوجيهي
Route::get('/tawjihi-formulas', [\App\Http\Controllers\TawjihiFormulaController::class, 'index'])->name('tawjihi.formulas');

// التحقق العام وعرض الشهادات الأكاديمية الملكية
Route::get('/verify/certificate/{code}', [\App\Http\Controllers\SmartLearningController::class, 'verifyCertificate'])->name('certificates.verify');
Route::get('/certificates/{id}', [\App\Http\Controllers\SmartLearningController::class, 'showCertificate'])->name('certificates.show');


/*
|--------------------------------------------------------------------------
| 2. بوابة المدير العام (Admin Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'IsAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminIndex'])->name('dashboard');

    Route::get('/teachers/chat', [CommunicationController::class, 'teachersChat'])->name('teachers.chat');
    Route::post('/teachers/chat/send', [CommunicationController::class, 'sendFromAdminToTeacher'])->name('teachers.send');

    Route::get('/students/add', [AdminManagerController::class, 'studentCreate'])->name('students.add');
    Route::post('/students/save', [AdminManagerController::class, 'studentStore'])->name('students.save');
    Route::post('/students/toggle-status/{id}', [StudentController::class, 'toggleStatus'])->name('students.toggleStatus');
    Route::post('/students/{id}/approve', [StudentController::class, 'approveStudent'])->name('students.approve');
    Route::post('/students/{id}/sync-subjects', [StudentController::class, 'syncSubjects'])->name('students.syncSubjects');
    Route::post('/students/{id}/toggle-subject/{subject_id}', [StudentController::class, 'toggleSubjectEnrollment'])->name('students.toggleSubject');

    Route::get('/teachers/info', [AdminManagerController::class, 'teachersInfo'])->name('teachers.info');
    Route::get('/management', [AdminManagerController::class, 'teachersInfo'])->name('management.index');
    Route::get('/teachers/create', [AdminManagerController::class, 'teacherCreate'])->name('teachers.create');
    Route::post('/teachers/store', [AdminManagerController::class, 'teacherStore'])->name('teachers.store');

    Route::get('/settings', [AdminManagerController::class, 'settings'])->name('settings.index');
    Route::post('/settings/update', [AdminManagerController::class, 'settingsUpdate'])->name('settings.update');
    Route::get('/system-pulse', [AdminManagerController::class, 'pulse'])->name('activities.index');

    Route::get('/inbox', [CommunicationController::class, 'adminInbox'])->name('messages.index');
    Route::get('/fetch/{student_id}', [CommunicationController::class, 'fetchMessages'])->name('fetch');
    Route::post('/send', [CommunicationController::class, 'send'])->name('send');
    Route::post('/messages/send', [CommunicationController::class, 'send'])->name('messages.send');

    Route::get('/submissions', [ExamController::class, 'submissions'])->name('submissions.index');
    Route::get('/exams/{exam}/submissions', [ExamController::class, 'submissions'])->name('exams.submissions');
    Route::get('/submissions/{id}/grade', [ExamController::class, 'grade'])->name('submissions.grade');
    Route::post('/submissions/{id}/save-grade', [ExamController::class, 'saveGrade'])->name('submissions.saveGrade');
    Route::get('/exams/{id}/stats', [ExamController::class, 'stats'])->name('exams.stats');
    Route::resource('exams', ExamController::class);

    Route::get('/educational-contents', [EducationalContentController::class, 'index'])->name('educational_contents.index');
    Route::get('/teachers', [DashboardController::class, 'teachersIndex'])->name('teachers.index');

    // مسارات الطلاب بشكل آمن بدون تعارض
    Route::get('students/records/all', [StudentController::class, 'profile_all'])->name('students.profile_all');
    Route::get('students/profile/{id}', [StudentController::class, 'profile'])->name('students.profile');
    Route::resource('students', StudentController::class);

    // مسارات المعلمين المتغيرة
    Route::get('/teachers/{id}/edit', [AdminManagerController::class, 'teacherEdit'])->name('teachers.edit');
    Route::put('/teachers/{id}', [AdminManagerController::class, 'teacherUpdate'])->name('teachers.update');
    Route::delete('/teachers/{id}', [AdminManagerController::class, 'teacherDestroy'])->name('teachers.destroy');
    Route::get('/teachers/{id}', [DashboardController::class, 'showTeacher'])->name('teachers.show');

    // تسعير مواد التوجيهي والعروض الموسمية وباقات المواد
    Route::get('/subjects/pricing', [\App\Http\Controllers\Admin\SubjectPricingController::class, 'index'])->name('subjects.pricing');
    Route::post('/subjects/pricing/seasonal-discount', [\App\Http\Controllers\Admin\SubjectPricingController::class, 'applySeasonalDiscount'])->name('subjects.pricing.seasonal');
    Route::post('/subjects/pricing/{id}', [\App\Http\Controllers\Admin\SubjectPricingController::class, 'update'])->name('subjects.pricing.update');
    Route::post('/subjects/pricing/{id}/update', [\App\Http\Controllers\Admin\SubjectPricingController::class, 'update'])->name('subjects.pricing.update_alias');

    // إدارة الاشتراكات وعمليات الدفع والتحقق من الإيصالات
    Route::get('/payments', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{id}/status', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'updateStatus'])->name('payments.updateStatus');
    Route::get('/payments/{id}/receipt', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'viewReceipt'])->name('payments.receipt');

    // إدارة واعتماد شهادات ونتائج نهاية العام للثانوية العامة
    Route::get('/certificates', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates/toggle-publish', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'togglePublish'])->name('certificates.togglePublish');
    Route::post('/certificates/toggle-gpa', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'toggleGpa'])->name('certificates.toggleGpa');
    Route::post('/certificates/issue', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'issue'])->name('certificates.issue');
    Route::delete('/certificates/{id}', [\App\Http\Controllers\Admin\AdminCertificateController::class, 'destroy'])->name('certificates.destroy');
});


/*
|--------------------------------------------------------------------------
| 3. بوابة المدرس (Teacher Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'IsTeacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [ExamController::class, 'index'])->name('dashboard');
    Route::get('/submissions', [ExamController::class, 'submissions'])->name('submissions.index');
    Route::get('/exams/{exam}/submissions', [ExamController::class, 'submissions'])->name('exams.submissions');
    Route::get('/submissions/{submission}/grade', [ExamController::class, 'grade'])->name('submissions.grade');
    Route::post('/submissions/{submission}/save-grade', [ExamController::class, 'saveGrade'])->name('submissions.saveGrade');
    Route::resource('exams', ExamController::class);

    Route::get('/educational_contents', [EducationalContentController::class, 'index'])->name('educational_contents.index');
    Route::get('/educational_contents/create/{subject_id?}', [EducationalContentController::class, 'create'])->name('educational_contents.create');
    Route::post('/educational_contents', [EducationalContentController::class, 'store'])->name('educational_contents.store');
    Route::get('/educational_contents/{id}/edit', [EducationalContentController::class, 'edit'])->name('educational_contents.edit');
    Route::put('/educational_contents/{id}', [EducationalContentController::class, 'update'])->name('educational_contents.update');
    Route::delete('/educational_contents/{id}', [EducationalContentController::class, 'destroy'])->name('educational_contents.destroy');

    // إدارة اشتراكات وصلاحيات الطلاب في الفيديوهات والدروس والاختبارات عبر خانات الاختيار
    Route::get('/access-control', [\App\Http\Controllers\Teacher\StudentAccessController::class, 'index'])->name('access.index');
    Route::get('/students', [\App\Http\Controllers\Teacher\StudentAccessController::class, 'index'])->name('students.index');
    Route::get('/access/{enrollment_id}/contents', [\App\Http\Controllers\Teacher\StudentAccessController::class, 'getStudentContents'])->name('access.contents');
    Route::post('/access/{enrollment_id}/update', [\App\Http\Controllers\Teacher\StudentAccessController::class, 'updateAccess'])->name('access.update');
    Route::post('/access/quick-enroll', [\App\Http\Controllers\Teacher\StudentAccessController::class, 'quickEnroll'])->name('access.quickEnroll');
    Route::get('/access/item-students', [\App\Http\Controllers\Teacher\StudentAccessController::class, 'getItemStudents'])->name('access.itemStudents');
    Route::post('/access/toggle-item-student', [\App\Http\Controllers\Teacher\StudentAccessController::class, 'toggleItemStudent'])->name('access.toggleItemStudent');
    Route::post('/access/bulk-toggle-item-students', [\App\Http\Controllers\Teacher\StudentAccessController::class, 'bulkToggleItemStudents'])->name('access.bulkToggleItemStudents');

    Route::get('/inbox', [CommunicationController::class, 'teacherInbox'])->name('messages.index');
    Route::get('/messages/{student_id}', [CommunicationController::class, 'fetchTeacherStudentMessages'])->name('messages.fetch');
    Route::post('/send-message', [CommunicationController::class, 'sendFromTeacher'])->name('messages.send');

    Route::get('/chat', [CommunicationController::class, 'teacherAdminChat'])->name('teacher.admin.chat');
    Route::get('/messages', [CommunicationController::class, 'fetchTeacherAdminMessages']);
    Route::post('/send', [CommunicationController::class, 'sendFromTeacherToAdmin']);

    Route::get('/admin/chat', [CommunicationController::class, 'teacherAdminChat'])->name('admin.chat');
    Route::get('/admin/chat/messages', [CommunicationController::class, 'fetchTeacherAdminMessages'])->name('admin.chat.messages');
    Route::post('/admin/chat/send', [CommunicationController::class, 'sendFromTeacherToAdmin'])->name('admin.chat.send');
});


/*
|--------------------------------------------------------------------------
| 4. بوابة الطالب (Student Guard Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:student', 'IsStudent'])->prefix('student')->name('student.')->group(function () {
    Route::get('/pending-approval', [StudentController::class, 'pendingApproval'])->name('pending-approval');
    Route::get('/dashboard', [DashboardController::class, 'studentIndex'])->name('dashboard');
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::post('/profile/update-password', [StudentController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::get('/my-exams', [ExamController::class, 'studentIndex'])->name('exams.index');
    Route::get('/exams/{id}/take', [ExamController::class, 'takeExam'])->name('exams.take');
    Route::post('/exams/{id}/submit', [ExamController::class, 'submitExam'])->name('exams.submit');

    Route::get('/exams/{id}/result', [ExamController::class, 'showResult'])->name('exams.result');
    Route::get('/results/{id}', [ExamController::class, 'showResult'])->name('exam.results');
    Route::get('/exams/{id}/results', [ExamController::class, 'showResult'])->name('exams.results');

    Route::get('/support', [CommunicationController::class, 'studentChat'])->name('support');
    Route::get('/chat', [CommunicationController::class, 'studentChat'])->name('chat');

    // تم التعديل هنا لربط الجلب بدالة fetchMessages وتمرير ال admin_id بشكل صحيح
    Route::get('/support/fetch/{admin_id}', [CommunicationController::class, 'fetchMessages'])->name('support.fetch');
    Route::post('/support/send', [CommunicationController::class, 'sendFromStudent'])->name('support.send');

    Route::get('/messages/fetch/{admin_id}', [CommunicationController::class, 'fetchMessages'])->name('fetchMessages');
    Route::post('/messages/send', [CommunicationController::class, 'sendFromStudent'])->name('sendMessage');

    Route::get('/teachers', [CommunicationController::class, 'teachersIndex'])->name('teachers.index');
    Route::get('/teachers/{teacher_id}/chat', [CommunicationController::class, 'showTeacherChat'])->name('chat.teacher');
    Route::get('/teachers/{teacher_id}/conversation', [CommunicationController::class, 'showTeacherChat'])->name('teachers.chat');
    Route::post('/teachers/{teacher_id}/chat', [CommunicationController::class, 'sendToTeacher'])->name('chat.teacher.send');
    Route::get('/teachers/{teacher_id}/messages', [CommunicationController::class, 'fetchTeacherMessages'])->name('messages.teacher');
    Route::post('/teachers/send', [CommunicationController::class, 'sendToTeacher'])->name('send.teacher');

    // كتالوج المواد واختيار مادة أو أكثر وبوابات الدفع الفلسطينية (بال باي، جوال باي، بنك فلسطين)
    Route::get('/courses/catalog', [\App\Http\Controllers\Student\CourseEnrollmentController::class, 'catalog'])->name('courses.catalog');
    Route::post('/courses/checkout', [\App\Http\Controllers\Student\CourseEnrollmentController::class, 'prepareCheckout'])->name('courses.checkout');
    Route::get('/checkout', [\App\Http\Controllers\Student\PaymentGatewayController::class, 'showCheckout'])->name('checkout.show');
    Route::post('/checkout/process', [\App\Http\Controllers\Student\PaymentGatewayController::class, 'processPayment'])->name('checkout.process');
    Route::get('/checkout/receipt/{id}', [\App\Http\Controllers\Student\PaymentGatewayController::class, 'successReceipt'])->name('checkout.receipt');

    Route::get('/subjects', [DashboardController::class, 'studentSubjectsIndex'])->name('subjects.index');
    Route::get('/subjects/{id}', [DashboardController::class, 'studentSubjectShow'])->name('subjects.show');
    Route::get('/notifications', [App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [App\Http\Controllers\Student\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/mark-read', [App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Student\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/redeem-code', [StudentController::class, 'redeemCode'])->name('redeemCode');

    // مشغل الفيديو الذكي وملاحظات التوقيت
    Route::get('/video-notes/{content_id}', [\App\Http\Controllers\VideoNoteController::class, 'fetchNotes'])->name('videoNotes.fetch');
    Route::post('/video-notes', [\App\Http\Controllers\VideoNoteController::class, 'storeNote'])->name('videoNotes.store');
    Route::delete('/video-notes/{id}', [\App\Http\Controllers\VideoNoteController::class, 'destroyNote'])->name('videoNotes.destroy');
    Route::post('/video-progress', [\App\Http\Controllers\VideoNoteController::class, 'saveProgress'])->name('videoProgress.save');

    // بطاقات الاستذكار السريع والقوانين (Flashcards)
    Route::get('/flashcards', [\App\Http\Controllers\Student\FlashcardController::class, 'index'])->name('flashcards.index');

    // مؤشر الالتزام اليومي ولوحة الشرف (Leaderboard)
    Route::get('/leaderboard', [\App\Http\Controllers\Student\StreakController::class, 'leaderboard'])->name('leaderboard');
    Route::post('/streak/activity', [\App\Http\Controllers\Student\StreakController::class, 'recordActivity'])->name('streak.activity');

    // مولّد جدول المراجعة الذكي للامتحانات (Study Planner)
    Route::get('/study-planner', [\App\Http\Controllers\Student\StudyPlannerController::class, 'index'])->name('planner.index');
    Route::post('/study-planner/generate', [\App\Http\Controllers\Student\StudyPlannerController::class, 'generate'])->name('planner.generate');

    // الأوسمة والشهادات الأكاديمية الملكية ومؤقت التركيز وبومودورو
    Route::get('/achievements', [\App\Http\Controllers\SmartLearningController::class, 'myAchievements'])->name('achievements');
    Route::get('/certificates/{id}', [\App\Http\Controllers\SmartLearningController::class, 'showCertificate'])->name('certificates.show');
    Route::post('/pomodoro/session', [\App\Http\Controllers\SmartLearningController::class, 'savePomodoroSession'])->name('pomodoro.save');

    // قنوات التواصل التفاعلية
    Route::get('/channels', [\App\Http\Controllers\ChannelController::class, 'showChannelPage'])->name('channels.index');
    Route::post('/channels/join', [\App\Http\Controllers\ChannelController::class, 'joinRequest'])->name('channels.join');

    // تذاكر الدعم الفني
    Route::post('/support/ticket', [\App\Http\Controllers\CommunicationController::class, 'submitTicket'])->name('support.ticket');
});

// توافقية مسارات الإدارة القديمة والتسليم
Route::get('/admin/students-legacy', [StudentController::class, 'index'])->name('students.index');
Route::get('/admin/students/{student}/edit-legacy', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/admin/students/{student}/update-legacy', [StudentController::class, 'update'])->name('students.update');
Route::post('/student/exams/{id}/submit-legacy', [ExamController::class, 'submitExam'])->name('exams.submit');