<?php

namespace App\Models;

use App\Traits\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = [
        'slug',
        'name_en',
        'name_ar',
        'short_description_en',
        'short_description_ar',
        'description_en',
        'description_ar',
        'card_image',
        'detail_image',
        'detail_image_secondary',
        'icon',
        'feature_one_en',
        'feature_one_ar',
        'feature_two_en',
        'feature_two_ar',
        'hero_badge_en',
        'hero_badge_ar',
        'hero_title_en',
        'hero_title_ar',
        'hero_text_en',
        'hero_text_ar',
        'stats',
        'primary_cta_en',
        'primary_cta_ar',
        'secondary_cta_en',
        'secondary_cta_ar',
        'floating_title_en',
        'floating_title_ar',
        'floating_text_en',
        'floating_text_ar',
        'services_title_en',
        'services_title_ar',
        'services_text_en',
        'services_text_ar',
        'services_list',
        'expertise_title_en',
        'expertise_title_ar',
        'expertise_lead_en',
        'expertise_lead_ar',
        'expertise_list',
        'emergency_label_en',
        'emergency_label_ar',
        'appointments_label_en',
        'appointments_label_ar',
        'appointments_value_en',
        'appointments_value_ar',
        'contact_phone',
    ];

    protected function casts(): array
    {
        return [
            'stats' => 'array',
            'services_list' => 'array',
            'expertise_list' => 'array',
        ];
    }
}
