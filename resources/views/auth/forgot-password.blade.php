@extends('frontpage.nano.auth')
@section('title', 'Reset password')

@section('content')
  <p class="eyebrow">Account recovery</p>
  <h1>Forgot your password?</h1>
  <p class="sub">Enter your email and we will send you a link to choose a new one.</p>

  @if (session('status'))
    <div class="alert alert-ok">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('password.email') }}" class="form" data-submit-once>
    @csrf
    <div class="field @error('email') invalid @enderror">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
      @error('email')<p class="field-err">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn btn-navy btn-block" data-busy="Sending…">Email reset link</button>
  </form>

  <p class="auth-foot"><a href="{{ route('login') }}">Back to log in</a></p>
@endsection
