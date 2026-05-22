<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Services\DepositService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepositController extends Controller
{
    public function __construct(private DepositService $deposit) {}

    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Deposit', [
            'methods' => $this->deposit->getActiveMethods(),
            'history' => $this->deposit->getUserDeposits($user),
        ]);
    }

    public function store(DepositRequest $request)
    {
        $deposit = $this->deposit->createDeposit(
            $request->user(),
            $request->validated(),
            $request->file('proof')
        );

        return redirect()->route('deposit')->with('success', 'Deposit request submitted.');
    }
}
