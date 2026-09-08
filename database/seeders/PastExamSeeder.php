<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PastExam;

class PastExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exams = [
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الرياضيات - الورقة الأولى',
                'exam_paper_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_math_paper_1.pdf',
                'answer_key_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_math_key_1.pdf',
                'total_marks' => 100,
                'notes' => 'امتحان الثانوية العامة للفرع العلمي - الدورة الأولى ويشمل وحدات التفاضل والتكامل وتطبيقاته.',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الفيزياء',
                'exam_paper_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_physics_paper.pdf',
                'answer_key_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_physics_key.pdf',
                'total_marks' => 100,
                'notes' => 'امتحان الفيزياء للفرع العلمي مع سلم توزيع الدرجات لمسائل كيرشوف والمجال الكهرومغناطيسي.',
            ],
            [
                'year' => 2024,
                'session' => 'first',
                'branch' => 'literary',
                'subject_name' => 'اللغة العربية (المطالعة والنصوص والعروض)',
                'exam_paper_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_arabic_paper.pdf',
                'answer_key_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_arabic_key.pdf',
                'total_marks' => 100,
                'notes' => 'امتحان الورقة الأولى للفرع الأدبي مع الإجابات النموذجية لأسئلة الإعراب والتحليل البلاغي.',
            ],
            [
                'year' => 2023,
                'session' => 'first',
                'branch' => 'scientific',
                'subject_name' => 'الكيمياء',
                'exam_paper_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_chemistry_paper.pdf',
                'answer_key_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_chemistry_key.pdf',
                'total_marks' => 100,
                'notes' => 'امتحان الكيمياء مع النموذج الوزاري المعتمد لحسابات الاتزان الكيميائي والحموض والقواعد.',
            ],
            [
                'year' => 2023,
                'session' => 'first',
                'branch' => 'literary',
                'subject_name' => 'الدراسات التاريخية',
                'exam_paper_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_history_paper.pdf',
                'answer_key_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_history_key.pdf',
                'total_marks' => 100,
                'notes' => 'امتحان التاريخ الوزاري ويشمل القضية الفلسطينية والثورات العربية مع نموذج الإجابة الرسمي.',
            ],
            [
                'year' => 2023,
                'session' => 'second',
                'branch' => 'scientific',
                'subject_name' => 'الرياضيات - الورقة الثانية',
                'exam_paper_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_math_paper_2.pdf',
                'answer_key_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_math_key_2.pdf',
                'total_marks' => 100,
                'notes' => 'امتحان الدورة الثانية للفرع العلمي ويشمل المصفوفات والمتجهات والهندسة الفراغية.',
            ],
            [
                'year' => 2023,
                'session' => 'first',
                'branch' => 'business',
                'subject_name' => 'المشاريع الريادية',
                'exam_paper_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_projects_paper.pdf',
                'answer_key_url' => 'https://raw.githubusercontent.com/AhmedShamali/Menaret-Al-Tawjihi/main/public/sample_projects_key.pdf',
                'total_marks' => 100,
                'notes' => 'امتحان فرع الريادة والأعمال الوزاري مع دراسة الجدوى وتأسيس المشاريع.',
            ]
        ];

        foreach ($exams as $item) {
            PastExam::updateOrCreate(
                [
                    'year' => $item['year'],
                    'session' => $item['session'],
                    'branch' => $item['branch'],
                    'subject_name' => $item['subject_name'],
                ],
                $item
            );
        }
    }
}
