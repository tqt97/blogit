<?php

use Illuminate\Support\Facades\Route;
use Modules\Post\Presentation\Controllers\Admin\PostController;

Route::middleware(['web', 'auth'])->prefix('admin')->group(function () {
    Route::post('posts/restore', [PostController::class, 'bulkRestore'])->name('posts.bulk-restore');
    Route::delete('posts/force', [PostController::class, 'bulkForceDestroy'])->name('posts.bulk-force-destroy');
    Route::post('posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('posts/{post}/force', [PostController::class, 'forceDestroy'])->name('posts.force-destroy');
    Route::resource('posts', PostController::class)->except(['show']);
    Route::delete('posts', [PostController::class, 'bulkDestroy'])->name('posts.bulk-destroy');
});
