<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyProject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyProjectController extends Controller
{
    public function index()
    {
        $projects = PropertyProject::withCount('investments')->orderBy('created_at', 'desc')->paginate(20);
        return Inertia::render('Admin/PropertyProjects/Index', ['projects' => $projects]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'strategy' => 'nullable|string|max:64',
            'min_investment' => 'required|numeric|min:0',
            'target_roi' => 'nullable|numeric|min:0|max:100',
            'status' => 'in:open,draft,active,completed,archived',
            'image' => 'nullable|string|max:500',
            'media' => 'nullable|json',
            'disclosure' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        PropertyProject::create($data);
        return redirect()->route('admin.property-projects.index')->with('success', 'Project created.');
    }

    public function update(Request $request, PropertyProject $propertyProject)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'region' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'strategy' => 'nullable|string|max:64',
            'min_investment' => 'sometimes|numeric|min:0',
            'target_roi' => 'nullable|numeric|min:0|max:100',
            'status' => 'in:draft,active,completed,archived',
            'image' => 'nullable|string|max:500',
            'media' => 'nullable|json',
            'disclosure' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $propertyProject->update($data);
        return redirect()->back()->with('success', 'Project updated.');
    }

    public function destroy(PropertyProject $propertyProject)
    {
        $propertyProject->delete();
        return redirect()->route('admin.property-projects.index')->with('success', 'Project deleted.');
    }
}
