<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expert;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpertController extends Controller
{
    public function index()
    {
        $experts = Expert::withCount('subscriptions')->orderBy('name')->paginate(20);
        return Inertia::render('Admin/Experts/Index', ['experts' => $experts]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|string|max:500',
            'bio' => 'nullable|string',
            'win_rate' => 'nullable|numeric|min:0|max:100',
            'profit_share' => 'nullable|numeric|min:0|max:100',
            'total_volume' => 'nullable|numeric|min:0',
            'status' => 'in:verified,pro,institutional,top-tier',
            'is_active' => 'boolean',
        ]);

        Expert::create($data);
        return redirect()->route('admin.experts.index')->with('success', 'Expert created.');
    }

    public function update(Request $request, Expert $expert)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'avatar' => 'nullable|string|max:500',
            'bio' => 'nullable|string',
            'win_rate' => 'nullable|numeric|min:0|max:100',
            'profit_share' => 'nullable|numeric|min:0|max:100',
            'total_volume' => 'nullable|numeric|min:0',
            'status' => 'in:verified,pro,institutional,top-tier',
            'is_active' => 'boolean',
        ]);

        $expert->update($data);
        return redirect()->back()->with('success', 'Expert updated.');
    }

    public function destroy(Expert $expert)
    {
        $expert->delete();
        return redirect()->route('admin.experts.index')->with('success', 'Expert deleted.');
    }
}
