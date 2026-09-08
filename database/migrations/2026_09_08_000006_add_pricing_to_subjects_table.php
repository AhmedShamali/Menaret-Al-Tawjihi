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
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'price_ils')) {
                $table->decimal('price_ils', 8, 2)->default(150.00)->after('color');
            }
            if (!Schema::hasColumn('subjects', 'discount_price_ils')) {
                $table->decimal('discount_price_ils', 8, 2)->nullable()->after('price_ils');
            }
            if (!Schema::hasColumn('subjects', 'is_free')) {
                $table->boolean('is_free')->default(false)->after('discount_price_ils');
            }
            if (!Schema::hasColumn('subjects', 'description')) {
                $table->text('description')->nullable()->after('is_free');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['price_ils', 'discount_price_ils', 'is_free', 'description']);
        });
    }
};
