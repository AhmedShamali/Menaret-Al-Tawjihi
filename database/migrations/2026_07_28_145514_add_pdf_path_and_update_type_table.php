<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('educational_contents', function (Blueprint $table) {
            // إضافة حقل الـ PDF إذا لم يكن موجوداً
            if (!Schema::hasColumn('educational_contents', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('url_path');
            }
            // تعديل نوع العمود ليقبل جميع القيم بدون مشاكل Truncate
            $table->string('type')->default('video')->change();
        });
    }

    public function down(): void
    {
        Schema::table('educational_contents', function (Blueprint $table) {
            if (Schema::hasColumn('educational_contents', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }
        });
    }
};
