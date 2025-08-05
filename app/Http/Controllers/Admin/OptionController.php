<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage_options']);
    }

    public function index()
    {
        $options = Option::orderBy('option_name')->paginate(20);
        return view('admin.options.index', compact('options'));
    }

    public function edit($id)
    {
        $option = Option::findOrFail($id);
        return view('admin.options.edit', compact('option'));
    }

    public function update(Request $request, $id)
    {
        $option = Option::findOrFail($id);
        $request->validate([
            'option_value' => 'nullable|string',
        ]);
        $option->option_value = $request->option_value;
        $option->save();
        return redirect()->route('admin.options.index')->with('success', 'Option updated.');
    }

    public function saveUserOptions(Request $request)
    {
        $validated = $request->validate([
            'users_need_activation' => 'required|in:yes,no',
            'users_activation_method' => 'nullable|in:email,phone',
            'users_need_admin_approval' => 'required|in:yes,no',
        ]);

        Option::set('users_need_activation', $validated['users_need_activation']);
        Option::set('users_activation_method', $validated['users_need_activation'] === 'yes' ? ($validated['users_activation_method'] ?? 'email') : '');
        Option::set('users_need_admin_approval', $validated['users_need_admin_approval']);

        return redirect()->route('admin.options.index')->with('success', 'User options updated.');
    }
}
