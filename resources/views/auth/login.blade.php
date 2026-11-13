@extends('frontpage.nano.auth')
@section('title', 'Log in')

@section('content')
  <p class="eyebrow">Welcome back</p>
  <h1>Log in</h1>
  <p class="sub">Access your Nano Market Pro account.</p>

  @if (session('status'))
    <div class="alert alert-ok">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}" class="form" data-submit-once>
    @csrf
    <div class="field @error('email') invalid @enderror">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username">
      @error('email')<p class="field-err">{{ $message }}</p>@enderror
    </div>

    <div class="field @error('password') invalid @enderror">
      <label for="password">Password</label>
      <div class="pw">
        <input id="password" name="password" type="password" placeholder="Your password" required autocomplete="current-password">
        <button type="button" data-toggle-password="password">Show</button>
      </div>
      @error('password')<p class="field-err">{{ $message }}</p>@enderror
    </div>

    @include('auth._honeypot')
    <input type="hidden" name="login_time" value="{{ time() }}">

    <div class="row-between">
      <label class="check"><input type="checkbox" id="remember" name="remember"> Remember me</label>
      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
      @endif
    </div>

    <button type="submit" class="btn btn-navy btn-block" data-busy="Signing in…">Log in</button>
  </form>

  <p class="auth-foot">New to Nano Market Pro? <a href="{{ route('register') }}">Open an account</a></p>
  <p class="auth-note">Trading involves risk. <a href="{{ route('risk') }}">Risk disclosure</a></p>
@endsection
