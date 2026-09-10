<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'google_id')) {
                    $table->string('google_id')->nullable()->after('email');
                }
                if (!Schema::hasColumn('students', 'provider')) {
                    $table->string('provider')->nullable()->after('google_id');
                }
                if (!Schema::hasColumn('students', 'provider_id')) {
                    $table->string('provider_id')->nullable()->after('provider');
                }
                if (!Schema::hasColumn('students', 'avatar_url')) {
                    $table->text('avatar_url')->nullable()->after('photo');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'google_id')) {
                    $table->string('google_id')->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'provider')) {
                    $table->string('provider')->nullable()->after('google_id');
                }
                if (!Schema::hasColumn('users', 'avatar_url')) {
                    $table->text('avatar_url')->nullable()->after('email');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (Schema::hasColumn('students', 'avatar_url')) {
                    $table->dropColumn('avatar_url');
                }
                if (Schema::hasColumn('students', 'provider_id')) {
                    $table->dropColumn('provider_id');
                }
                if (Schema::hasColumn('students', 'provider')) {
                    $table->dropColumn('provider');
                }
                if (Schema::hasColumn('students', 'google_id')) {
                    $table->dropColumn('google_id');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'avatar_url')) {
                    $table->dropColumn('avatar_url');
                }
                if (Schema::hasColumn('users', 'provider')) {
                    $table->dropColumn('provider');
                }
                if (Schema::hasColumn('users', 'google_id')) {
                    $table->dropColumn('google_id');
                }
            });
        }
    }
};
