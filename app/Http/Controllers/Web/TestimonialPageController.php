<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Support\PublicAssetUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TestimonialPageController extends Controller
{
    public function index(): View
    {
        $feedback = Feedback::query()
            ->with('user')
            ->latest()
            ->paginate(9);

        return view('testimonials.index', [
            'feedback' => $feedback->through(fn (Feedback $item) => [
                'id' => $item->id,
                'message' => $item->message,
                'rating' => $item->rating,
                'created_at' => $item->created_at,
                'user_name' => $item->user?->name ?? 'عميلنا',
                'user_avatar' => PublicAssetUrl::from($item->user?->avatar),
            ]),
        ]);
    }

    public function store(StoreFeedbackRequest $request): RedirectResponse
    {
        $request->user()->feedback()->create([
            'message' => $request->string('message')->toString(),
            'rating' => $request->integer('rating'),
        ]);

        return redirect()
            ->route('testimonials.index')
            ->with('status', 'تم إضافة التقييم بنجاح.');
    }
}
