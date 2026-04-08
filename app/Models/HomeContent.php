<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero',
        'about',
        'sections',
    ];

    protected function casts(): array
    {
        return [
            'hero' => 'array',
            'about' => 'array',
            'sections' => 'array',
        ];
    }

    public static function defaults(): array
    {
        return [
            'hero' => [
                'badges' => [
                    ['en' => 'Trusted Care', 'ar' => 'رعاية موثوقة'],
                    ['en' => '24/7 Emergency', 'ar' => 'طوارئ 24/7'],
                    ['en' => '4.9/5 Rating', 'ar' => 'تقييم 4.9/5'],
                ],
                'title' => [
                    'en' => 'Our medical team care about your health.',
                    'ar' => 'فريقنا الطبي يهتم بصحتك.',
                ],
                'subtitle' => [
                    'en' => 'Your health journey starts here. Book trusted doctors, explore services, and follow clear next steps—online, anytime.',
                    'ar' => 'تبدأ رحلتك الصحية هنا. احجز أطباء موثوقين، استكشف الخدمات، واتبع خطوات واضحة—عبر الإنترنت في أي وقت.',
                ],
                'primary_cta' => [
                    'en' => 'Book Appointment',
                    'ar' => 'احجز موعداً',
                ],
                'secondary_cta' => [
                    'en' => 'Watch Our Story',
                    'ar' => 'شاهد قصتنا',
                ],
                'image' => '/assets/img/health/staff-10.webp',
                'feature' => [
                    'title' => ['en' => 'Next Available', 'ar' => 'أقرب موعد متاح'],
                    'value' => ['en' => 'Today 2:30 PM', 'ar' => 'اليوم 2:30 م'],
                    'caption' => ['en' => 'Typical slot — exact time confirmed when you book.', 'ar' => 'موعد تقريبي — يُثبت الوقت عند تأكيد الحجز.'],
                ],
                'rating' => [
                    'score' => '4.9/5',
                    'count' => ['en' => '1,234 Reviews', 'ar' => '1,234 تقييماً'],
                ],
                'hotline_label' => ['en' => 'Emergency Hotline', 'ar' => 'خط الطوارئ'],
                'hotline_number' => '+20 100 123 4567',
            ],
            'about' => [
                'title' => ['en' => 'Compassionate Care, Advanced Medicine', 'ar' => 'رعاية إنسانية وطب متقدم'],
                'lead' => [
                    'en' => 'For years, we have focused on combining high medical standards with a calmer and more personal patient experience.',
                    'ar' => 'منذ سنوات نركز على الجمع بين معايير طبية عالية وتجربة مريض أكثر هدوءاً وشخصية.',
                ],
                'body' => [
                    'en' => 'Browse clinical areas — the content below is loaded from the database / dashboard.',
                    'ar' => 'استعرض التخصصات السريرية — المحتوى أدناه يُحمّل من قاعدة البيانات / لوحة التحكم.',
                ],
                'primary_cta' => ['en' => 'Learn More About Us', 'ar' => 'اعرف المزيد عنا'],
                'image' => '/assets/img/health/facilities-9.webp',
                'card_title' => ['en' => '24/7 Emergency Care', 'ar' => 'رعاية طوارئ 24/7'],
                'card_text' => ['en' => 'Always here when you need us most.', 'ar' => 'نحن هنا عندما تحتاجنا أكثر.'],
                'badge_value' => '25+',
                'badge_label' => ['en' => 'Years of Trusted Care', 'ar' => 'سنوات من الرعاية الموثوقة'],
            ],
            'sections' => [
                'services' => [
                    'title' => ['en' => 'Featured Services', 'ar' => 'خدمات مميزة'],
                    'subtitle' => [
                        'en' => 'From general checkups to dental and skin care — services shaped around your family’s daily needs.',
                        'ar' => 'من الكشف العام إلى الأسنان والجلد — خدمات مصممة حول احتياجات عائلتك اليومية.',
                    ],
                ],
                'doctors' => [
                    'title' => ['en' => 'Meet Our Doctors', 'ar' => 'تعرّف على أطبائنا'],
                    'subtitle' => [
                        'en' => 'Browse profiles, specialties, and experience to choose the doctor who fits your case.',
                        'ar' => 'تعرّف على السيرة والتخصص والخبرة لاختيار الطبيب الأنسب لحالتك.',
                    ],
                ],
                'feedback' => [
                    'title' => ['en' => 'Patient Feedback', 'ar' => 'آراء المرضى'],
                    'subtitle' => [
                        'en' => 'Real feedback from patients who visited our clinic.',
                        'ar' => 'آراء حقيقية من مرضى زاروا عيادتنا.',
                    ],
                    'cta' => ['en' => 'See All Feedback', 'ar' => 'عرض كل التقييمات'],
                ],
                'departments' => [
                    'title' => ['en' => 'Featured Departments', 'ar' => 'أقسام مميزة'],
                    'subtitle' => [
                        'en' => 'Browse clinical areas — the content below is loaded from the database / dashboard.',
                        'ar' => 'القلب، الأعصاب، العظام، وغيرها — أقسام تتعاون من أجل صحتك.',
                    ],
                    'featured' => [
                        [
                            'label' => ['en' => 'Specialized Care', 'ar' => 'رعاية متخصصة'],
                            'title' => ['en' => 'Cardiovascular Medicine', 'ar' => 'طب القلب والأوعية الدموية'],
                            'description' => ['en' => 'Advanced diagnostics and interventional care for complete heart health management.', 'ar' => 'تشخيص وعلاج تدخلي متقدم لإدارة صحة القلب بشكل متكامل.'],
                            'feature_1' => ['en' => '24/7 Emergency Cardiac Care', 'ar' => 'رعاية قلبية طارئة 24/7'],
                            'feature_2' => ['en' => 'Minimally Invasive Procedures', 'ar' => 'إجراءات طفيفة التوغل'],
                            'cta' => ['en' => 'Explore Cardiology', 'ar' => 'استكشف أمراض القلب'],
                            'image' => '/assets/img/health/cardiology-1.webp',
                            'icon' => 'bi bi-heart-pulse',
                        ],
                        [
                            'label' => ['en' => 'Expert Care', 'ar' => 'رعاية خبيرة'],
                            'title' => ['en' => 'Neurological Sciences', 'ar' => 'علوم الأعصاب'],
                            'description' => ['en' => 'Modern neuroimaging and treatment pathways for complex brain and spine conditions.', 'ar' => 'تصوير عصبي حديث ومسارات علاج للحالات المعقدة للدماغ والعمود الفقري.'],
                            'feature_1' => ['en' => 'Advanced Brain Imaging', 'ar' => 'تصوير دماغي متقدم'],
                            'feature_2' => ['en' => 'Robotic Surgery', 'ar' => 'جراحة روبوتية'],
                            'cta' => ['en' => 'Explore Neurology', 'ar' => 'استكشف الأعصاب'],
                            'image' => '/assets/img/health/neurology-4.webp',
                            'icon' => 'bi bi-cpu',
                        ],
                    ],
                    'highlights' => [
                        [
                            'title' => ['en' => 'Orthopedic Surgery', 'ar' => 'جراحة العظام'],
                            'description' => ['en' => 'Comprehensive musculoskeletal care with modern surgical pathways.', 'ar' => 'رعاية شاملة للجهاز الحركي مع مسارات جراحية حديثة.'],
                            'items' => [
                                ['en' => 'Sports Medicine', 'ar' => 'طب الرياضة'],
                                ['en' => 'Joint Replacement', 'ar' => 'استبدال المفاصل'],
                                ['en' => 'Spine Surgery', 'ar' => 'جراحات العمود الفقري'],
                            ],
                            'cta' => ['en' => 'Learn More', 'ar' => 'اعرف المزيد'],
                            'icon' => 'bi bi-shield-plus',
                        ],
                        [
                            'title' => ['en' => 'Pediatric Care', 'ar' => 'رعاية الأطفال'],
                            'description' => ['en' => 'Child-centered care from infancy to adolescence with family-focused support.', 'ar' => 'رعاية تتمحور حول الطفل من حديثي الولادة حتى المراهقة مع دعم للأسرة.'],
                            'items' => [
                                ['en' => 'Neonatal Intensive Care', 'ar' => 'رعاية حديثي الولادة المركزة'],
                                ['en' => 'Developmental Pediatrics', 'ar' => 'طب الأطفال التنموي'],
                                ['en' => 'Pediatric Surgery', 'ar' => 'جراحة الأطفال'],
                            ],
                            'cta' => ['en' => 'Learn More', 'ar' => 'اعرف المزيد'],
                            'icon' => 'bi bi-people',
                        ],
                        [
                            'title' => ['en' => 'Cancer Treatment', 'ar' => 'علاج الأورام'],
                            'description' => ['en' => 'A multidisciplinary oncology program with modern personalized treatment options.', 'ar' => 'برنامج أورام متعدد التخصصات بخيارات علاج حديثة ومخصصة.'],
                            'items' => [
                                ['en' => 'Precision Medicine', 'ar' => 'الطب الدقيق'],
                                ['en' => 'Immunotherapy', 'ar' => 'العلاج المناعي'],
                                ['en' => 'Radiation Oncology', 'ar' => 'العلاج الإشعاعي'],
                            ],
                            'cta' => ['en' => 'Learn More', 'ar' => 'اعرف المزيد'],
                            'icon' => 'bi bi-activity',
                        ],
                    ],
                ],
                'services_spotlight' => [
                    'title' => ['en' => 'Comprehensive Healthcare Excellence', 'ar' => 'تميز شامل في الرعاية الصحية'],
                    'subtitle' => [
                        'en' => 'Emergency support, follow-up visits, and everyday care — one clinic for every step of your journey.',
                        'ar' => 'دعم طارئ، متابعة، ورعاية يومية — عيادة واحدة لكل مراحل رحلتك الصحية.',
                    ],
                    'badge' => ['en' => 'Emergency Care', 'ar' => 'رعاية طارئة'],
                    'image' => '/assets/img/health/consultation-4.webp',
                    'cta' => ['en' => 'Explore Our Services', 'ar' => 'استكشف خدماتنا'],
                    'circles' => [
                        [
                            'title' => ['en' => 'Maternal Care', 'ar' => 'رعاية الأمومة'],
                            'subtitle' => ['en' => 'Expert pregnancy & delivery support', 'ar' => 'دعم متخصص للحمل والولادة'],
                            'image' => '/assets/img/health/maternal-2.webp',
                        ],
                        [
                            'title' => ['en' => 'Vaccination', 'ar' => 'التطعيمات'],
                            'subtitle' => ['en' => 'Complete immunization programs', 'ar' => 'برامج تطعيم متكاملة'],
                            'image' => '/assets/img/health/vaccination-3.webp',
                        ],
                        [
                            'title' => ['en' => 'Emergency Care', 'ar' => 'رعاية طارئة'],
                            'subtitle' => ['en' => '24/7 critical care services', 'ar' => 'خدمات رعاية حرجة على مدار الساعة'],
                            'image' => '/assets/img/health/emergency-2.webp',
                        ],
                        [
                            'title' => ['en' => 'Advanced Technology', 'ar' => 'تقنيات متقدمة'],
                            'subtitle' => ['en' => 'State-of-the-art medical equipment', 'ar' => 'معدات طبية حديثة'],
                            'image' => '/assets/img/health/facilities-6.webp',
                        ],
                    ],
                ],
                'emergency_banner' => [
                    'title' => ['en' => 'Emergency Services Available 24/7', 'ar' => 'خدمات طوارئ متاحة 24/7'],
                    'subtitle' => ['en' => 'Our emergency department is ready with fast response teams and modern care capabilities.', 'ar' => 'قسم الطوارئ لدينا جاهز بفرق استجابة سريعة وإمكانيات رعاية حديثة.'],
                    'button' => ['en' => 'Call Emergency', 'ar' => 'اتصل بالطوارئ'],
                    'phone' => '+20 100 123 4567',
                ],
                'call_to_action' => [
                    'title' => [
                        'en' => 'Excellence in medical care, every day',
                        'ar' => 'تميّز في الرعاية الطبية كل يوم',
                    ],
                    'subtitle' => [
                        'en' => 'Trusted doctors, modern equipment, and a calm patient experience from your first visit through follow-up.',
                        'ar' => 'أطباء موثوقون، معدات حديثة، وتجربة مريض هادئة من أول زيارة حتى المتابعة.',
                    ],
                    'primary_cta' => ['en' => 'Book a consultation', 'ar' => 'احجز استشارة'],
                    'secondary_cta' => ['en' => 'Explore services', 'ar' => 'استكشف الخدمات'],
                    'image' => '/assets/img/health/facilities-9.webp',
                    'features' => [
                        [
                            'title' => ['en' => 'Modern diagnostics', 'ar' => 'تشخيص حديث'],
                            'text' => [
                                'en' => 'Imaging and lab support that help your doctor decide the right next step with confidence.',
                                'ar' => 'تصوير ومختبر يساعدان طبيبك على اختيار الخطوة التالية بثقة.',
                            ],
                            'icon' => 'bi bi-shield-check',
                        ],
                        [
                            'title' => ['en' => 'When you need us', 'ar' => 'عندما تحتاجنا'],
                            'text' => [
                                'en' => 'Extended hours and emergency lines so urgent concerns get a fast, clear response.',
                                'ar' => 'ساعات موسّعة وخطوط طوارئ لتصل بسرعة عند الحاجة.',
                            ],
                            'icon' => 'bi bi-clock',
                        ],
                        [
                            'title' => ['en' => 'One coordinated team', 'ar' => 'فريق واحد منسّق'],
                            'text' => [
                                'en' => 'Specialists, nurses, and reception work together so your visit feels organized and personal.',
                                'ar' => 'أخصائيون وممرضون واستقبال يعملون معًا لتكون زيارتك منظمة وشخصية.',
                            ],
                            'icon' => 'bi bi-people',
                        ],
                    ],
                    'contact_title' => ['en' => 'Need help right now?', 'ar' => 'تحتاج مساعدة الآن؟'],
                    'contact_text' => [
                        'en' => 'Call our hotline for urgent questions or directions to the clinic.',
                        'ar' => 'اتصل بخط المساعدة للأسئلة العاجلة أو معرفة طريق العيادة.',
                    ],
                    'phone' => '+20 100 123 4567',
                    'location_cta' => ['en' => 'Find Location', 'ar' => 'اعثر على الموقع'],
                ],
                'find_doctor' => [
                    'title' => ['en' => 'Find A Doctor', 'ar' => 'ابحث عن طبيب'],
                    'subtitle' => ['en' => 'Search through our comprehensive directory of experienced medical professionals.', 'ar' => 'ابحث في دليلنا الشامل لأطباء ذوي خبرة.'],
                    'search_title' => ['en' => 'Find Your Perfect Healthcare Provider', 'ar' => 'اعثر على مقدم الرعاية المناسب'],
                    'search_subtitle' => ['en' => 'Search by doctor name or specialty to reach the right provider faster.', 'ar' => 'ابحث باسم الطبيب أو التخصص للوصول إلى المقدم المناسب بسرعة.'],
                    'name_placeholder' => ['en' => 'Enter doctor name', 'ar' => 'اكتب اسم الطبيب'],
                    'all_specialties' => ['en' => 'All Specialties', 'ar' => 'كل التخصصات'],
                    'search_button' => ['en' => 'Find Doctors', 'ar' => 'ابحث عن الأطباء'],
                    'view_details' => ['en' => 'View Details', 'ar' => 'عرض التفاصيل'],
                    'book_now' => ['en' => 'Book Now', 'ar' => 'احجز الآن'],
                    'schedule' => ['en' => 'Schedule', 'ar' => 'جدولة'],
                    'view_all' => ['en' => 'View All Doctors', 'ar' => 'عرض كل الأطباء'],
                    'circle_intro' => ['en' => 'Trusted specialists across our clinic', 'ar' => 'أخصائيون موثوقون في عيادتنا'],
                    'card_meta' => [
                        ['status' => 'available', 'rating' => '4.9', 'reviews' => ['en' => '(127 reviews)', 'ar' => '(127 تقييماً)'], 'experience' => ['en' => '14 years experience', 'ar' => '14 سنة خبرة'], 'primary_mode' => 'book'],
                        ['status' => 'busy', 'rating' => '4.8', 'reviews' => ['en' => '(89 reviews)', 'ar' => '(89 تقييماً)'], 'experience' => ['en' => '16 years experience', 'ar' => '16 سنة خبرة'], 'primary_mode' => 'schedule'],
                        ['status' => 'available', 'rating' => '5.0', 'reviews' => ['en' => '(203 reviews)', 'ar' => '(203 تقييماً)'], 'experience' => ['en' => '11 years experience', 'ar' => '11 سنة خبرة'], 'primary_mode' => 'book'],
                        ['status' => 'offline', 'rating' => '4.7', 'reviews' => ['en' => '(156 reviews)', 'ar' => '(156 تقييماً)'], 'experience' => ['en' => '22 years experience', 'ar' => '22 سنة خبرة'], 'primary_mode' => 'schedule'],
                        ['status' => 'available', 'rating' => '4.5', 'reviews' => ['en' => '(74 reviews)', 'ar' => '(74 تقييماً)'], 'experience' => ['en' => '9 years experience', 'ar' => '9 سنوات خبرة'], 'primary_mode' => 'book'],
                        ['status' => 'available', 'rating' => '4.9', 'reviews' => ['en' => '(194 reviews)', 'ar' => '(194 تقييماً)'], 'experience' => ['en' => '19 years experience', 'ar' => '19 سنة خبرة'], 'primary_mode' => 'schedule'],
                    ],
                ],
            ],
        ];
    }

    public static function ensureSeeded(): self
    {
        $content = static::query()->firstOrCreate([], static::defaults());

        $content->updateQuietly([
            'hero' => array_replace_recursive(static::defaults()['hero'], $content->hero ?? []),
            'about' => array_replace_recursive(static::defaults()['about'], $content->about ?? []),
            'sections' => array_replace_recursive(static::defaults()['sections'], $content->sections ?? []),
        ]);

        return $content->fresh();
    }
}