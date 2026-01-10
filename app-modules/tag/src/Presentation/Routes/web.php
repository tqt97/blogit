<?php

use Illuminate\Support\Facades\Route;
use Modules\Tag\Presentation\Controllers\Admin\TagController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::resource('tags', TagController::class)->except(['show']);
    Route::delete('tags', [TagController::class, 'bulkDestroy'])->name('tags.bulk-destroy');
});
