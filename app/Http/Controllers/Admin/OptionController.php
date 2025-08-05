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
}
