<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentApiController;

/*
|--------------------------------------------------------------------------
| API Routes for Educational Platform Mobile App (Tawjihi Palestine)
|--------------------------------------------------------------------------
| جاهزة للربط مع تطبيقات الهواتف الذكية (Flutter / React Native)
*/

Route::prefix('v1')->group(function () {
    // 1. المصادقة والحسابات
    Route::post('/auth/login', [StudentApiController::class, 'login']);
    Route::post('/auth/register', [StudentApiController::class, 'register']);

    // 2. المواد والدروس والصلاحيات
    Route::get('/student/dashboard', [StudentApiController::class, 'dashboard']);
    Route::get('/student/subjects', [StudentApiController::class, 'subjects']);
    Route::get('/student/subjects/{id}', [StudentApiController::class, 'subjectDetails']);

    // 3. المزامنة والتشغيل أوفلاين للهاتف
    Route::get('/student/offline-sync', [StudentApiController::class, 'offlineSync']);

    // 4. تفعيل كروت الشحن والأكواد
    Route::post('/student/redeem-voucher', [StudentApiController::class, 'redeemVoucher']);
});
