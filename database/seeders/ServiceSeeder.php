<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $services = [
            [
                'name_en' => 'General Checkup',
                'name_ar' => 'كشف عام',
                'description_en' => 'Routine clinical evaluation with a doctor review, preventive guidance, and clear next steps for follow-up care.',
                'description_ar' => 'تقييم طبي روتيني مع مراجعة الطبيب ونصائح وقائية وخطوات واضحة للمتابعة.',
                'image' => '/assets/img/health/cardiology-2.webp',
                'icon' => 'fas fa-stethoscope',
                'feature_one_en' => 'Vital Signs Review',
                'feature_one_ar' => 'مراجعة العلامات الحيوية',
                'feature_two_en' => 'Doctor Consultation',
                'feature_two_ar' => 'استشارة الطبيب',
                'cta_en' => 'Learn More',
                'cta_ar' => 'اعرف المزيد',
            ],
            [
                'name_en' => 'Dental Care',
                'name_ar' => 'العناية بالأسنان',
                'description_en' => 'Dental assessment and treatment planning for pain relief, smile care, and long-term oral health.',
                'description_ar' => 'فحص الأسنان ووضع خطة علاج لتخفيف الألم والعناية بالابتسامة والحفاظ على صحة الفم.',
                'image' => '/assets/img/health/orthopedics-1.webp',
                'icon' => 'fas fa-tooth',
                'feature_one_en' => 'Teeth Cleaning',
                'feature_one_ar' => 'تنظيف الأسنان',
                'feature_two_en' => 'Treatment Planning',
                'feature_two_ar' => 'خطة علاجية',
                'cta_en' => 'Learn More',
                'cta_ar' => 'اعرف المزيد',
            ],
            [
                'name_en' => 'Skin Consultation',
                'name_ar' => 'استشارة جلدية',
                'description_en' => 'Professional evaluation for common skin conditions with clear diagnosis and personalized treatment guidance.',
                'description_ar' => 'تقييم متخصص لمشكلات الجلد الشائعة مع تشخيص واضح وخطة علاج مناسبة.',
                'image' => '/assets/img/health/dermatology-4.webp',
                'icon' => 'fas fa-hand-holding-medical',
                'feature_one_en' => 'Skin Analysis',
                'feature_one_ar' => 'تحليل البشرة',
                'feature_two_en' => 'Acne Treatment',
                'feature_two_ar' => 'علاج حب الشباب',
                'cta_en' => 'Learn More',
                'cta_ar' => 'اعرف المزيد',
            ],
        ];

        foreach ($services as $service) {
            Service::query()->updateOrCreate(
                ['name_en' => $service['name_en']],
                $service,
            );
        }
    }
}
