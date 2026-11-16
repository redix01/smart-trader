{{-- Mark + stacked wordmark. Styles: .brand-lockup (prime/styles.css) or Tailwind classes passed in $class. --}}
<span class="brand-lockup {{ $class ?? '' }}">
  @include('partials.brand-mark', ['size' => $size ?? 34])
  <span class="wm"><b>PRIME</b><small>DIGITAL CAPITAL</small></span>
</span>
