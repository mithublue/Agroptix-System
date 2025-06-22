<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Gate::authorize('viewAny', SpatiePermission::class);
        $permissions = SpatiePermission::latest()->paginate(15);
        return view('admin.permissions.index', compact('permissions'));
    }
}
