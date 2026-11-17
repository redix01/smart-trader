@extends('dashboard.layout.app')
@section('title', 'Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 p-4 sm:p-6">
    <div>
        <p class="eyebrow" style="font-family:var(--mono);font-size:12px;letter-spacing:.16em;text-transform:uppercase;color:var(--gold)">Account</p>
        <h1 style="margin-top:8px">{{ __('Profile') }}</h1>
    </div>

    <div class="p-4 sm:p-8 bg-white border border-gray-700 rounded-2xl">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white border border-gray-700 rounded-2xl">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white border border-gray-700 rounded-2xl">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
