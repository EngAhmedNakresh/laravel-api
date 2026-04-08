<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('short_description_en')->nullable();
            $table->string('short_description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('card_image')->nullable();
            $table->string('detail_image')->nullable();
            $table->string('detail_image_secondary')->nullable();
            $table->string('icon')->nullable();
            $table->string('feature_one_en')->nullable();
            $table->string('feature_one_ar')->nullable();
            $table->string('feature_two_en')->nullable();
            $table->string('feature_two_ar')->nullable();
            $table->string('hero_badge_en')->nullable();
            $table->string('hero_badge_ar')->nullable();
            $table->string('hero_title_en')->nullable();
            $table->string('hero_title_ar')->nullable();
            $table->text('hero_text_en')->nullable();
            $table->text('hero_text_ar')->nullable();
            $table->json('stats')->nullable();
            $table->string('primary_cta_en')->nullable();
            $table->string('primary_cta_ar')->nullable();
            $table->string('secondary_cta_en')->nullable();
            $table->string('secondary_cta_ar')->nullable();
            $table->string('floating_title_en')->nullable();
            $table->string('floating_title_ar')->nullable();
            $table->text('floating_text_en')->nullable();
            $table->text('floating_text_ar')->nullable();
            $table->string('services_title_en')->nullable();
            $table->string('services_title_ar')->nullable();
            $table->text('services_text_en')->nullable();
            $table->text('services_text_ar')->nullable();
            $table->json('services_list')->nullable();
            $table->string('expertise_title_en')->nullable();
            $table->string('expertise_title_ar')->nullable();
            $table->text('expertise_lead_en')->nullable();
            $table->text('expertise_lead_ar')->nullable();
            $table->json('expertise_list')->nullable();
            $table->string('emergency_label_en')->nullable();
            $table->string('emergency_label_ar')->nullable();
            $table->string('appointments_label_en')->nullable();
            $table->string('appointments_label_ar')->nullable();
            $table->string('appointments_value_en')->nullable();
            $table->string('appointments_value_ar')->nullable();
            $table->string('contact_phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
