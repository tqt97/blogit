<?php

namespace Modules\Roles\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController
{
    public function index(Request $request): Response
    {
        return Inertia::render('admin/roles/index', []);
    }
}
