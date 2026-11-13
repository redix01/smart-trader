@extends('frontpage.nano.auth')
@section('title', 'Choose a new password')

@section('content')
  <p class="eyebrow">Account recovery</p>
  <h1>Choose a new password</h1>
  <p class="sub">Use at least 8 characters. You will be logged out of other devices.</p>

  <form method="POST" action="{{ route('password.store') }}" class="form" data-submit-once>
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="field @error('email') invalid @enderror">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autocomplete="username">
      @error('email')<p class="field-err">{{ $message }}</p>@enderror
    </div>

    <div class="field @error('password') invalid @enderror">
      <label for="password">New password</label>
      <div class="pw">
        <input id="password" name="password" type="password" placeholder="New password" required autofocus autocomplete="new-password">
        <button type="button" data-toggle-password="password">Show</button>
      </div>
      @error('password')<p class="field-err">{{ $message }}</p>@enderror
    </div>

    <div class="field">
      <label for="password_confirmation">Confirm password</label>
      <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repeat new password" required autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-navy btn-block" data-busy="Saving…">Reset password</button>
  </form>

  <p class="auth-foot"><a href="{{ route('login') }}">Back to log in</a></p>
@endsection
