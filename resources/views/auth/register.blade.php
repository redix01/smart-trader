@extends('frontpage.nano.auth')
@section('title', 'Open an account')
@section('card_class', 'wide')

@section('content')
  <p class="eyebrow">Step 1 of 3</p>
  <h1>Open an account</h1>
  <p class="sub">It takes about two minutes. You will verify your email next.</p>

  <form method="POST" action="{{ route('register') }}" class="form" data-submit-once>
    @csrf
    <input type="hidden" name="ref" value="{{ old('ref', $referralCode ?? '') }}">

    <div class="form-row">
      <div class="field @error('name') invalid @enderror">
        <label for="name">Full name</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Jane Doe" required autofocus autocomplete="name">
        @error('name')<p class="field-err">{{ $message }}</p>@enderror
      </div>
      <div class="field @error('username') invalid @enderror">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="{{ old('username') }}" placeholder="janedoe" required autocomplete="username">
        @error('username')<p class="field-err">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="field @error('email') invalid @enderror">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="email">
      @error('email')<p class="field-err">{{ $message }}</p>@enderror
    </div>

    <div class="field @error('phone') invalid @enderror">
      <label for="phone">Phone</label>
      <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="+1 555 000 0000" required autocomplete="tel">
      @error('phone')<p class="field-err">{{ $message }}</p>@enderror
    </div>

    <div class="form-row">
      <div class="field @error('country') invalid @enderror">
        <label for="country">Country</label>
        <select id="country" name="country" required>
        <option value="">{{ __('Select your country') }}</option>
        <option value="United States" {{ old('country') == 'United States' ? 'selected' : '' }}>United States</option>
        <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
        <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
        <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>Germany</option>
        <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>France</option>
        <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>Australia</option>
        <option value="Japan" {{ old('country') == 'Japan' ? 'selected' : '' }}>Japan</option>
        <option value="South Korea" {{ old('country') == 'South Korea' ? 'selected' : '' }}>South Korea</option>
        <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>Singapore</option>
        <option value="Netherlands" {{ old('country') == 'Netherlands' ? 'selected' : '' }}>Netherlands</option>
        <option value="Switzerland" {{ old('country') == 'Switzerland' ? 'selected' : '' }}>Switzerland</option>
        <option value="Sweden" {{ old('country') == 'Sweden' ? 'selected' : '' }}>Sweden</option>
        <option value="Norway" {{ old('country') == 'Norway' ? 'selected' : '' }}>Norway</option>
        <option value="Denmark" {{ old('country') == 'Denmark' ? 'selected' : '' }}>Denmark</option>
        <option value="Finland" {{ old('country') == 'Finland' ? 'selected' : '' }}>Finland</option>
        <option value="Belgium" {{ old('country') == 'Belgium' ? 'selected' : '' }}>Belgium</option>
        <option value="Austria" {{ old('country') == 'Austria' ? 'selected' : '' }}>Austria</option>
        <option value="Italy" {{ old('country') == 'Italy' ? 'selected' : '' }}>Italy</option>
        <option value="Spain" {{ old('country') == 'Spain' ? 'selected' : '' }}>Spain</option>
        <option value="Portugal" {{ old('country') == 'Portugal' ? 'selected' : '' }}>Portugal</option>
        <option value="Ireland" {{ old('country') == 'Ireland' ? 'selected' : '' }}>Ireland</option>
        <option value="New Zealand" {{ old('country') == 'New Zealand' ? 'selected' : '' }}>New Zealand</option>
        <option value="Hong Kong" {{ old('country') == 'Hong Kong' ? 'selected' : '' }}>Hong Kong</option>
        <option value="Taiwan" {{ old('country') == 'Taiwan' ? 'selected' : '' }}>Taiwan</option>
        <option value="Israel" {{ old('country') == 'Israel' ? 'selected' : '' }}>Israel</option>
        <option value="United Arab Emirates" {{ old('country') == 'United Arab Emirates' ? 'selected' : '' }}>United Arab Emirates</option>
        <option value="Saudi Arabia" {{ old('country') == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia</option>
        <option value="Brazil" {{ old('country') == 'Brazil' ? 'selected' : '' }}>Brazil</option>
        <option value="Mexico" {{ old('country') == 'Mexico' ? 'selected' : '' }}>Mexico</option>
        <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India</option>
        <option value="China" {{ old('country') == 'China' ? 'selected' : '' }}>China</option>
        </select>
        @error('country')<p class="field-err">{{ $message }}</p>@enderror
      </div>
      <div class="field @error('currency') invalid @enderror">
        <label for="currency">Account currency</label>
        <select id="currency" name="currency" required>
        <option value="">{{ __('Select currency') }}</option>
        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
        <option value="JPY" {{ old('currency') == 'JPY' ? 'selected' : '' }}>JPY</option>
        <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>CAD</option>
        <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD</option>
        <option value="CHF" {{ old('currency') == 'CHF' ? 'selected' : '' }}>CHF</option>
        <option value="CNY" {{ old('currency') == 'CNY' ? 'selected' : '' }}>CNY</option>
        <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR</option>
        <option value="BRL" {{ old('currency') == 'BRL' ? 'selected' : '' }}>BRL</option>
        </select>
        @error('currency')<p class="field-err">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="form-row">
      <div class="field @error('password') invalid @enderror">
        <label for="password">Password</label>
        <div class="pw">
          <input id="password" name="password" type="password" placeholder="Create a password" required autocomplete="new-password">
          <button type="button" data-toggle-password="password">Show</button>
        </div>
        @error('password')<p class="field-err">{{ $message }}</p>@enderror
      </div>
      <div class="field">
        <label for="password_confirmation">Confirm password</label>
        <div class="pw">
          <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repeat password" required autocomplete="new-password">
          <button type="button" data-toggle-password="password_confirmation">Show</button>
        </div>
      </div>
    </div>

    @include('auth._honeypot')
    <input type="hidden" name="registration_time" value="{{ time() }}">

    <label class="check">
      <input id="terms" name="terms" type="checkbox" required>
      <span>I agree to the <a href="{{ route('terms') }}" class="link" target="_blank" rel="noopener">Terms</a> and acknowledge the <a href="{{ route('risk') }}" class="link" target="_blank" rel="noopener">risk disclosure</a>.</span>
    </label>
    @error('terms')<p class="field-err">{{ $message }}</p>@enderror

    <button type="submit" class="btn btn-navy btn-block" data-busy="Creating account…">Create account</button>
  </form>

  <p class="auth-foot">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
@endsection
