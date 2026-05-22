<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->side, fn($q, $v) => $q->where('side', $v))
            ->when($request->pair, fn($q, $v) => $q->where('pair', 'like', "%{$v}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['status', 'side', 'pair']),
        ]);
    }

    public function show(Order $order)
    {
        $order->load('user');
        return Inertia::render('Admin/Orders/Show', ['order' => $order]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'in:open,filled,cancelled,rejected',
        ]);

        if ($data['status'] === 'cancelled' && $order->status === 'open') {
            $order->update(['status' => 'cancelled', 'closed_at' => now()]);
        } elseif ($data['status'] === 'filled') {
            $order->update(['status' => 'filled', 'filled' => $order->amount, 'closed_at' => now()]);
        }

        return redirect()->back()->with('success', 'Order updated.');
    }
}
