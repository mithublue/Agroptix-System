<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
    /**
     * AJAX: List users (all roles) formatted for TomSelect.
     * Optional search via ?q=
     * Response: { success: bool, data: [{ value, text }] }
     */
    public function list(Request $request)
    {
        try {
            // List all users; optionally filter by active/approved below
            $query = User::query();

            // Optional: include only active/approved users if columns exist
            if (\Schema::hasColumn('users', 'is_active')) {
                $query->where('is_active', true);
            }
            if (\Schema::hasColumn('users', 'is_approved')) {
                $query->where(function($q) {
                    $q->where('is_approved', true)
                      ->orWhereNull('is_approved');
                });
            }

            // Optional: fetch a specific user by id for preselection
            if ($request->filled('id')) {
                $id = (int) $request->query('id');
                $user = $query->where('id', $id)->first();
                if ($user) {
                    $label = $user->name ?: ("User #{$user->id}");
                    if (!empty($user->email)) {
                        $label .= " ({$user->email})";
                    }
                    return response()->json([
                        'success' => true,
                        'data' => [[
                            'value' => $user->id,
                            'text' => $label,
                        ]],
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

            // Optional search
            if ($request->filled('q')) {
                $search = $request->query('q');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $items = $query->select('id', 'name', 'email')
                ->orderBy('name')
                ->limit(50)
                ->get()
                ->map(function ($u) {
                    $label = $u->name ?: ("User #{$u->id}");
                    if (!empty($u->email)) {
                        $label .= " ({$u->email})";
                    }
                    return [
                        'value' => $u->id,
                        'text' => $label,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $items,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching producers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch producers.'
            ], 500);
        }
    }
}
