<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Schema\Blueprint;

class SchemaHealer
{
    /**
     * Run lightweight self-healing schema checks to ensure production database
     * has all required columns and tables without throwing 1054 Unknown column errors.
     */
    public static function heal(): void
    {
        try {
            // 1. فحص وتحديث جدول تسجيلات المواد للطلاب enrollments
            if (Schema::hasTable('enrollments')) {
                $missingEnrollmentCols = [];
                if (!Schema::hasColumn('enrollments', 'semester')) $missingEnrollmentCols[] = 'semester';
                if (!Schema::hasColumn('enrollments', 'region_applied')) $missingEnrollmentCols[] = 'region_applied';
                if (!Schema::hasColumn('enrollments', 'fee_amount')) $missingEnrollmentCols[] = 'fee_amount';
                if (!Schema::hasColumn('enrollments', 'paid_amount')) $missingEnrollmentCols[] = 'paid_amount';

                if (!empty($missingEnrollmentCols)) {
                    Schema::table('enrollments', function (Blueprint $table) use ($missingEnrollmentCols) {
                        if (in_array('semester', $missingEnrollmentCols)) {
                            $table->string('semester', 16)->default('both');
                        }
                        if (in_array('region_applied', $missingEnrollmentCols)) {
                            $table->string('region_applied', 20)->default('west_bank');
                        }
                        if (in_array('fee_amount', $missingEnrollmentCols)) {
                            $table->decimal('fee_amount', 8, 2)->default(0.00);
                        }
                        if (in_array('paid_amount', $missingEnrollmentCols)) {
                            $table->decimal('paid_amount', 8, 2)->default(0.00);
                        }
                    });
                }
            }

            // 2. فحص وتحديث تسعيرات المواد في جدول subjects
            if (Schema::hasTable('subjects')) {
                $missingSubjectCols = [];
                $priceCols = [
                    'price_term_1', 'price_term_2', 'price_full_year',
                    'price_term_1_gaza', 'price_term_2_gaza', 'price_full_year_gaza'
                ];
                foreach ($priceCols as $c) {
                    if (!Schema::hasColumn('subjects', $c)) {
                        $missingSubjectCols[] = $c;
                    }
                }

                if (!empty($missingSubjectCols)) {
                    Schema::table('subjects', function (Blueprint $table) use ($missingSubjectCols) {
                        foreach ($missingSubjectCols as $c) {
                            $table->decimal($c, 8, 2)->nullable();
                        }
                    });
                }
            }

            // 3. فحص وإنشاء جدول الاشتراكات والذمم الفصلية student_semester_subscriptions
            if (!Schema::hasTable('student_semester_subscriptions')) {
                Schema::create('student_semester_subscriptions', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                    $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
                    $table->string('academic_year', 16)->default('2026-2027');
                    $table->string('semester', 16)->default('term_1');
                    $table->string('region', 20)->default('west_bank');
                    $table->decimal('amount', 8, 2)->default(0.00);
                    $table->decimal('paid_amount', 8, 2)->default(0.00);
                    $table->string('status', 20)->default('unpaid');
                    $table->boolean('is_manual')->default(false);
                    $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
                    $table->timestamp('paid_at')->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();

                    $table->index(['student_id', 'academic_year', 'semester'], 'idx_student_year_semester');
                });
            }

            // 4. فحص أعمدة جدول الطلاب students الحيوية
            if (Schema::hasTable('students')) {
                $missingStudentCols = [];
                if (!Schema::hasColumn('students', 'region')) $missingStudentCols[] = 'region';
                if (!Schema::hasColumn('students', 'target_region')) $missingStudentCols[] = 'target_region';
                if (!Schema::hasColumn('students', 'plain_password')) $missingStudentCols[] = 'plain_password';
                if (!Schema::hasColumn('students', 'custom_discount_percent')) $missingStudentCols[] = 'custom_discount_percent';
                if (!Schema::hasColumn('students', 'custom_discount_fixed')) $missingStudentCols[] = 'custom_discount_fixed';
                if (!Schema::hasColumn('students', 'freeze_reason')) $missingStudentCols[] = 'freeze_reason';

                if (!empty($missingStudentCols)) {
                    Schema::table('students', function (Blueprint $table) use ($missingStudentCols) {
                        if (in_array('region', $missingStudentCols)) {
                            $table->string('region', 20)->nullable()->default('west_bank');
                        }
                        if (in_array('target_region', $missingStudentCols)) {
                            $table->string('target_region', 20)->nullable()->default('all');
                        }
                        if (in_array('plain_password', $missingStudentCols)) {
                            $table->string('plain_password')->nullable();
                        }
                        if (in_array('custom_discount_percent', $missingStudentCols)) {
                            $table->decimal('custom_discount_percent', 5, 2)->default(0.00);
                        }
                        if (in_array('custom_discount_fixed', $missingStudentCols)) {
                            $table->decimal('custom_discount_fixed', 8, 2)->default(0.00);
                        }
                        if (in_array('freeze_reason', $missingStudentCols)) {
                            $table->text('freeze_reason')->nullable();
                        }
                    });
                }
            }

            // 5. فحص عمود المنطقة المستهدفة ومن قام بالرفع في جدول المحتوى التعليمي
            if (Schema::hasTable('educational_contents')) {
                if (!Schema::hasColumn('educational_contents', 'target_region')) {
                    Schema::table('educational_contents', function (Blueprint $table) {
                        $table->string('target_region', 20)->default('all');
                    });
                }
                if (!Schema::hasColumn('educational_contents', 'uploaded_by')) {
                    Schema::table('educational_contents', function (Blueprint $table) {
                        $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                    });
                }
            }

            // 6. فحص عمود المنطقة المستهدفة في جدول الامتحانات
            if (Schema::hasTable('exams') && !Schema::hasColumn('exams', 'target_region')) {
                Schema::table('exams', function (Blueprint $table) {
                    $table->string('target_region', 20)->default('all');
                });
            }

            // 7. التأكد من دعم جدول المستخدمين users لدور المصور videographer وحقل plain_password
            if (Schema::hasTable('users')) {
                if (!Schema::hasColumn('users', 'plain_password')) {
                    Schema::table('users', function (Blueprint $table) {
                        $table->string('plain_password')->nullable();
                    });
                }
                try {
                    $driver = DB::getDriverName();
                    if (in_array($driver, ['mysql', 'mariadb'])) {
                        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(32) NOT NULL DEFAULT 'teacher'");
                    }
                } catch (\Throwable $e) {}
            }

            // 8. فحص وصيانة المحتوى التعليمي وربط صلاحيات وصول الطلبة تلقائياً
            if (Schema::hasTable('educational_contents')) {
                // تفعيل ظهور المحتويات التعليمية والتأكد من عدم حجبها بقيم فارغة
                DB::table('educational_contents')
                    ->whereNull('is_visible')
                    ->update(['is_visible' => true]);

                // التأكد من ضبط نوع الفيديو للمحتويات التي تمتلك مسار فيديو
                DB::table('educational_contents')
                    ->whereNotNull('url_path')
                    ->where('url_path', '!=', '')
                    ->where(function($q) {
                        $q->whereNull('type')->orWhere('type', '');
                    })
                    ->update(['type' => 'video']);

                // ربط الفيديوهات تلقائياً باشتراكات الطلاب النشطة للمواد
                if (Schema::hasTable('content_assignments') && Schema::hasTable('enrollments')) {
                    $contents = DB::table('educational_contents')->where('is_visible', true)->get(['id', 'subject_id']);
                    foreach ($contents as $c) {
                        $enrIds = DB::table('enrollments')
                            ->where('subject_id', $c->subject_id)
                            ->where('status', 'active')
                            ->pluck('id');
                        foreach ($enrIds as $eId) {
                            $exists = DB::table('content_assignments')
                                ->where('enrollment_id', $eId)
                                ->where('educational_content_id', $c->id)
                                ->exists();
                            if (!$exists) {
                                DB::table('content_assignments')->insert([
                                    'enrollment_id'          => $eId,
                                    'educational_content_id' => $c->id,
                                    'is_visible'             => true,
                                    'created_at'             => now(),
                                    'updated_at'             => now(),
                                ]);
                            }
                        }
                    }
                }
            }

            // 9. فحص وإنشاء جداول الذكاء الاصطناعي الأكاديمي الشامل
            if (!Schema::hasTable('ai_conversations')) {
                Schema::create('ai_conversations', function (Blueprint $table) {
                        $table->id();
                        $table->string('actor_type')->default('student');
                        $table->unsignedBigInteger('actor_id');
                        $table->string('title')->default('محادثة جديدة');
                        $table->string('category')->default('general');
                        $table->string('symbol_mode')->default('ar');
                        $table->timestamps();
                        $table->index(['actor_type', 'actor_id']);
                    });
                }

                if (!Schema::hasTable('ai_messages')) {
                    Schema::create('ai_messages', function (Blueprint $table) {
                        $table->id();
                        $table->foreignId('conversation_id')->constrained('ai_conversations')->onDelete('cascade');
                        $table->string('role')->default('user');
                        $table->longText('content');
                        $table->string('image_url')->nullable();
                        $table->string('notation_mode')->nullable();
                        $table->timestamps();
                        $table->index('conversation_id');
                    });
                }

                // 10. فحص وتحديث جدول أسئلة الامتحانات questions لدعم كافة الأنواع الموسعة وتفادي خطأ Data truncated
                if (Schema::hasTable('questions')) {
                    $driver = DB::getDriverName();
                    if (in_array($driver, ['mysql', 'mariadb'])) {
                        try {
                            DB::statement("ALTER TABLE `questions` MODIFY COLUMN `type` VARCHAR(64) NOT NULL DEFAULT 'mcq'");
                        } catch (\Throwable $e) {}
                        try {
                            DB::statement("ALTER TABLE `questions` MODIFY COLUMN `correct_answer` TEXT NULL");
                        } catch (\Throwable $e) {}
                    } elseif ($driver === 'pgsql') {
                        try {
                            DB::statement("ALTER TABLE questions ALTER COLUMN type TYPE VARCHAR(64)");
                        } catch (\Throwable $e) {}
                        try {
                            DB::statement("ALTER TABLE questions ALTER COLUMN correct_answer TYPE TEXT");
                        } catch (\Throwable $e) {}
                    }

                    $missingQuestionCols = [];
                    if (!Schema::hasColumn('questions', 'matching_pairs')) $missingQuestionCols[] = 'matching_pairs';
                    if (!Schema::hasColumn('questions', 'options_data')) $missingQuestionCols[] = 'options_data';
                    if (!Schema::hasColumn('questions', 'is_multiple')) $missingQuestionCols[] = 'is_multiple';
                    if (!Schema::hasColumn('questions', 'a_image')) $missingQuestionCols[] = 'a_image';
                    if (!Schema::hasColumn('questions', 'b_image')) $missingQuestionCols[] = 'b_image';
                    if (!Schema::hasColumn('questions', 'c_image')) $missingQuestionCols[] = 'c_image';
                    if (!Schema::hasColumn('questions', 'd_image')) $missingQuestionCols[] = 'd_image';

                    if (!empty($missingQuestionCols)) {
                        Schema::table('questions', function (Blueprint $table) use ($missingQuestionCols) {
                            if (in_array('matching_pairs', $missingQuestionCols)) $table->text('matching_pairs')->nullable();
                            if (in_array('options_data', $missingQuestionCols)) $table->text('options_data')->nullable();
                            if (in_array('is_multiple', $missingQuestionCols)) $table->boolean('is_multiple')->default(false);
                            if (in_array('a_image', $missingQuestionCols)) $table->string('a_image')->nullable();
                            if (in_array('b_image', $missingQuestionCols)) $table->string('b_image')->nullable();
                            if (in_array('c_image', $missingQuestionCols)) $table->string('c_image')->nullable();
                            if (in_array('d_image', $missingQuestionCols)) $table->string('d_image')->nullable();
                        });
                    }
                }

            } catch (\Throwable $e) {
                Log::warning('SchemaHealer warning: ' . $e->getMessage());
            }
    }
}
