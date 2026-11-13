@props(['dark' => false])
{{-- Nano Market Pro logo: monogram tile + wordmark. Use on any page: <x-nano-logo /> (or :dark="true" on navy). --}}
<span {{ $attributes->class(['nm-brand', 'nm-brand-dark' => $dark]) }}>
  <svg class="nm-mark" viewBox="0 0 512 512" aria-hidden="true" focusable="false">
    <defs>
      <linearGradient id="nmg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e6cf9f"/><stop offset="1" stop-color="#b08a3e"/></linearGradient>
      <linearGradient id="nmb" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#141c33"/><stop offset="1" stop-color="#0b1020"/></linearGradient>
    </defs>
    <rect width="512" height="512" rx="112" fill="url(#nmb)"/>
    <rect x="9" y="9" width="494" height="494" rx="104" fill="none" stroke="#d8b878" stroke-opacity=".35" stroke-width="3"/>
    <rect x="136" y="128" width="52" height="256" rx="10" fill="#eef1f7"/>
    <rect x="324" y="128" width="52" height="256" rx="10" fill="#eef1f7"/>
    <path d="M136 128h52l188 256h-52z" fill="url(#nmg)"/>
  </svg>
  <span class="nm-word">Nano Market<small>PRO</small></span>
</span>
