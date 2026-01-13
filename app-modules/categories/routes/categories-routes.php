<?php

use Illuminate\Support\Facades\Route;
use Modules\Categories\Presentation\Http\Controllers\CategoryController;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::prefix('categories')->group(function () {
        // Add your categories routes here
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index')->can('view_categories');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store')->can('create_categories');
        Route::get('create', [CategoryController::class, 'create'])->name('categories.create')->can('create_categories');
        Route::get('{category}', [CategoryController::class, 'show'])->name('categories.show')->can('view_categories');
        Route::get('{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->can('edit_categories');
        Route::put('{category}', [CategoryController::class, 'update'])->name('categories.update')->can('edit_categories');
        Route::delete('{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->can('delete_categories');
    });
});
