<?php
use Illuminate\Support\Facades\Route;
use Modules\Tag\Presentation\Controllers\Admin\TagController;

Route::middleware(['web', 'auth'])
    // prefix('admin')
    //     ->as('admin.')
    ->group(function () {
        Route::resource('tags', TagController::class)
            ->parameters(['tags' => 'tag']);
        Route::delete('tags', [TagController::class, 'bulkDestroy'])->name('tags.bulk-destroy');
    });
