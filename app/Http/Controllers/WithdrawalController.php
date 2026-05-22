<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalRequest;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WithdrawalController extends Controller
{
    public function __construct(private WithdrawalService $withdrawal) {}

    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Withdraw', [
            'balance' => $this->withdrawal->getUserBalance($user),
            'history' => $this->withdrawal->getUserWithdrawals($user),
        ]);
    }

    public function store(WithdrawalRequest $request)
    {
        $this->withdrawal->createWithdrawal($request->user(), $request->validated());

        return redirect()->route('withdraw')->with('success', 'Withdrawal request submitted.');
    }
}
