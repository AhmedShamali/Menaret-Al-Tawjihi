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
    ExamSubmissionController,
    SupportController,
    StudentProfileController
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

Route::resource('educational_contents', EducationalContentController::class);


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

    Route::get('/teachers/info', [AdminManagerController::class, 'teachersInfo'])->name('teachers.info');
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
    Route::get('/dashboard', [DashboardController::class, 'studentIndex'])->name('dashboard');
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');

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
    Route::post('/teachers/{teacher_id}/chat', [CommunicationController::class, 'sendToTeacher'])->name('chat.teacher.send');
    Route::get('/teachers/{teacher_id}/messages', [CommunicationController::class, 'fetchTeacherMessages'])->name('messages.teacher');
    Route::post('/teachers/send', [CommunicationController::class, 'sendToTeacher'])->name('send.teacher');

    Route::get('/subjects', [DashboardController::class, 'studentSubjectsIndex'])->name('subjects.index');
    Route::get('/subjects/{id}', [DashboardController::class, 'studentSubjectShow'])->name('subjects.show');
    Route::get('/student/subjects/{id}', [DashboardController::class, 'showSubject'])->name('student.subjects.show');
    Route::get('/notifications', [App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications.index');
});