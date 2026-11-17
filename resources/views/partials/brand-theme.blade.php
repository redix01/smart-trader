{{-- Prime Digital Capital palette: deep navy surfaces + blue accent (matches the public site).
     Include right after the Tailwind CDN script. The accent scale keeps the `gold-*` name
     so existing classes pick it up unchanged. --}}
<script>
  tailwind.config = Object.assign(tailwind.config || {}, {
    darkMode: 'class',
    theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'], mono: ['JetBrains Mono','ui-monospace','monospace'] },
      colors: {
        gray: { 50:'#f5f7fb', 100:'#eef1f7', 200:'#dde3ee', 300:'#c3cbe3', 400:'#8e9ac0', 500:'#6b779b', 600:'#253461', 700:'#111b3e', 800:'#0b1330', 900:'#070e26', 950:'#050a1e' },
        gold: { 300:'#c4dcff', 400:'#8fbcff', 500:'#4f8cff', 600:'#3a6fe0' }
      }
    } }
  });
</script>
