<?php

use Illuminate\Support\Facades\Route;
use Modules\Post\Presentation\Controllers\Admin\PostController;

Route::middleware(['web', 'auth'])->prefix('admin')->group(function () {
    Route::resource('posts', PostController::class)->except(['show']);
    Route::delete('posts', [PostController::class, 'bulkDestroy'])->name('posts.bulk-destroy');
});
