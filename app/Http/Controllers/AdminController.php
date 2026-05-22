<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(private AdminService $admin) {}

    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'pendingKyc' => $this->admin->getPendingKyc(),
            'pendingDeposits' => $this->admin->getPendingDeposits(),
            'pendingWithdrawals' => $this->admin->getPendingWithdrawals(),
        ]);
    }

    public function approveKyc(Request $request, int $id)
    {
        $this->admin->approveKyc($id, $request->user()->id);
        return redirect()->back()->with('success', 'KYC approved.');
    }

    public function rejectKyc(Request $request, int $id)
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);
        $this->admin->rejectKyc($id, $request->user()->id, $data['reason']);
        return redirect()->back()->with('success', 'KYC rejected.');
    }

    public function approveDeposit(Request $request, int $id)
    {
        $this->admin->approveDeposit($id, $request->user()->id);
        return redirect()->back()->with('success', 'Deposit approved.');
    }

    public function rejectDeposit(Request $request, int $id)
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);
        $this->admin->rejectDeposit($id, $request->user()->id, $data['reason']);
        return redirect()->back()->with('success', 'Deposit rejected.');
    }

    public function approveWithdrawal(Request $request, int $id)
    {
        $this->admin->approveWithdrawal($id, $request->user()->id);
        return redirect()->back()->with('success', 'Withdrawal approved.');
    }

    public function rejectWithdrawal(Request $request, int $id)
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);
        $this->admin->rejectWithdrawal($id, $request->user()->id, $data['reason']);
        return redirect()->back()->with('success', 'Withdrawal rejected.');
    }
}
