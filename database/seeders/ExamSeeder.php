<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $physics = \App\Models\Subject::where('subject_key', 'like', '%physics%')->first();
        $admin = \App\Models\User::first();

        if ($physics && $admin) {
            $exam = \App\Models\Exam::updateOrCreate(
                [
                    'subject_id' => $physics->id,
                    'title'      => 'اختبار تدريبي شامل - فيزياء التوجيهي (الوحدة الأولى)',
                ],
                [
                    'teacher_id'       => $admin->id,
                    'stage_id'         => $physics->stage_id,
                    'duration_minutes' => 45,
                    'is_published'     => true,
                    'is_active'        => true,
                    'status'           => 'published',
                ]
            );

            // إضافة أسئلة نموذجية للاختبار
            \App\Models\Question::updateOrCreate(
                [
                    'exam_id'       => $exam->id,
                    'question_text' => 'ما هي وحدة قياس التدفق المغناطيسي في النظام الدولي للوحدات (SI)؟',
                ],
                [
                    'type'           => 'multiple_choice',
                    'a'              => 'تسلا (Tesla)',
                    'b'              => 'ويبر (Weber)',
                    'c'              => 'هنري (Henry)',
                    'd'              => 'فولت (Volt)',
                    'correct_answer' => 'b',
                    'points'         => 5,
                    'require_file'   => false,
                ]
            );

            \App\Models\Question::updateOrCreate(
                [
                    'exam_id'       => $exam->id,
                    'question_text' => 'ينص قانون لنز على أن اتجاه التيار الحثي يكون بحيث:',
                ],
                [
                    'type'           => 'multiple_choice',
                    'a'              => 'يساعد التغير في التدفق المغناطيسي المسبب له',
                    'b'              => 'يقاوم التغير في التدفق المغناطيسي المسبب له',
                    'c'              => 'يزيد من شدة المجال المغناطيسي الأصلي دائماً',
                    'd'              => 'يكون مساوياً للصفر في الدارات المغلقة',
                    'correct_answer' => 'b',
                    'points'         => 5,
                    'require_file'   => false,
                ]
            );
        }
    }
}
