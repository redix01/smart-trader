<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepositController extends Controller
{
    public function __construct(private AdminService $admin) {}

    public function index(Request $request)
    {
        $deposits = Deposit::with('user')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->method_id, fn($q, $v) => $q->where('deposit_method_id', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Deposits/Index', [
            'deposits' => $deposits,
            'filters' => $request->only(['status', 'method_id']),
        ]);
    }

    public function show(Deposit $deposit)
    {
        $deposit->load('user');
        return Inertia::render('Admin/Deposits/Show', ['deposit' => $deposit]);
    }

    public function approve(Request $request, int $id)
    {
        $this->admin->approveDeposit($id, $request->user()->id);
        return redirect()->back()->with('success', 'Deposit approved.');
    }

    public function reject(Request $request, int $id)
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);
        $this->admin->rejectDeposit($id, $request->user()->id, $data['reason']);
        return redirect()->back()->with('success', 'Deposit rejected.');
    }
}
