<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->search, fn($q, $v) => $q->where('name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%"))
            ->when($request->tier, fn($q, $v) => $q->where('account_tier', $v))
            ->when($request->kyc, fn($q, $v) => $q->where('kyc_level', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'tier', 'kyc']),
        ]);
    }

    public function show(User $user)
    {
        $user->load(['wallets', 'kycSubmissions', 'deposits' => fn($q) => $q->latest()->limit(10), 'withdrawals' => fn($q) => $q->latest()->limit(10)]);
        return Inertia::render('Admin/Users/Show', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,'.$user->id,
            'account_tier' => 'sometimes|in:user,admin',
            'kyc_level' => 'sometimes|in:unverified,pending,verified,rejected',
        ]);

        $user->update($data);
        return redirect()->back()->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
