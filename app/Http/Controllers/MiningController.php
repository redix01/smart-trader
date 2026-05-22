<?php

namespace App\Http\Controllers;

use App\Services\MiningService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MiningController extends Controller
{
    public function __construct(private MiningService $mining) {}

    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Mining', [
            'plans' => $this->mining->getActivePlans(),
            'subscriptions' => $this->mining->getUserSubscriptions($user),
            'kycRequired' => $user->kyc_level === 'unverified',
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->kyc_level === 'unverified') {
            return redirect()->route('mining')
                ->with('error', 'KYC verification is required before subscribing to a mining plan.');
        }

        $data = $request->validate([
            'plan_id' => 'required|exists:mining_plans,id',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $this->mining->createSubscription($user, (int) $data['plan_id'], (float) $data['amount']);
        } catch (ValidationException $e) {
            return redirect()->route('mining')
                ->withErrors($e->errors())
                ->withInput();
        }

        return redirect()->route('mining')->with('success', 'Mining subscription created.');
    }
}
