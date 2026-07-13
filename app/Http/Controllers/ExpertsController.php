<?php

namespace App\Http\Controllers;

use App\Services\ExpertService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpertsController extends Controller
{
    public function __construct(private ExpertService $expert) {}

    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('CopyExperts', [
            'experts' => $this->expert->getActiveExperts(),
            'subscriptions' => $this->expert->getUserSubscriptions($user),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expert_id' => 'required|exists:experts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $this->expert->subscribe($request->user(), (int) $data['expert_id'], (float) $data['amount']);

        return redirect()->route('experts')->with('success', 'Subscribed to expert strategy.');
    }

    public function destroy(Request $request, int $subscription)
    {
        $this->expert->cancel($request->user(), $subscription);

        return redirect()->route('experts')->with('success', 'Expert subscription cancelled. No profits were transferred.');
    }
}
