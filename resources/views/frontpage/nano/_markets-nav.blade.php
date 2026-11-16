<nav class="subnav" aria-label="Markets">
  <a href="{{ route('forex') }}" @if(request()->routeIs('forex')) aria-current="page" @endif>Forex</a>
  <a href="{{ route('crypto') }}" @if(request()->routeIs('crypto')) aria-current="page" @endif>Crypto</a>
  <a href="{{ route('stocks') }}" @if(request()->routeIs('stocks')) aria-current="page" @endif>Stocks</a>
</nav>
