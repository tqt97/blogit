<?php

use Modules\Roles\Http\Controllers\Admin\RoleController;

// use Modules\Roles\Http\Controllers\RolesController;

Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
// Route::get('/roles/create', [RolesController::class, 'create'])->name('roles.create');
// Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');
// Route::get('/roles/{role}', [RolesController::class, 'show'])->name('roles.show');
// Route::get('/roles/{role}/edit', [RolesController::class, 'edit'])->name('roles.edit');
// Route::put('/roles/{role}', [RolesController::class, 'update'])->name('roles.update');
// Route::delete('/roles/{role}', [RolesController::class, 'destroy'])->name('roles.destroy');
