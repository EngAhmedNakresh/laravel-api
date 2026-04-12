<?php

use App\Http\Controllers\Web\TestimonialPageController;
use Illuminate\Support\Facades\Route;

Route::get('/testimonials', [TestimonialPageController::class, 'index'])->name('testimonials.index');
Route::post('/testimonials', [TestimonialPageController::class, 'store'])
    ->middleware('auth:sanctum')
    ->name('testimonials.store');
