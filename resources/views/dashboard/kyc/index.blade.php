@extends('dashboard.layout.app')
@section('content')

@php
    $kycBadge = fn ($st) => match ($st) {
        'completed' => 'bg-emerald-500/15 text-emerald-400',
        'in_progress' => 'bg-blue-500/15 text-blue-300',
        default => 'bg-amber-500/15 text-amber-300',
    };
    $kycLabel = fn ($st) => ucfirst(str_replace('_', ' ', $st));
    $sectionsDone = collect([$kycStatus['personal_info']['status'], $kycStatus['address_info']['status'], $kycStatus['id_info']['status']])->filter(fn ($v) => $v === 'completed')->count();
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Account</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Identity verification</h1>
            <p class="text-sm text-gray-400 mt-0.5">Complete all three steps to verify your identity and unlock all platform features.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-400">{{ $sectionsDone }}/3 done</span>
            <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $kycBadge($kycStatus['overall_status']) }}">{{ $kycLabel($kycStatus['overall_status']) }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl border border-red-500/40 bg-red-500/10 text-sm text-red-300">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.kyc.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
        @csrf
        
        <!-- Personal Information -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-4 sm:mb-6">
                <h3 class="text-base font-semibold text-white"><span class="text-gray-500 font-mono mr-1">1.</span>Personal information</h3>
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $kycBadge($kycStatus['personal_info']['status']) }}">
                    {{ $kycLabel($kycStatus['personal_info']['status']) }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Full Name</label>
                    <input type="text" value="{{ $kycStatus['personal_info']['full_name'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/30 border border-gray-700 text-gray-400" disabled>
                    <p class="text-xs text-gray-500 mt-1">Full name cannot be changed</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                    <input type="email" value="{{ $kycStatus['personal_info']['email'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/30 border border-gray-700 text-gray-400" disabled>
                    <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Phone</label>
                    <input type="text" value="{{ $kycStatus['personal_info']['phone'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/30 border border-gray-700 text-gray-400" disabled>
                    <p class="text-xs text-gray-500 mt-1">Phone cannot be changed</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Date of Birth</label>
                    <input type="date" name="date_of_birth" autocomplete="bday" value="{{ $kycStatus['personal_info']['date_of_birth'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Nationality</label>
                    <input type="text" name="nationality" value="{{ $kycStatus['personal_info']['nationality'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-4 sm:mb-6">
                <h3 class="text-base font-semibold text-white"><span class="text-gray-500 font-mono mr-1">2.</span>Address</h3>
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $kycBadge($kycStatus['address_info']['status']) }}">
                    {{ $kycLabel($kycStatus['address_info']['status']) }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Street Address</label>
                    <input type="text" name="street_address" value="{{ $kycStatus['address_info']['street_address'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">City</label>
                    <input type="text" name="city" value="{{ $kycStatus['address_info']['city'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">State/Province</label>
                    <input type="text" name="state" value="{{ $kycStatus['address_info']['state'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Postal Code</label>
                    <input type="text" name="postal_code" autocomplete="postal-code" value="{{ $kycStatus['address_info']['postal_code'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Country</label>
                    <input type="text" name="country" value="{{ $kycStatus['address_info']['country'] }}" 
                           class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- ID Information -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-4 sm:mb-6">
                <h3 class="text-base font-semibold text-white"><span class="text-gray-500 font-mono mr-1">3.</span>Identity documents</h3>
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $kycBadge($kycStatus['id_info']['status']) }}">
                    {{ $kycLabel($kycStatus['id_info']['status']) }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">ID Type</label>
                    <select name="id_type" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select ID Type</option>
                        <option value="passport" {{ $kycStatus['id_info']['id_type'] === 'passport' ? 'selected' : '' }}>Passport</option>
                        <option value="national_id" {{ $kycStatus['id_info']['id_type'] === 'national_id' ? 'selected' : '' }}>National ID</option>
                        <option value="drivers_license" {{ $kycStatus['id_info']['id_type'] === 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">ID Front Side</label>
                    <input type="file" name="id_front" accept="image/*" 
                           class="block w-full text-sm text-gray-300 rounded-lg bg-gray-900/60 border border-gray-600 p-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600">
                    <p class="text-xs text-gray-500 mt-1">Upload front side of your ID document</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">ID Back Side</label>
                    <input type="file" name="id_back" accept="image/*" 
                           class="block w-full text-sm text-gray-300 rounded-lg bg-gray-900/60 border border-gray-600 p-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600">
                    <p class="text-xs text-gray-500 mt-1">Upload back side of your ID document</p>
                </div>
                
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Selfie with ID (Optional)</label>
                    <input type="file" name="selfie" accept="image/*" 
                           class="block w-full text-sm text-gray-300 rounded-lg bg-gray-900/60 border border-gray-600 p-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600">
                    <p class="text-xs text-gray-500 mt-1">Take a selfie while holding your ID document (optional)</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
            <p class="text-xs text-gray-500 sm:mr-auto">Our compliance team reviews your documents before your account is verified.</p>
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                Submit for review
            </button>
        </div>
    </form>
</div>

@endsection
