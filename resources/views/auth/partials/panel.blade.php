<aside class="auth-panel" aria-hidden="true">
  <video autoplay muted loop playsinline preload="metadata" poster="{{ asset('prime/hero-poster.jpg') }}"><source src="{{ asset('prime/hero-bg.mp4') }}" type="video/mp4"></video>
  <a href="{{ route('index') }}" tabindex="-1">@include('partials.brand-lockup', ['size' => 40])</a>
  <div>
    <h2>Stocks, crypto and copy trading in one account.</h2>
    <p>Trade yourself in the Trade Room, follow experienced traders, or let an AI trader work a strategy for you.</p>
    <ul>
      <li>Live stock and crypto prices</li>
      <li>Copy trading, AI traders and bots</li>
      <li>Staking and mining plans for idle balance</li>
    </ul>
  </div>
  <p class="fine">Trading involves substantial risk and may result in the loss of your capital. Past performance is not indicative of future results.</p>
</aside>
