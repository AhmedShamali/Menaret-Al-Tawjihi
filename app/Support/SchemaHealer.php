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

        } catch (\Throwable $e) {
            Log::warning('SchemaHealer warning: ' . $e->getMessage());
        }
    }
}
