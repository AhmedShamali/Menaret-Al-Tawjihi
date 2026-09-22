<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\PastExam;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // نماذج امتحانات الثانوية العامة الرسمية - دولة فلسطين
        $officialExams = [
            // 2024 - العلمي
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الرياضيات (الورقة الأولى)',
                'total_marks' => 100,
                'downloads_count' => 1240,
                'notes' => 'الامتحان الوزاري الرسمي الموحد - الدورة الأولى (يونيو 2024)',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الرياضيات (الورقة الثانية)',
                'total_marks' => 100,
                'downloads_count' => 1120,
                'notes' => 'الامتحان الوزاري الرسمي الموحد - الدورة الأولى (يونيو 2024)',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الفيزياء',
                'total_marks' => 100,
                'downloads_count' => 980,
                'notes' => 'امتحان الفيزياء للفرع العلمي - جلسة يونيو 2024 مع المرفقات والرسومات البيانية',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الكيمياء',
                'total_marks' => 100,
                'downloads_count' => 840,
                'notes' => 'امتحان الكيمياء الرسمي - الدورة الأولى 2024',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'العلوم الحياتية (الأحياء)',
                'total_marks' => 100,
                'downloads_count' => 760,
                'notes' => 'امتحان الأحياء الرسمي لطلبة توجيهي العلمي - دورة 2024',
            ],
            // 2024 - المشترك والأدبي
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'literary',
                'subject_name' => 'اللغة العربية (المطالعة والنصوص)',
                'total_marks' => 100,
                'downloads_count' => 1430,
                'notes' => 'امتحان اللغة العربية الورقة الأولى لكافة الفروع الأكاديمية 2024',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'literary',
                'subject_name' => 'اللغة الإنجليزية',
                'total_marks' => 100,
                'downloads_count' => 1310,
                'notes' => 'امتحان اللغة الإنجليزية الرسمي المعتمد 2024',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'literary',
                'subject_name' => 'التاريخ',
                'total_marks' => 100,
                'downloads_count' => 620,
                'notes' => 'امتحان مبحث التاريخ للفرع الأدبي والشرعي 2024',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'literary',
                'subject_name' => 'الجغرافيا',
                'total_marks' => 100,
                'downloads_count' => 590,
                'notes' => 'امتحان مبحث الجغرافيا للفرع الأدبي 2024 مع الخرائط المعتمدة',
            ],
            // 2024 - الريادة والأعمال
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'business',
                'subject_name' => 'المحاسبة',
                'total_marks' => 100,
                'downloads_count' => 470,
                'notes' => 'امتحان مبحث المحاسبة لفرع الريادة والأعمال 2024',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'business',
                'subject_name' => 'إدارة المشاريع',
                'total_marks' => 100,
                'downloads_count' => 410,
                'notes' => 'امتحان إدارة المشاريع الريادية 2024',
            ],
            // 2023 - العلمي والأدبي
            [
                'year' => 2023,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الرياضيات (الورقة الأولى)',
                'total_marks' => 100,
                'downloads_count' => 2100,
                'notes' => 'الامتحان الوزاري الرسمي لعام 2023 - الدورة الأولى',
            ],
            [
                'year' => 2023,
                'session' => 'second',
                'branch' => 'scientific',
                'subject_name' => 'الرياضيات (الدورة الثانية)',
                'total_marks' => 100,
                'downloads_count' => 890,
                'notes' => 'امتحان الإكمال والتحسين - الدورة الثانية (أغسطس 2023)',
            ],
            [
                'year' => 2023,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الفيزياء',
                'total_marks' => 100,
                'downloads_count' => 1750,
                'notes' => 'امتحان الفيزياء للفرع العلمي - الدورة الأولى 2023',
            ],
            [
                'year' => 2023,
                'session' => 'first',
                'branch' => 'literary',
                'subject_name' => 'اللغة العربية (الورقة الأولى والثانية)',
                'total_marks' => 150,
                'downloads_count' => 1920,
                'notes' => 'امتحان اللغة العربية الشامل للفرع الأدبي 2023',
            ],
            // 2022
            [
                'year' => 2022,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الرياضيات العامة والعلمية',
                'total_marks' => 100,
                'downloads_count' => 2450,
                'notes' => 'امتحان الثانوية العامة الوزاري - دورة 2022',
            ],
            [
                'year' => 2022,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الفيزياء',
                'total_marks' => 100,
                'downloads_count' => 1880,
                'notes' => 'امتحان الفيزياء الوزاري الرسمي - دورة 2022',
            ],
        ];

        foreach ($officialExams as $examData) {
            PastExam::firstOrCreate(
                [
                    'year' => $examData['year'],
                    'session' => $examData['session'],
                    'branch' => $examData['branch'],
                    'subject_name' => $examData['subject_name'],
                ],
                $examData
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نحذف البيانات تجنباً لفقدان ملفات أضافها المستخدم
    }
};
