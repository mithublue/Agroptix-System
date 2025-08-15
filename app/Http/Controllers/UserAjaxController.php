<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserAjaxController extends Controller
{
    public function list(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $users = User::withoutGlobalScopes()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                });
            })
            ->orderBy('name')
            ->limit(50)
            ->get(['id','name','email']);

        return response()->json([
            'data' => $users->map(fn($u) => [
                'value' => $u->id,
                'text'  => $u->name . ' <' . $u->email . '>',
            ]),
        ]);
    }
}
