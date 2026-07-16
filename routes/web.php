<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StageController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\EducationalContentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});


// ====================
// Stages
// ====================

Route::get('/stages', [StageController::class, 'index'])->name('stages.index');
Route::get('/stages/{id}', [StageController::class, 'show'])->name('stages.show');


// ====================
// Subjects
// ====================

Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
Route::get('/subject/{id}', [SubjectController::class, 'show'])->name('subject.show');


// ====================
// Educational Contents
// ====================

Route::resource('educational_contents', EducationalContentController::class);


// ====================
// Students
// ====================

Route::resource('students', StudentController::class);

Route::post(
    '/students/toggle-status/{id}',
    [StudentController::class, 'toggleStatus']
)->name('students.toggleStatus');


// ====================
// Questions
// ====================

Route::resource('questions', QuestionController::class);


// ====================
// Upload Limits (Testing)
// ====================

Route::get('/check-limit', function () {
    return [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size'       => ini_get('post_max_size'),
    ];
});


// ====================
// Admin Dashboard
// ====================

Route::get('/admin/dashboard', [DashboardController::class, 'adminIndex'])
    ->name('admin.dashboard');


// ====================
// Student Dashboard
// ====================

Route::get('/student/dashboard', [DashboardController::class, 'studentIndex'])
    ->name('student.dashboard');


// ====================
// Admin Routes
// ====================

Route::prefix('admin')->name('admin.')->group(function () {

    // Exams
    Route::resource('exams', ExamController::class);

    // Submissions
    Route::get('submissions', [ExamController::class, 'submissions'])
        ->name('submissions.index');

    Route::get('submissions/{id}/grade', [ExamController::class, 'grade'])
        ->name('submissions.grade');

    Route::post('submissions/{id}/save-grade', [ExamController::class, 'saveGrade'])
        ->name('submissions.saveGrade');

    // Exam Statistics
    Route::get('exams/{id}/stats', [ExamController::class, 'stats'])
        ->name('exams.stats');
});


// ====================
// Student Exam Portal
// ====================

Route::prefix('student')->name('student.')->group(function () {

    Route::get('my-exams', [ExamController::class, 'studentIndex'])
        ->name('exams.index');

    Route::get('exams/{id}/take', [ExamController::class, 'takeExam'])
        ->name('exams.take');

    Route::post('exams/{id}/submit', [ExamController::class, 'submitExam'])
        ->name('exams.submit');

    Route::get('results/{id}', [ExamController::class, 'showResult'])
        ->name('exams.result');

    Route::get('gradebook', [ExamController::class, 'gradebook'])
        ->name('gradebook');
});
