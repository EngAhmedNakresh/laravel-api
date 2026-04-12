<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آراء العملاء</title>
    <link rel="stylesheet" href="{{ asset('assets/css/testimonials.css') }}">
</head>
<body>
    <main class="testimonials-page">
        <section class="hero-shell">
            <div class="hero-copy">
                <span class="eyebrow">Testimonials</span>
                <h1>آراء عملائنا وتجاربهم معنا</h1>
                <p>
                    شارك تجربتك مع العيادة، واستعرض تقييمات العملاء بطريقة واضحة وأنيقة تشبه صفحات
                    <span>Clinic Testimonials</span>.
                </p>
            </div>

            <div class="hero-panel">
                <div class="hero-stat">
                    <strong>{{ $feedback->total() }}</strong>
                    <span>إجمالي التقييمات</span>
                </div>
                <div class="hero-stat">
                    <strong>{{ number_format((float) $feedback->avg('rating'), 1) }}</strong>
                    <span>متوسط النجوم في هذه الصفحة</span>
                </div>
            </div>
        </section>

        <section class="feedback-composer">
            <div class="composer-head">
                <div>
                    <span class="eyebrow">Leave A Review</span>
                    <h2>أضف تقييمك</h2>
                </div>

                @auth
                    <div class="current-user">
                        <img
                            src="{{ \App\Support\PublicAssetUrl::from(auth()->user()->avatar) }}"
                            alt="{{ auth()->user()->name }}"
                        >
                        <div>
                            <strong>{{ auth()->user()->name }}</strong>
                            <span>سيتم عرض صورتك من ملف العميل</span>
                        </div>
                    </div>
                @endauth
            </div>

            @if (session('status'))
                <div class="flash success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="flash error">
                    {{ $errors->first() }}
                </div>
            @endif

            @auth
                <form action="{{ route('testimonials.store') }}" method="POST" class="review-form">
                    @csrf

                    <div class="stars-input" role="radiogroup" aria-label="اختر التقييم">
                        @for ($star = 5; $star >= 1; $star--)
                            <input
                                type="radio"
                                id="rating-{{ $star }}"
                                name="rating"
                                value="{{ $star }}"
                                {{ (int) old('rating', 5) === $star ? 'checked' : '' }}
                            >
                            <label for="rating-{{ $star }}" title="{{ $star }} نجوم">★</label>
                        @endfor
                    </div>

                    <label class="field">
                        <span>اكتب رأيك</span>
                        <textarea
                            name="message"
                            rows="5"
                            maxlength="1000"
                            placeholder="احكِ لنا عن تجربتك مع الأطباء، الاستقبال، أو الخدمة."
                        >{{ old('message') }}</textarea>
                    </label>

                    <button type="submit" class="submit-btn">إرسال التقييم</button>
                </form>
            @else
                <div class="login-note">
                    <h3>تسجيل الدخول مطلوب لإضافة تقييم</h3>
                    <p>الصفحة جاهزة، لكن إرسال التقييم يعتمد على المستخدم المسجل حتى تظهر صورته من ملفه الشخصي.</p>
                </div>
            @endauth
        </section>

        <section class="feedback-listing">
            <div class="section-head">
                <span class="eyebrow">What Patients Say</span>
                <h2>أحدث التقييمات</h2>
            </div>

            <div class="cards-grid">
                @forelse ($feedback as $item)
                    <article class="testimonial-card">
                        <div class="card-stars" aria-label="{{ $item['rating'] }} من 5">
                            @for ($star = 1; $star <= 5; $star++)
                                <span class="{{ $star <= $item['rating'] ? 'filled' : '' }}">★</span>
                            @endfor
                        </div>

                        <p class="message">{{ $item['message'] }}</p>

                        <footer class="author-row">
                            <img
                                src="{{ $item['user_avatar'] }}"
                                alt="{{ $item['user_name'] }}"
                                class="avatar"
                            >
                            <div>
                                <strong>{{ $item['user_name'] }}</strong>
                                <span>{{ optional($item['created_at'])->format('Y-m-d') }}</span>
                            </div>
                        </footer>
                    </article>
                @empty
                    <div class="empty-state">
                        <h3>لا توجد تقييمات بعد</h3>
                        <p>ابدأ بإضافة أول تقييم ليظهر هنا بنفس تنسيق البطاقات.</p>
                    </div>
                @endforelse
            </div>

            @if ($feedback->hasPages())
                <div class="pager">
                    {{ $feedback->links() }}
                </div>
            @endif
        </section>
    </main>
</body>
</html>
