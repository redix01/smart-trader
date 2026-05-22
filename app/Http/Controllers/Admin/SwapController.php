<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SwapQuote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SwapController extends Controller
{
    public function index(Request $request)
    {
        $swaps = SwapQuote::with('user')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->from, fn($q, $v) => $q->where('from_currency', $v))
            ->when($request->to, fn($q, $v) => $q->where('to_currency', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Swaps/Index', [
            'swaps' => $swaps,
            'filters' => $request->only(['status', 'from', 'to']),
        ]);
    }

    public function show(SwapQuote $swap)
    {
        $swap->load('user');
        return Inertia::render('Admin/Swaps/Show', ['swap' => $swap]);
    }
}
