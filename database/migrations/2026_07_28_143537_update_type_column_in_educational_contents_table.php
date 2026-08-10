<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('educational_contents', function (Blueprint $table) {
            // تغيير نوع العمود ليتحمل أي قيمة مثل 'both' أو 'video' أو 'file'
            $table->string('type')->default('video')->change();
        });
    }

    public function down(): void
    {
        Schema::table('educational_contents', function (Blueprint $table) {
            $table->string('type', 10)->change();
        });
    }
};
