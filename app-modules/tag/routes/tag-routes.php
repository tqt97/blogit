<?php

use Illuminate\Support\Facades\Route;
use Modules\Tag\Presentation\Controllers\Admin\TagController;

// Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
// Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create');
// Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
// Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tags.show');
// Route::get('/tags/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit');
// Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
// Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

Route::middleware(['web', 'auth'])
    // prefix('admin')
    //     ->as('admin.')
    ->group(function () {
        Route::resource('tags', TagController::class)
            ->parameters(['tags' => 'tag']);
        Route::delete('tags', [TagController::class, 'bulkDestroy'])->name('tags.bulk-destroy');
    });
