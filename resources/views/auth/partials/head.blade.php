{{-- Shared Prime styling for the standalone auth pages. Include right before </head> (after the Tailwind CDN script). --}}
<meta name="theme-color" content="#050a1e">
<link rel="icon" href="{{ asset('img/pdc-icon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/pdc-icon-180.png') }}?v=6">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script>
  tailwind.config = Object.assign(tailwind.config || {}, {
    darkMode: 'class',
    theme: { extend: { colors: {
      gray: { 50:'#f5f7fb', 100:'#eef1f7', 200:'#dde3ee', 300:'#c3cbe3', 400:'#8e9ac0', 500:'#6b779b', 600:'#253461', 700:'#111b3e', 800:'#0b1330', 900:'#070e26', 950:'#050a1e' }
    } } }
  });
</script>
<style>
  body.auth-body{background:#050a1e;color:#eef2ff;font-family:Inter,system-ui,sans-serif;margin:0;-webkit-font-smoothing:antialiased}
  .auth-shell{min-height:100vh;min-height:100dvh;display:grid;grid-template-columns:1fr}
  .auth-panel{display:none}
  .auth-main{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 16px;position:relative;isolation:isolate}
  .auth-main::before{content:"";position:absolute;inset:0;z-index:-1;background:radial-gradient(60% 40% at 50% 0,rgba(59,130,246,.14),transparent 70%)}
  .auth-main>div{width:100%}
  .auth-main .bg-gray-800{background:#0b1330;border-color:rgba(170,190,255,.12);border-radius:16px;box-shadow:0 30px 80px -30px rgba(0,0,0,.8)}
  .auth-main input:not([type=checkbox]):not([type=radio]),.auth-main select,.auth-main textarea{font-size:16px} /* stops iOS zoom on focus */
  .auth-main h1,.auth-main h2{font-family:Manrope,Inter,sans-serif;letter-spacing:-.02em}
  @media (max-width:479px){.auth-main{padding:28px 14px;justify-content:flex-start}.auth-main .bg-gray-800{padding:22px !important}}
  @media (min-width:1024px){
    .auth-shell{grid-template-columns:minmax(0,1.05fr) minmax(0,1fr)}
    .auth-panel{display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;isolation:isolate;padding:48px 56px;border-right:1px dashed rgba(170,190,255,.16)}
    .auth-main .lg-hide{display:none}
    .auth-main{padding:48px 40px}
  }
  .auth-panel video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:-1;mix-blend-mode:screen;opacity:.55;-webkit-mask-image:linear-gradient(to bottom,#000 40%,transparent);mask-image:linear-gradient(to bottom,#000 40%,transparent)}
  .auth-panel h2{font:700 clamp(32px,3.2vw,46px)/1.08 Manrope,sans-serif;letter-spacing:-.025em;max-width:480px}
  .auth-panel p{color:#a7b1cf;margin-top:16px;max-width:440px;font-size:17px;line-height:1.6}
  .auth-panel ul{list-style:none;padding:0;margin:28px 0 0;display:flex;flex-direction:column;gap:12px}
  .auth-panel li{display:flex;gap:12px;color:#c3cbe3;font-size:15px}
  .auth-panel li::before{content:"";flex:none;width:20px;height:20px;margin-top:1px;border-radius:6px;background:rgba(59,130,246,.16) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M4 8.5l2.5 2.5L12 5.5' fill='none' stroke='%2360a5fa' stroke-width='2'/%3E%3C/svg%3E") center/13px no-repeat}
  .auth-panel .fine{font-size:12px;color:#6b779b;max-width:460px;line-height:1.6}
  .brand-lockup{display:inline-flex;align-items:center;gap:11px;color:#eef2ff}
  .brand-lockup .wm{display:flex;flex-direction:column;line-height:1;text-align:left}
  .brand-lockup .wm b{font:800 19px Manrope,sans-serif;letter-spacing:.06em}
  .brand-lockup .wm small{font:600 9.5px Inter,sans-serif;letter-spacing:.34em;color:#a7b1cf;margin-top:5px}
</style>
