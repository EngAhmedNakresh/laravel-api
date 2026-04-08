<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description_ar');
            $table->string('icon')->nullable()->after('image');
            $table->string('feature_one_en')->nullable()->after('icon');
            $table->string('feature_one_ar')->nullable()->after('feature_one_en');
            $table->string('feature_two_en')->nullable()->after('feature_one_ar');
            $table->string('feature_two_ar')->nullable()->after('feature_two_en');
            $table->string('cta_en')->nullable()->after('feature_two_ar');
            $table->string('cta_ar')->nullable()->after('cta_en');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'image',
                'icon',
                'feature_one_en',
                'feature_one_ar',
                'feature_two_en',
                'feature_two_ar',
                'cta_en',
                'cta_ar',
            ]);
        });
    }
};