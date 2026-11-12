{{-- Nano Market Pro palette: navy surfaces + gold accent. Include right after the Tailwind CDN script. --}}
<script>
  tailwind.config = Object.assign(tailwind.config || {}, {
    darkMode: 'class',
    theme: { extend: { colors: {
      gray: { 50:'#f8f7f3', 100:'#f1efe8', 200:'#e3ddcf', 300:'#c9cede', 400:'#9aa4bb', 500:'#6b7286', 600:'#3b4357', 700:'#1d2742', 800:'#141c33', 900:'#0b1020', 950:'#070b17' },
      gold: { 300:'#e6cf9f', 400:'#d8b878', 500:'#b08a3e', 600:'#8f6f2f' }
    } } }
  });
</script>
