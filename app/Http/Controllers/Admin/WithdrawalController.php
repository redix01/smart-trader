<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WithdrawalController extends Controller
{
    public function __construct(private AdminService $admin) {}

    public function index(Request $request)
    {
        $withdrawals = Withdrawal::with('user')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Withdrawals/Index', [
            'withdrawals' => $withdrawals,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load('user');
        return Inertia::render('Admin/Withdrawals/Show', ['withdrawal' => $withdrawal]);
    }

    public function approve(Request $request, int $id)
    {
        $this->admin->approveWithdrawal($id, $request->user()->id);
        return redirect()->back()->with('success', 'Withdrawal approved.');
    }

    public function reject(Request $request, int $id)
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);
        $this->admin->rejectWithdrawal($id, $request->user()->id, $data['reason']);
        return redirect()->back()->with('success', 'Withdrawal rejected.');
    }
}
