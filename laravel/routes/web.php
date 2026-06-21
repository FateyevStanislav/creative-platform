<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/categories/{slug}', [PostController::class, 'byCategory'])->name('categories.show');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/auth/github/redirect', [AuthController::class, 'githubRedirect'])->name('github.redirect');
    Route::get('/auth/github/callback', [AuthController::class, 'githubCallback'])->name('github.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/feed/subscriptions', [PostController::class, 'subscriptionFeed'])->name('feed.subscriptions');

    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{id}/replies', [CommentController::class, 'reply'])->name('comments.reply');
    Route::get('/comments/{id}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/posts/{id}/reactions', [ReactionController::class, 'store'])->name('reactions.store');
    Route::delete('/posts/{id}/reactions', [ReactionController::class, 'destroy'])->name('reactions.destroy');

    Route::post('/publishers/{id}/subscribe', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::delete('/publishers/{id}/subscribe', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    Route::get('/publishers/{id}', [PostController::class, 'publisherPage'])->name('publishers.show');

    Route::prefix('admin')->name('admin.')->middleware('can:admin')->group(function () {
        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{id}', [Admin\ReportController::class, 'show'])->name('reports.show');
        Route::patch('/reports/{id}', [Admin\ReportController::class, 'update'])->name('reports.update');
        Route::delete('/posts/{id}', [Admin\PostController::class, 'destroy'])->name('posts.destroy');
        Route::delete('/comments/{id}', [Admin\PostController::class, 'destroyComment'])->name('comments.destroy');
        Route::patch('/users/{id}/block', [Admin\UserController::class, 'block'])->name('users.block');
        Route::patch('/users/{id}/unblock', [Admin\UserController::class, 'unblock'])->name('users.unblock');
    });
});