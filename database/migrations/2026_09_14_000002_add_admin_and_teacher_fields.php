<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'freeze_reason')) {
                    $table->text('freeze_reason')->nullable()->after('status');
                }
                if (!Schema::hasColumn('students', 'plain_password')) {
                    $table->string('plain_password')->nullable()->after('password');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'plain_password')) {
                    $table->string('plain_password')->nullable()->after('password');
                }
                if (!Schema::hasColumn('users', 'last_activity')) {
                    $table->timestamp('last_activity')->nullable()->after('remember_token');
                }
            });
        }

        if (Schema::hasTable('educational_contents')) {
            Schema::table('educational_contents', function (Blueprint $table) {
                if (!Schema::hasColumn('educational_contents', 'is_visible')) {
                    $table->boolean('is_visible')->default(true)->after('order');
                }
            });
        }

        if (Schema::hasTable('flashcards')) {
            Schema::table('flashcards', function (Blueprint $table) {
                if (!Schema::hasColumn('flashcards', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable()->after('difficulty');
                }
                if (!Schema::hasColumn('flashcards', 'is_custom')) {
                    $table->boolean('is_custom')->default(false)->after('difficulty');
                }
                if (!Schema::hasColumn('flashcards', 'is_hidden')) {
                    $table->boolean('is_hidden')->default(false)->after('difficulty');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn(['freeze_reason', 'plain_password']);
            });
        }
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['plain_password', 'last_activity']);
            });
        }
        if (Schema::hasTable('educational_contents')) {
            Schema::table('educational_contents', function (Blueprint $table) {
                $table->dropColumn(['is_visible']);
            });
        }
        if (Schema::hasTable('flashcards')) {
            Schema::table('flashcards', function (Blueprint $table) {
                $table->dropColumn(['student_id', 'is_custom', 'is_hidden']);
            });
        }
    }
};
