<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'overrides',
    ];

    protected function casts(): array
    {
        return [
            'overrides' => 'array',
        ];
    }

    public static function defaults(): array
    {
        return [
            'topbarEmail' => '',
            'topbarPhone' => '',
            'brandEn' => '',
            'brandAr' => '',
            'footerTitleEn' => '',
            'footerTitleAr' => '',
            'footerDescriptionEn' => '',
            'footerDescriptionAr' => '',
            'footerAddressEn' => '',
            'footerAddressAr' => '',
            'footerPhone' => '',
            'footerEmail' => '',
            'heroTitleEn' => '',
            'heroTitleAr' => '',
            'heroSubtitleEn' => '',
            'heroSubtitleAr' => '',
            'heroPrimaryCtaEn' => '',
            'heroPrimaryCtaAr' => '',
            'heroSecondaryCtaEn' => '',
            'heroSecondaryCtaAr' => '',
            'heroHotlineLabelEn' => '',
            'heroHotlineLabelAr' => '',
            'heroHotlineNumber' => '',
            'heroImageUrl' => '',
            'aboutTitleEn' => '',
            'aboutTitleAr' => '',
            'aboutLeadEn' => '',
            'aboutLeadAr' => '',
            'aboutBodyEn' => '',
            'aboutBodyAr' => '',
            'aboutPrimaryCtaEn' => '',
            'aboutPrimaryCtaAr' => '',
            'aboutImageUrl' => '',
            'aboutCardTitleEn' => '',
            'aboutCardTitleAr' => '',
            'aboutCardTextEn' => '',
            'aboutCardTextAr' => '',
            'aboutBadgeValue' => '',
            'aboutBadgeLabelEn' => '',
            'aboutBadgeLabelAr' => '',
            'departmentsTitleEn' => '',
            'departmentsTitleAr' => '',
            'departmentsSubtitleEn' => '',
            'departmentsSubtitleAr' => '',
            'emergencyTitleEn' => '',
            'emergencyTitleAr' => '',
            'emergencySubtitleEn' => '',
            'emergencySubtitleAr' => '',
            'emergencyButtonEn' => '',
            'emergencyButtonAr' => '',
            'emergencyPhone' => '',
            'servicesSectionTitleEn' => '',
            'servicesSectionTitleAr' => '',
            'servicesSectionSubtitleEn' => '',
            'servicesSectionSubtitleAr' => '',
            'servicesSpotlightBadgeEn' => '',
            'servicesSpotlightBadgeAr' => '',
            'servicesSpotlightTitleEn' => '',
            'servicesSpotlightTitleAr' => '',
            'servicesSpotlightSubtitleEn' => '',
            'servicesSpotlightSubtitleAr' => '',
            'servicesSpotlightCtaEn' => '',
            'servicesSpotlightCtaAr' => '',
            'servicesSpotlightImageUrl' => '',
            'findDoctorTitleEn' => '',
            'findDoctorTitleAr' => '',
            'findDoctorSubtitleEn' => '',
            'findDoctorSubtitleAr' => '',
            'findDoctorSearchTitleEn' => '',
            'findDoctorSearchTitleAr' => '',
            'findDoctorSearchSubtitleEn' => '',
            'findDoctorSearchSubtitleAr' => '',
            'findDoctorNamePlaceholderEn' => '',
            'findDoctorNamePlaceholderAr' => '',
            'findDoctorAllSpecialtiesEn' => '',
            'findDoctorAllSpecialtiesAr' => '',
            'findDoctorSearchButtonEn' => '',
            'findDoctorSearchButtonAr' => '',
            'findDoctorViewDetailsEn' => '',
            'findDoctorViewDetailsAr' => '',
            'findDoctorScheduleEn' => '',
            'findDoctorScheduleAr' => '',
            'findDoctorBookNowEn' => '',
            'findDoctorBookNowAr' => '',
            'findDoctorViewAllEn' => '',
            'findDoctorViewAllAr' => '',
            'callToActionTitleEn' => '',
            'callToActionTitleAr' => '',
            'callToActionSubtitleEn' => '',
            'callToActionSubtitleAr' => '',
            'callToActionPrimaryCtaEn' => '',
            'callToActionPrimaryCtaAr' => '',
            'callToActionSecondaryCtaEn' => '',
            'callToActionSecondaryCtaAr' => '',
            'callToActionImageUrl' => '',
            'callToActionContactTitleEn' => '',
            'callToActionContactTitleAr' => '',
            'callToActionContactTextEn' => '',
            'callToActionContactTextAr' => '',
            'callToActionPhone' => '',
            'callToActionLocationCtaEn' => '',
            'callToActionLocationCtaAr' => '',
        ];
    }

    public static function ensureSeeded(): self
    {
        $record = static::query()->firstOrCreate([], [
            'overrides' => static::defaults(),
        ]);

        $record->updateQuietly([
            'overrides' => array_replace(static::defaults(), $record->overrides ?? []),
        ]);

        return $record->fresh();
    }
}
