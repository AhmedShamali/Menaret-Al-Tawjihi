<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. مزامنة كلمات المرور الصريحة للطلاب الذين لا يملكون plain_password
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'plain_password')) {
            try {
                $students = DB::table('students')
                    ->whereNull('plain_password')
                    ->orWhere('plain_password', '')
                    ->select('id', 'password', 'nid', 'phone')
                    ->get();

                foreach ($students as $student) {
                    if (empty($student->password)) continue;

                    $candidates = array_filter([
                        '123456',
                        $student->nid ?? null,
                        $student->phone ?? null,
                        '12345678',
                        'password',
                    ]);

                    foreach ($candidates as $candidate) {
                        if (Hash::check($candidate, $student->password)) {
                            DB::table('students')->where('id', $student->id)->update([
                                'plain_password' => $candidate
                            ]);
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 2. مزامنة كلمات المرور للمعلمين والمدراء
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'plain_password')) {
            try {
                $users = DB::table('users')
                    ->whereNull('plain_password')
                    ->orWhere('plain_password', '')
                    ->select('id', 'password', 'phone')
                    ->get();

                foreach ($users as $user) {
                    if (empty($user->password)) continue;

                    $candidates = array_filter([
                        '44200479',
                        '123456',
                        $user->phone ?? null,
                        '12345678',
                        'password',
                        'admin123',
                    ]);

                    foreach ($candidates as $candidate) {
                        if (Hash::check($candidate, $user->password)) {
                            DB::table('users')->where('id', $user->id)->update([
                                'plain_password' => $candidate
                            ]);
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
