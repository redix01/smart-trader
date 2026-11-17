@extends('dashboard.layout.app')

@section('content')
@php
    $idSubmitted = $user->id_type && $user->id_front && $user->id_back;
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Account</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Profile</h1>
            <p class="text-sm text-gray-400 mt-0.5">Update your details, change your password and manage verification.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 items-start">
        <!-- Profile Overview -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <div class="flex lg:flex-col items-center gap-4 lg:text-center">
                <button type="button" class="relative inline-block shrink-0 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Change profile photo">
                    <img class="w-20 h-20 lg:w-24 lg:h-24 rounded-full object-cover border border-gray-700"
                         src="{{ $user->avatar_url }}"
                         alt="Profile photo">
                    <span class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full grid place-items-center border-2 border-gray-800">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </span>
                </button>
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold text-white truncate">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-400 truncate">{{ $user->email }}</p>
                    <span class="mt-2 inline-block px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-500/15 text-blue-300">
                        {{ $user->package->name ?? 'Free' }} member
                    </span>
                </div>
            </div>

            <dl class="mt-5 pt-5 border-t border-gray-700 space-y-3 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-400">Member since</dt>
                    <dd class="text-white">{{ $user->created_at->format('M Y') }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-400">Last login</dt>
                    <dd class="text-white">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y') : 'N/A' }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-400">Status</dt>
                    <dd><span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-500/15 text-emerald-400">Active</span></dd>
                </div>
            </dl>
        </div>

        <!-- Settings -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            <!-- Personal Information -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-white">Personal information</h3>
                <p class="text-xs text-gray-400 mt-0.5 mb-4">How we contact you about your account.</p>

                @if(session()->has('success'))
                    <div class="mb-4 p-3 rounded-lg border border-emerald-500/40 bg-emerald-500/10 text-sm text-emerald-300">
                        {{ session()->get('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-lg border border-red-500/40 bg-red-500/10 text-sm text-red-300">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.updateProfile', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="profileName" class="block text-sm font-medium text-gray-300 mb-1.5">Full name</label>
                            <input id="profileName" type="text" name="name" value="{{ $user->name }}" autocomplete="name"
                                   class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter your full name">
                        </div>

                        <div>
                            <label for="profileEmail" class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                            <input id="profileEmail" type="email" value="{{ $user->email }}"
                                   class="w-full px-3 py-2.5 rounded-lg bg-gray-900/30 border border-gray-700 text-gray-400 focus:outline-none"
                                   readonly>
                            <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                        </div>

                        <div>
                            <label for="profilePhone" class="block text-sm font-medium text-gray-300 mb-1.5">Phone number</label>
                            <input id="profilePhone" type="tel" name="phone" value="{{ $user->phone }}" autocomplete="tel"
                                   class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter phone number">
                        </div>

                        <div>
                            <label for="profileTelegram" class="block text-sm font-medium text-gray-300 mb-1.5">Telegram username</label>
                            <input id="profileTelegram" type="text" name="telegram" value="{{ $user->telegram }}"
                                   class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="@username">
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Settings -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-white">Password</h3>
                <p class="text-xs text-gray-400 mt-0.5 mb-4">Choose a strong password you don&#39;t use elsewhere.</p>

                @if(session()->has('status'))
                    <div class="mb-4 p-3 rounded-lg border border-emerald-500/40 bg-emerald-500/10 text-sm text-emerald-300">
                        {{ session()->get('status') }}
                    </div>
                @endif

                <form action="{{ route('user.updatePassword') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="currentPassword" class="block text-sm font-medium text-gray-300 mb-1.5">Current password</label>
                            <input id="currentPassword" type="password" name="current_password" autocomplete="current-password"
                                   class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter current password">
                        </div>

                        <div>
                            <label for="newPassword" class="block text-sm font-medium text-gray-300 mb-1.5">New password</label>
                            <input id="newPassword" type="password" name="new_password" autocomplete="new-password"
                                   class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter new password">
                        </div>

                        <div>
                            <label for="newPasswordConfirmation" class="block text-sm font-medium text-gray-300 mb-1.5">Confirm new password</label>
                            <input id="newPasswordConfirmation" type="password" name="new_password_confirmation" autocomplete="new-password"
                                   class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Confirm new password">
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                            Change password
                        </button>
                    </div>
                </form>
            </div>

            <!-- KYC Verification -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-white">Identity verification</h3>
                <p class="text-xs text-gray-400 mt-0.5 mb-4">Verify your identity to unlock all platform features.</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-gray-700 bg-gray-900/50">
                        <span class="text-sm font-semibold text-white">ID documents</span>
                        @if($idSubmitted)
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-500/15 text-emerald-400">Submitted</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-500/15 text-amber-300">Not submitted</span>
                        @endif
                    </div>
                    <a href="{{ route('user.kyc.index') }}" class="flex items-center justify-between gap-3 p-3 min-h-[56px] rounded-xl border border-gray-700 bg-gray-900/50 hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 shrink-0 rounded-lg bg-gray-700 grid place-items-center text-gold-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 12h6M9 16h6M7 3h7l5 5v11a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white">KYC details</div>
                                    <div class="text-xs text-gray-400 truncate">View or complete your verification</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                </div>
            </div>

            <!-- Account Actions -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-white">Shortcuts</h3>
                <p class="text-xs text-gray-400 mt-0.5 mb-4">Jump to other account pages.</p>
                <div class="space-y-3">
                    <a href="{{ route('user.referrals') }}" class="flex items-center justify-between gap-3 p-3 min-h-[56px] rounded-xl border border-gray-700 bg-gray-900/50 hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 shrink-0 rounded-lg bg-gray-700 grid place-items-center text-gold-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0M16 4.5a3.5 3.5 0 010 7M22 20a7 7 0 00-4-6.3"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white">Referrals</div>
                                    <div class="text-xs text-gray-400 truncate">Invite friends and track rewards</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    <a href="{{ route('user.transactions.index') }}" class="flex items-center justify-between gap-3 p-3 min-h-[56px] rounded-xl border border-gray-700 bg-gray-900/50 hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 shrink-0 rounded-lg bg-gray-700 grid place-items-center text-gold-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white">Transaction history</div>
                                    <div class="text-xs text-gray-400 truncate">Deposits, withdrawals and trades</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                </div>
            </div>

            <!-- Notifications -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 md:col-span-2">
                <h3 class="text-base font-semibold text-white">Notifications</h3>
                <p class="text-xs text-gray-400 mt-0.5 mb-4">Choose how we keep you updated.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-white">Email notifications</p>
                                <p class="text-xs text-gray-400">Receive updates via email</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0 min-h-[40px]">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-white">Push notifications</p>
                                <p class="text-xs text-gray-400">Receive browser notifications</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0 min-h-[40px]">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle profile photo upload
document.querySelector('.relative.inline-block').addEventListener('click', function() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("user.updateProfile", $user->id) }}';
            form.enctype = 'multipart/form-data';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = 'avatar';
            fileInput.files = e.target.files;
            
            form.appendChild(csrfToken);
            form.appendChild(fileInput);
            document.body.appendChild(form);
            form.submit();
        }
    };
    input.click();
});
</script>
@endsection
