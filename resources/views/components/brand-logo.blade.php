@props(['size' => 34])
{{-- Prime Digital Capital logo for app pages: <x-brand-logo /> --}}
<span {{ $attributes->merge(['class' => 'brand-lockup']) }}>
  @include('partials.brand-mark', ['size' => $size])
  <span class="wm"><b>PRIME</b><small>DIGITAL CAPITAL</small></span>
</span>
