@extends('frontpage.nano.auth')
@section('title', 'Confirm password')

@section('content')
  <p class="eyebrow">Security check</p>
  <h1>Confirm your password</h1>
  <p class="sub">This is a secure area. Please confirm your password before continuing.</p>

  <form method="POST" action="{{ route('password.confirm') }}" class="form" data-submit-once>
    @csrf
    <div class="field @error('password') invalid @enderror">
      <label for="password">Password</label>
      <div class="pw">
        <input id="password" name="password" type="password" required autofocus autocomplete="current-password">
        <button type="button" data-toggle-password="password">Show</button>
      </div>
      @error('password')<p class="field-err">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn btn-navy btn-block" data-busy="Checking…">Confirm</button>
  </form>
@endsection
