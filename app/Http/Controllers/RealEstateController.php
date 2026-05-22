<?php

namespace App\Http\Controllers;

use App\Services\RealEstateService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RealEstateController extends Controller
{
    public function __construct(private RealEstateService $realEstate) {}

    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('RealEstate', [
            'projects' => $this->realEstate->getActiveProjects(),
            'investments' => $this->realEstate->getUserInvestments($user),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:property_projects,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $this->realEstate->invest($request->user(), (int) $data['project_id'], (float) $data['amount']);

        return redirect()->route('realestate')->with('success', 'Investment submitted.');
    }
}
