<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * أمر تصدير وحفظ نسخة احتياطية لكافة بيانات المنصة
 * php artisan platform:backup
 */
Artisan::command('platform:backup', function () {
    $this->info('==> بدء عملية النسخ الاحتياطي لقاعدة بيانات المنصة...');

    $tables = [
        'settings',
        'stages',
        'subjects',
        'users',
        'students',
        'exams',
        'questions',
        'exam_submissions',
        'submission_answers',
        'certificates',
        'payments',
        'enrollments',
        'educational_contents',
        'flashcards',
        'messages',
        'notifications',
    ];

    $backupData = [
        'generated_at' => now()->toIso8601String(),
        'app_name' => config('app.name'),
        'tables' => [],
    ];

    $totalRecords = 0;

    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            $rows = DB::table($table)->get()->toArray();
            $count = count($rows);
            $backupData['tables'][$table] = $rows;
            $totalRecords += $count;
            $this->line(" [✓] تم نسخ جدول ({$table}): {$count} سجل");
        }
    }

    $dir = storage_path('app/backups');
    if (!File::exists($dir)) {
        File::makeDirectory($dir, 0755, true);
    }

    $fileName = 'backup_' . date('Y_m_d_His') . '.json';
    $filePath = $dir . '/' . $fileName;

    File::put($filePath, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $this->info("==> اكتمل النسخ الاحتياطي بنجاح! ({$totalRecords} سجل)");
    $this->info("==> مسار الملف: {$filePath}");
})->purpose('حفظ وتصدير نسخة احتياطية من كافة جداول وبيانات المنصة كملف JSON');

/**
 * أمر استرجاع نسخة احتياطية للمنصة
 * php artisan platform:restore {file}
 */
Artisan::command('platform:restore {file}', function ($file) {
    $this->warn('==> بدء استرجاع النسخة الاحتياطية...');

    $path = $file;
    if (!File::exists($path)) {
        $path = storage_path('app/backups/' . $file);
    }

    if (!File::exists($path)) {
        $this->error("الملف غير موجود في: {$path}");
        return 1;
    }

    $json = json_decode(File::get($path), true);
    if (!isset($json['tables'])) {
        $this->error('ملف النسخة الاحتياطية غير صالح أو تالف.');
        return 1;
    }

    foreach ($json['tables'] as $table => $rows) {
        if (Schema::hasTable($table) && !empty($rows)) {
            $inserted = 0;
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                // محاولة الإدراج الآمن دون تكرار
                if (isset($rowArray['id'])) {
                    $exists = DB::table($table)->where('id', $rowArray['id'])->exists();
                    if (!$exists) {
                        DB::table($table)->insert($rowArray);
                        $inserted++;
                    }
                }
            }
            $this->line(" [✓] جدول ({$table}): تم استرجاع {$inserted} سجل جديد");
        }
    }

    $this->info('==> تم استرجاع البيانات بنجاح وبأمان تام!');
})->purpose('استرجاع بيانات المنصة من ملف النسخة الاحتياطية');
