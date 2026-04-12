<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_testimonials_page(): void
    {
        $response = $this->get('/testimonials');

        $response->assertOk()
            ->assertSee('آراء عملائنا وتجاربهم معنا')
            ->assertSee('تسجيل الدخول مطلوب لإضافة تقييم');
    }

    public function test_authenticated_user_can_submit_feedback_from_testimonials_page(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
            'avatar' => 'avatars/client.png',
        ]);

        $response = $this->actingAs($user)->post('/testimonials', [
            'message' => 'تجربة ممتازة مع الاستقبال والتنظيم.',
            'rating' => 5,
        ]);

        $response->assertRedirect('/testimonials');

        $this->assertDatabaseHas('feedback', [
            'user_id' => $user->id,
            'rating' => 5,
            'message' => 'تجربة ممتازة مع الاستقبال والتنظيم.',
        ]);

        $page = $this->get('/testimonials');

        $page->assertOk()
            ->assertSee('تجربة ممتازة مع الاستقبال والتنظيم.')
            ->assertSee($user->name);
    }
}
