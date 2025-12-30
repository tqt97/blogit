<?php

// use Modules\AccessControls\Http\Controllers\AccessControlsController;

// Route::get('/access-controls', [AccessControlsController::class, 'index'])->name('access-controls.index');
// Route::get('/access-controls/create', [AccessControlsController::class, 'create'])->name('access-controls.create');
// Route::post('/access-controls', [AccessControlsController::class, 'store'])->name('access-controls.store');
// Route::get('/access-controls/{access-control}', [AccessControlsController::class, 'show'])->name('access-controls.show');
// Route::get('/access-controls/{access-control}/edit', [AccessControlsController::class, 'edit'])->name('access-controls.edit');
// Route::put('/access-controls/{access-control}', [AccessControlsController::class, 'update'])->name('access-controls.update');
// Route::delete('/access-controls/{access-control}', [AccessControlsController::class, 'destroy'])->name('access-controls.destroy');

use Illuminate\Support\Facades\Route;
use Modules\AccessControls\Http\Controllers\PermissionController;
use Modules\AccessControls\Http\Controllers\RoleController;

Route::middleware(['web', 'auth', 'verified'])->group(function () {

    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permissions.index')->can('view_permissions');
        Route::post('/', [PermissionController::class, 'store'])->name('permissions.store')->can('create_permissions');
        Route::put('{permission}', [PermissionController::class, 'update'])->name('permissions.update')->can('edit_permissions');
        Route::delete('{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->can('delete_permissions');
    });
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index')->can('view_roles');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store')->can('create_roles');
        Route::get('create', [RoleController::class, 'create'])->name('roles.create')->can('create_roles');
        Route::get('{role}', [RoleController::class, 'show'])->name('roles.show')->can('view_roles');
        Route::get('{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->can('edit_roles');
        Route::put('{role}', [RoleController::class, 'update'])->name('roles.update')->can('edit_roles');
        Route::delete('{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->can('delete_roles');
    });
});
