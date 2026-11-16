{{-- Prime Digital Capital palette: near-black surfaces + electric-blue accent. Include right after the Tailwind CDN script.
     The accent scale keeps the `gold-*` name so existing dashboard classes pick it up unchanged. --}}
<script>
  tailwind.config = Object.assign(tailwind.config || {}, {
    darkMode: 'class',
    theme: { extend: { colors: {
      gray: { 50:'#f5f7fb', 100:'#eef1f7', 200:'#dde3ee', 300:'#c3cad9', 400:'#8e97ab', 500:'#646d82', 600:'#363d50', 700:'#1d2233', 800:'#12151f', 900:'#0a0c14', 950:'#06070c' },
      gold: { 300:'#c4dcff', 400:'#8fbcff', 500:'#4f8cff', 600:'#3a6fe0' }
    } } }
  });
</script>
