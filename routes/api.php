<?php

use App\Http\Controllers\Api\Admin\AboutUsController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ChefController;
use App\Http\Controllers\Api\Admin\MenuItemController;
use App\Http\Controllers\Api\Admin\ReservationController;
use App\Http\Controllers\Api\Admin\SiteSettingController;
use App\Http\Controllers\Api\Admin\TestimonialController;
use App\Http\Controllers\Api\Admin\BlogCategoryController;
use App\Http\Controllers\Api\Admin\TagController;
use App\Http\Controllers\Api\Admin\PostController;
use App\Http\Controllers\Api\Admin\CommentController;
use App\Http\Controllers\Api\Admin\ContactController;
use App\Http\Controllers\Api\Admin\FeatureController;
use App\Http\Controllers\Api\Guest\GuestController;
use App\Http\Controllers\Api\Guest\GuestActionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);

        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::get('/reservations/{id}', [ReservationController::class, 'show']);
        Route::put('/reservations/{id}/status', [ReservationController::class, 'updateStatus']);
        Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

        Route::apiResource('categories', CategoryController::class);
        Route::patch('/categories/{id}/toggle', [CategoryController::class, 'toggleStatus']);

        Route::apiResource('menu-items', MenuItemController::class);
        Route::patch('/menu-items/{id}/toggle', [MenuItemController::class, 'toggleStatus']);

        Route::apiResource('chefs', ChefController::class);
        
        Route::get('/testimonials', [TestimonialController::class, 'index']);
        Route::patch('/testimonials/{id}/toggle', [TestimonialController::class, 'toggleStatus']);
        Route::delete('/testimonials/{id}', [TestimonialController::class, 'destroy']);

        Route::get('/site-settings', [SiteSettingController::class, 'index']);
        Route::post('/site-settings', [SiteSettingController::class, 'update']);

        Route::apiResource('blog-categories', BlogCategoryController::class);
        Route::patch('/blog-categories/{id}/toggle', [BlogCategoryController::class, 'toggleStatus']);

        Route::apiResource('tags', TagController::class);
        Route::patch('/tags/{id}/toggle', [TagController::class, 'toggleStatus']);

        Route::apiResource('posts', PostController::class);
        Route::patch('/posts/{id}/toggle', [PostController::class, 'toggleStatus']);

        Route::get('/comments', [CommentController::class, 'index']);
        Route::post('/comments', [CommentController::class, 'store']);
        Route::get('/comments/{id}', [CommentController::class, 'show']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

        Route::get('/about-us', [AboutUsController::class, 'show']);
        Route::post('/about-us', [AboutUsController::class, 'update']);

        Route::apiResource('features', FeatureController::class)->except(['show']);

        Route::get('/contacts', [ContactController::class, 'index']);
        Route::get('/contacts/{id}', [ContactController::class, 'show']);
        Route::patch('/contacts/{id}/toggle', [ContactController::class, 'toggleStatus']);
        Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);
    });
    
});


// --- Guest Routes ---
Route::prefix('guest')->group(function () {
    // Get Data
    Route::get('/menu', [GuestController::class, 'getMenu']);
    Route::get('/menu/categories/{id}', [GuestController::class, 'getCategoryMenu']);
    Route::get('/chefs', [GuestController::class, 'getChefs']);
    Route::get('/testimonials', [GuestController::class, 'getTestimonials']);
    Route::get('/about-us', [GuestController::class, 'getAboutUs']);
    Route::get('/site-settings', [GuestController::class, 'getSiteSettings']);
    Route::get('/blog/posts', [GuestController::class, 'getBlogPosts']);
    Route::get('/blog/posts/{slug}', [GuestController::class, 'getSinglePost']);
    Route::get('/blog/categories', [GuestController::class, 'getBlogCategories']);

    // Actions
    Route::post('/reservations', [GuestActionController::class, 'storeReservation']);
    Route::post('/contacts', [GuestActionController::class, 'storeContact']);
    Route::post('/blog/posts/{postId}/comments', [GuestActionController::class, 'storeComment']);
});