<section class="tight">
  <div class="wrap">
    <div class="band">
      <h2>Ready to trade with more clarity?</h2>
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-navy">Go to your dashboard</a>
      @else
        <div class="cta">
          <a href="{{ route('register') }}" class="btn btn-navy">Open an account</a>
          <a href="{{ route('login') }}" class="btn btn-line">Log in</a>
        </div>
      @endauth
    </div>
  </div>
</section>
