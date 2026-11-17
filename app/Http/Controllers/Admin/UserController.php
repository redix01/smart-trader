<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   public function index()
   {
       $users = User::latest()->get();
       return view('admin.user.list', compact('users'));
   }

   public function show($id)
   {
       $user = User::findOrFail($id);
       
       // Get KYC status for the user (using the same structure as KycController)
       $kycStatus = [
           'personal_info' => [
               'full_name' => $user->name ?? '',
               'email' => $user->email ?? '',
               'phone' => $user->phone ?? '',
               'date_of_birth' => $user->date_of_birth ?? '',
               'nationality' => $user->nationality ?? '',
               'status' => $user->date_of_birth && $user->nationality ? 'completed' : 'pending'
           ],
           'address_info' => [
               'street_address' => $user->street_address ?? '',
               'city' => $user->city ?? '',
               'state' => $user->state ?? '',
               'postal_code' => $user->postal_code ?? '',
               'country' => $user->country ?? '',
               'status' => $user->street_address && $user->city && $user->state && $user->postal_code ? 'completed' : 'pending'
           ],
           'id_info' => [
               'id_type' => $user->id_type ?? '',
               'id_number' => $user->id_number ?? '',
               'id_front' => $user->id_front ?? '',
               'id_back' => $user->id_back ?? '',
               'selfie' => $user->selfie ?? '',
               'status' => $user->id_type && $user->id_front && $user->id_back ? 'completed' : 'pending'
           ],
           'overall_status' => 'pending'
       ];
       
       // Determine overall status
       $completedSections = 0;
       if ($kycStatus['personal_info']['status'] === 'completed') $completedSections++;
       if ($kycStatus['address_info']['status'] === 'completed') $completedSections++;
       if ($kycStatus['id_info']['status'] === 'completed') $completedSections++;
       
       if ($completedSections === 3) {
           $kycStatus['overall_status'] = 'completed';
       } elseif ($completedSections > 0) {
           $kycStatus['overall_status'] = 'in_progress';
       }
       
       return view('admin.user.show', compact('user', 'kycStatus'));
   }

    public function updateBalance(Request $request, $id)
    {
        $request->validate([
            'wallet_type' => 'required|in:balance,trading_balance,mining_balance,referral_balance,holding_balance,staking_balance,profit',
            'amount' => 'required|numeric|min:0',
            'action_type' => 'required|in:add,remove,reset',
        ]);

        $walletType = $request->wallet_type;
        $amount = (float) $request->amount;
        $actionType = $request->action_type;

        $result = DB::transaction(function () use ($id, $walletType, $amount, $actionType) {
            $user = User::whereKey($id)->lockForUpdate()->firstOrFail();
            $current = (float) $user->$walletType;

            // Never let a removal push a wallet below zero
            if ($actionType === 'remove' && $current < $amount) {
                return ['error' => 'Insufficient balance. Cannot remove more than the current balance.'];
            }

            $label = ucwords(str_replace('_', ' ', $walletType));
            $user->$walletType = match ($actionType) {
                'add' => $current + $amount,
                'remove' => $current - $amount,
                default => $amount,
            };
            $user->save();

            $message = match ($actionType) {
                'add' => 'Successfully added '.money($amount, 2, $user).' to '.$label,
                'remove' => 'Successfully removed '.money($amount, 2, $user).' from '.$label,
                default => 'Successfully reset '.$label.' to '.money($amount, 2, $user),
            };

            \Log::info('Admin wallet adjustment', [
                'user_id' => $user->id,
                'wallet' => $walletType,
                'action' => $actionType,
                'amount' => $amount,
                'before' => $current,
                'after' => (float) $user->$walletType,
                'by_admin' => auth()->id(),
            ]);

            return ['message' => $message];
        });

        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }

        return redirect()->back()->with('success', $result['message']);
    }

   public function deleteUser($id)
   {
       $user = User::findOrFail($id);

       // Deleting an admin cascades to everything they created (signals, mail logs) and could lock everyone out
       if ($user->role === 'admin' || $user->id === auth()->id()) {
           return redirect()->back()->with('error', 'Admin accounts cannot be deleted here.');
       }

       $user->delete();
       return redirect()->route('admin.user.index')->with('success', 'User has been deleted');
   }

   public function updateStatus(Request $request, $id)
   {
       $request->validate([
           'status' => 'required|in:active,inactive',
       ]);

       $user = User::findOrFail($id);
       if ($user->id === auth()->id()) {
           return redirect()->back()->with('error', 'You cannot deactivate your own account.');
       }
       $user->status = $request->status;
       $user->save();

       $statusText = $request->status === 'active' ? 'activated' : 'deactivated';
       return redirect()->back()->with('success', "User account has been {$statusText} successfully");
   }

   public function verifyEmail($id)
   {
       $user = User::findOrFail($id);
       
       // Mark email as verified
       $user->update([
           'email_verified_at' => now(),
           'verification_code' => null,
           'verification_code_expires_at' => null,
       ]);
       
       return redirect()->back()->with('success', 'User email has been manually verified successfully.');
   }

   public function unverifyEmail($id)
   {
       $user = User::findOrFail($id);
       
       // Remove email verification
       $user->update([
           'email_verified_at' => null,
       ]);
       
       return redirect()->back()->with('success', 'User email verification has been removed.');
   }

}
