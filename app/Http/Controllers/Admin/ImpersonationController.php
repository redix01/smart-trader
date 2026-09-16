<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImpersonationLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonationController extends Controller
{
    public function start(Request $request, $id)
    {
        $admin = Auth::user();
        $target = User::findOrFail($id);

        abort_if($target->id === $admin->id, 403, "You can't log in as your own account.");
        abort_if($target->IsAdmin(), 403, 'Admin accounts cannot be impersonated.');
        abort_if($request->session()->has('impersonator_id'), 403, 'You are already viewing an account as another user.');

        $log = ImpersonationLog::create([
            'admin_id' => $admin->id,
            'user_id' => $target->id,
            'ip_address' => $request->ip(),
            'started_at' => now(),
        ]);

        Log::info('Admin started impersonating user', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'user_id' => $target->id,
            'user_email' => $target->email,
            'ip' => $request->ip(),
        ]);

        $request->session()->regenerate();
        $request->session()->put('impersonator_id', $admin->id);
        $request->session()->put('impersonation_log_id', $log->id);

        Auth::login($target);

        return redirect()->route('user.dashboard');
    }

    public function stop(Request $request)
    {
        $adminId = $request->session()->get('impersonator_id');

        abort_unless($adminId, 403, 'You are not currently viewing an account as another user.');

        $admin = User::find($adminId);
        $impersonatedUser = Auth::user();

        $logId = $request->session()->get('impersonation_log_id');
        if ($logId) {
            ImpersonationLog::where('id', $logId)->update(['ended_at' => now()]);
        }

        Log::info('Admin stopped impersonating user', [
            'admin_id' => $adminId,
            'user_id' => $impersonatedUser?->id,
        ]);

        $request->session()->forget(['impersonator_id', 'impersonation_log_id']);
        $request->session()->regenerate();

        if (!$admin) {
            Auth::logout();
            return redirect()->route('login');
        }

        Auth::login($admin);

        return redirect()->route('admin.user.show', $impersonatedUser->id);
    }
}
