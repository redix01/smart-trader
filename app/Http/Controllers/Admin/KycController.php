<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycSubmission;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KycController extends Controller
{
    public function __construct(private AdminService $admin) {}

    public function index(Request $request)
    {
        $submissions = KycSubmission::with('user')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Kyc/Index', [
            'submissions' => $submissions,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(KycSubmission $kyc)
    {
        $kyc->load('user');
        return Inertia::render('Admin/Kyc/Show', ['submission' => $kyc]);
    }

    public function approve(Request $request, int $id)
    {
        $this->admin->approveKyc($id, $request->user()->id);
        return redirect()->back()->with('success', 'KYC approved.');
    }

    public function reject(Request $request, int $id)
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);
        $this->admin->rejectKyc($id, $request->user()->id, $data['reason']);
        return redirect()->back()->with('success', 'KYC rejected.');
    }
}
