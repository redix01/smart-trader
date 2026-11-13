{{-- Nano Market Pro light theme for the user dashboard (landing-page palette).
     Remaps the Tailwind scales the existing views already use so every page
     inherits the same look. Include right after the Tailwind CDN script.
     The admin panel keeps partials/nano-theme (navy). --}}
<script>
  tailwind.config = {
    theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'], serif: ['Fraunces','Georgia','serif'], mono: ['JetBrains Mono','ui-monospace','monospace'] },
      colors: {
        /* surfaces: 900 page, 800 card, 700 subtle/border, 600 stronger border */
        gray: { 50:'#f8f7f3', 100:'#f1efe8', 200:'#1d2742', 300:'#3b4357', 400:'#6b7286', 500:'#8a90a2', 600:'#d9d2c1', 700:'#ece7db', 800:'#ffffff', 900:'#f6f3ec', 950:'#efe9dc' },
        /* primary actions: navy; links/accents: gold */
        blue: { 50:'#f6f0e1', 100:'#efe4c8', 200:'#e6cf9f', 300:'#b08a3e', 400:'#8f6f2f', 500:'#0b1020', 600:'#0b1020', 700:'#141c33', 800:'#6e5421', 900:'#3a2d12' },
        cyan: { 300:'#b08a3e', 400:'#8f6f2f', 500:'#0b1020', 600:'#0b1020' },
        indigo: { 400:'#8f6f2f', 500:'#0b1020', 600:'#0b1020' },
        purple: { 300:'#6a4aa0', 400:'#6a4aa0', 500:'#4b3478', 600:'#3d2a63' },
        green: { 300:'#1f7a4d', 400:'#1f7a4d' },
        emerald: { 300:'#1f7a4d', 400:'#1f7a4d' },
        red: { 300:'#a32a2a', 400:'#a32a2a' },
        yellow: { 300:'#8a6a12', 400:'#8a6a12' },
        orange: { 300:'#a2560f', 400:'#a2560f' },
        gold: { 300:'#e6cf9f', 400:'#d8b878', 500:'#b08a3e', 600:'#8f6f2f' }
      }
    } }
  };
</script>
