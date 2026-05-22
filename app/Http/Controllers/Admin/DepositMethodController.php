<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepositMethodController extends Controller
{
    public function index()
    {
        $methods = DepositMethod::orderBy('sort_order')->paginate(20);
        return Inertia::render('Admin/DepositMethods/Index', ['methods' => $methods]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'currency' => 'required|string|max:10',
            'network' => 'required|string|max:32',
            'label' => 'required|string|max:255',
            'wallet_address' => 'required|string|max:500',
            'icon' => 'nullable|string|max:500',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'fee_fixed' => 'numeric|min:0',
            'fee_percent' => 'numeric|min:0|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        DepositMethod::create($data);
        return redirect()->route('admin.deposit-methods.index')->with('success', 'Deposit method created.');
    }

    public function update(Request $request, DepositMethod $depositMethod)
    {
        $data = $request->validate([
            'currency' => 'sometimes|string|max:10',
            'network' => 'sometimes|string|max:32',
            'label' => 'sometimes|string|max:255',
            'wallet_address' => 'sometimes|string|max:500',
            'icon' => 'nullable|string|max:500',
            'min_amount' => 'sometimes|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'fee_fixed' => 'sometimes|numeric|min:0',
            'fee_percent' => 'sometimes|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $depositMethod->update($data);
        return redirect()->back()->with('success', 'Deposit method updated.');
    }

    public function destroy(DepositMethod $depositMethod)
    {
        $depositMethod->delete();
        return redirect()->route('admin.deposit-methods.index')->with('success', 'Deposit method deleted.');
    }
}
