<header class="page-hero isolate">
  <video class="hero-video" autoplay muted loop playsinline preload="metadata" poster="{{ asset('prime/hero-poster.jpg') }}" aria-hidden="true"><source src="{{ asset('prime/hero-bg.mp4') }}" type="video/mp4"></video>
  <div class="hero-shade"></div>
  <div class="wrap">
    <span class="pill reveal">{{ $pill }}</span>
    <h1 class="reveal">{{ $title }}</h1>
    <p class="lead reveal">{{ $lead }}</p>
    @unless($noCta ?? false)
    <div class="cta reveal">
      @auth
        <a href="{{ route($ctaRoute ?? 'user.dashboard') }}" class="btn btn-primary">{{ $ctaAuth ?? 'Go to your dashboard' }}</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-primary">{{ $cta ?? 'Get started' }}</a>
        <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
      @endauth
    </div>
    @endunless
  </div>
</header>
