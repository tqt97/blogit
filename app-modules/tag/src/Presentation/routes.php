<?php

use Illuminate\Support\Facades\Route;
use Modules\Tag\Presentation\Controllers\Admin\TagController;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('tags', TagController::class)
            ->except(['show']); // hoặc giữ show nếu bạn có view
    });
