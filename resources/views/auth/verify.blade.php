@extends('frontpage.nano.auth')
@section('title', 'Verify email')

@section('content')
  <p class="eyebrow">Step 2 of 3</p>
  <h1>Verify your email</h1>
  <p class="sub">
    We sent a 6-digit code to your email address.
    @if(request()->query('email'))<br><strong>{{ request()->query('email') }}</strong>@endif
  </p>

  @if (session('success'))<div class="alert alert-ok">{{ session('success') }}</div>@endif
  @if (session('error'))<div class="alert alert-err">{{ session('error') }}</div>@endif

  <form method="POST" action="{{ route('verification.code.verify') }}" class="form" data-submit-once>
    @csrf
    <div class="field @error('code') invalid @enderror">
      <label for="code">Verification code</label>
      <input id="code" name="code" type="text" inputmode="numeric" maxlength="6" value="{{ old('code') }}" placeholder="000000" class="code-input" required autofocus autocomplete="one-time-code">
      @error('code')<p class="field-err">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn btn-navy btn-block" data-busy="Verifying…">Verify email</button>
  </form>

  <form id="resendForm" action="{{ route('verification.code.resend') }}" method="POST">
    @csrf
    <input type="hidden" name="email" value="{{ request()->query('email') }}">
    <p class="auth-foot">Didn't get the code? <button type="submit" class="link" style="background:none;border-top:0;border-left:0;border-right:0;font:inherit;cursor:pointer">Resend code</button></p>
  </form>
  <p class="auth-note"><a href="{{ route('login') }}">Back to log in</a></p>
@endsection

@push('scripts')
<script>
document.getElementById('code').addEventListener('input', function (e) {
  e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
});
</script>
@endpush
