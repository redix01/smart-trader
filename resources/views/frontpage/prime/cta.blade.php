<section class="sec">
  <div class="wrap">
    <div class="cta-band reveal">
      <div class="candles-bg"></div>
      <span class="pill">{{ $pill ?? 'Get started' }}</span>
      <h2>{{ $title ?? 'Start trading with Prime Digital Capital' }}</h2>
      <p class="lead">{{ $lead ?? 'Open an account in minutes and get live prices, a personal trade room and copy trading in one place.' }}</p>
      <div class="cta">
        @auth
          <a href="{{ route('user.dashboard') }}" class="btn btn-primary">Go to your dashboard</a>
        @else
          <a href="{{ route('register') }}" class="btn btn-primary">Get started</a>
          <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
        @endauth
      </div>
    </div>
  </div>
</section>
