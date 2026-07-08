

<!doctype html>
<html class="no-js" lang="en-US">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<base href="{{ asset("frontpage") }}/">
<link href="{{ asset("favicon.ico") }}" rel="icon" type="image/x-icon" />
<!-- Add icon library -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
 <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="icon" type="image/png" href="images/logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css" integrity="sha512-kJlvECunwXftkPwyvHbclArO8wszgBGisiLeuDFwNM8ws+wKIw0sv1os3ClWZOcrEB2eRXULYUsm8OVRGJKwGA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" href="slick/slick.css">
<link rel="stylesheet" type="text/css" href="slick/slick-theme.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- jQuery library -->
<script src="js/jquery.min.js"></script>
<!-- Popper JS -->
<script src="js/popper.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.form.js"></script>

<script src="js/moment.js"></script>
<script src="js/livestamp.min.js"></script>
<script src="js/imagesloaded.pkgd.min.js"></script>

<link href="css/main60c6.css?r=365" rel="stylesheet"/>
<link href="css/tw81ce.css?r=334" rel="stylesheet"/>
<link href="css/animate.css" rel="stylesheet" />
<link rel="stylesheet" href="css/intlTelInput.css">

<title>{{ config('app.name') }}</title>
<link rel="manifest" href="js/manifest.json">
<meta name="theme-color" content="#0C0F19">
<meta name="msapplication-navbutton-color" content="#0C0F19">
<!-- iOS Safari -->
<meta name="apple-mobile-web-app-status-bar-style" content="#0C0F19">


<meta name="theme-color" content="#0C0F19">
<meta name="msapplication-navbutton-color" content="#0C0F19">
<!-- iOS Safari -->
<meta name="apple-mobile-web-app-status-bar-style" content="#0C0F19">

<link href="{{ asset("favicon.ico") }}" rel="icon" type="image/x-icon" />
<link rel="icon" sizes="192x192" href="{{ asset("frontpage/images/logo.png") }}">

<meta name="keywords" content="{{ config('app.name') }}, Options, Crypto" />
<meta property="og:image" content="images/logo.png" />
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="Crypto Trading With {{ config('app.name') }}" />
<meta name="description" content="Crypto Trading With {{ config('app.name') }}, is totally different from its competitors trying to achieve something special starting with the...">
<meta property="og:description" content="Crypto Trading With {{ config('app.name') }}, is totally different from its competitors trying to achieve something special starting with the...">
<meta property="og:type" content="website" />

<script src="js/bootstrap-select.js"></script>
<script src="js/src/jquery.waypoints.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-select.css">


<script src="js/countUp.min.js"></script>
<script src="js/jquery.marquee.min.js"></script>
<script src="js/jquery.easing.1.3.js"></script>

<script>
$(document).ready(function(){
  var options = {
    useEasing: true,
    useGrouping: true,
    separator: ",",
    decimal: "."
  };
  var counter = $(".counter");
  $('#waypoint').waypoint(function(direction) {
    counter.each(function(index) {
        var value = $(counter[index])();
        var counterAnimation = new CountUp(counter[index], 0, value, 0, 3, options);
        counterAnimation.start();
    });
  }, {
    offset: '50%'
  });
});
</script>
<style>
.navbar.bg-dark {
background:#000 !important;
}
.btn-primary,
.bg-primary {
background:#2563eb !important;
border-color:#2563eb !important;
}
.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active {
background:#1d4ed8 !important;
border-color:#1d4ed8 !important;
}
</style>
</head>
<body>
<div class="overlay closeMenu"></div>
<div class="account-sidebar udex-collapse shadow-lg p-0" id="mySidebar">
  <div class="account-header px-3 py-3">
      <div class="d-flex  justify-content-between">
      <a class="navbar-brand mr-3 p-0" href="{{ url("/") }}">
      @include('frontpage.partials.logo', ['class' => 'wow slideInLeft d-inline-block', 'height' => '55px'])
    </a>
        <button class="navbar-toggler btn btn-link text-dark"><i class="fas fa-times text-white"></i></button>
      </div>
  </div>
  <div style="height: calc(100% - 64px); overflow:auto" class='mx-auto'>
  <ul class="navbar-nav wow slideInLeft ">
        <li class="nav-item text-center">
          <div class="dropdown">
            <a href="#" class="nav-link d-flex justify-content-between" data-toggle="dropdown">Products</a>
            <div class="dropdown-menu p-3">
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">Spot</a>
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">Margin</a>
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">Bot Trading</a>
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">Copy Trading</a>
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">AI Traders</a>
            </div>
          </div>
        </li>
        <li class="nav-item text-center">
          <div class="dropdown">
            <a href="#" class="nav-link d-flex justify-content-between" data-toggle="dropdown">Company</a>
            <div class="dropdown-menu p-3">
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/about") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/flag.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>About us</h5>
                  <p class="mb-0">Learn more about our mission</p>
                </div>
              </a>
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/privacy-policy") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/smile.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>Privacy Policy</h5>
                  <p class="mb-0">Learn we handle your data</p>
                </div>
              </a>
            </div>
          </div>
        </li>
        <li class="nav-item text-center">
          <div class="dropdown">
            <a href="#" class="nav-link d-flex justify-content-between" data-toggle="dropdown">Resources</a>
            <div class="dropdown-menu p-3">
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/faqs") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/book.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>FAQ</h5>
                  <p class="mb-0">Questions we get asked most</p>
                </div>
              </a>
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/stocks") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/dollar.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>Stock Trading</h5>
                  <p class="mb-0">Trading the stock markets</p>
                </div>
              </a>
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/forex") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/bank.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>Forex Trading</h5>
                  <p class="mb-0">Trading foreign exchange</p>
                </div>
              </a>
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/crypto") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/eth.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>Crypto Trading</h5>
                  <p class="mb-0">Trading cryptocurrencies</p>
                </div>
              </a>
            </div>
          </div>
        </li>
      </ul>
    <div class="p-3 d-flex wow slideInLeft">
      
        <a href="{{ route("login") }}" class="btn btn-dark rounded-md my-2 btn-block">LOGIN</a>
        <a href="{{ route("register") }}" class="btn btn-primary rounded-md my-2 btn-block">SIGNUP</a>    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  $(".navbar-toggler").click( function(){
    $("#mySidebar").toggle({ direction: "left" }, 1000);
    $(".closeMenu").fadeToggle();
  });
  $(".closeMenu").click( function(){
    $("#mySidebar").toggle({ direction: "left" }, 1000);
    $(".closeMenu").fadeToggle();
  });
  $('#mySidebar a[href!="#"]').click(function(){
    $("#mySidebar").toggle({ direction: "left" }, 1000);
    $(".closeMenu").fadeToggle();
  });
});
</script>
<style>
  .cg-container {
    border: 0 !important;
  }
  .skiptranslate {
    display: none !important;
  }
  body {
    top: 0 !important;
  }
</style>
<style>
.slick-dots{bottom: 10px !important; right: 10px !important;}
.slick-dots li button:hover:before,.slick-dots li button:focus:before{color:#05ae5a;}
.slick-dots li button:before{font-size: 12px;}
.slick-dots li.slick-active button:before{color:#05ae5a; font-size: 20px;}
.slick-dots li.slick-active button:before{opacity: 1;}
.slick-dotted.slick-slider{ margin-bottom: 0px;}
</style>

<script>
$(document).ready(function(){
  var nav = $('header');
  nav.removeClass('shadow-sm');
  /*--window Scroll functions--*/
  $(window).on('scroll', function () {
    /*--show and hide scroll to top --*/
    if ($(window).scrollTop() > 100) {
      nav.addClass('shadow-sm');
      nav.addClass('fixed-top');
      nav.addClass('bg-white');
      nav.children('nav').removeClass('navbar-dark').addClass('navbar-light');
      nav.find('.btn-light').removeClass('btn-light').addClass('btn-primary');
    } else {
      nav.removeClass('shadow-sm');
      nav.removeClass('fixed-top');
      nav.removeClass('bg-white');
      nav.children('nav').addClass('navbar-dark').removeClass('navbar-light');
      nav.find('.btn-primary').addClass('btn-light').removeClass('btn-primary');
    }
  });
});
</script>
<script>
  function functionConfirm(msg, myYes, myNo) {
    var confirmBox = $("#confirm");
    confirmBox.find(".message").text(msg);
    confirmBox.find(".yes,.no").unbind().click(function() {
      confirmBox.fadeOut();
    });
    confirmBox.find(".yes").click(myYes);
    confirmBox.find(".no").click(myNo);
    confirmBox.fadeIn();
  }
</script>
<div id="confirm">
  <div class="shadow rounded container border">
      <div class="message p-3"></div>
      <div class="text-right border-top p-3 bg-light">
        <button aria-label="Yes" class="btn btn-primary yes">Yes</button>
        <button aria-label="No" class="btn btn-primary no">No</button>
      </div>
  </div>
</div>
<!-- TradingView Ticker Tape -->
<div style="background: #000;">
  <script type="module" src="https://widgets.tradingview-widget.com/w/en/tv-ticker-tape.js"></script>
  <tv-ticker-tape theme="dark" symbols="FOREXCOM:SPXUSD,FOREXCOM:NSXUSD,FOREXCOM:DJI,FX:EURUSD,BITSTAMP:BTCUSD,BITSTAMP:ETHUSD,CMCMARKETS:GOLD,NASDAQ:AAPL,NASDAQ:MSFT,NASDAQ:GOOGL,NASDAQ:AMZN,NYSE:TSLA"></tv-ticker-tape>
</div>

<nav class="navbar navbar-expand-md navbar-dark bg-dark text-light py-0">

    <!-- Brand -->
    <a class="navbar-brand mr-3 p-0 wow slideInLeft" href="{{ url("/") }}">
      @include('frontpage.partials.logo', ['class' => 'wow slideInLeft d-inline-block', 'height' => '55px'])
    </a>
  

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav wow slideInLeft">
        <li class="nav-item">
          <div class="dropdown">
            <a href="#" class="nav-link" data-toggle="dropdown">Products</a>
            <div class="dropdown-menu dropdown-menu-right p-3">
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">Spot</a>
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">Margin</a>
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">Bot Trading</a>
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">Copy Trading</a>
              <a class="dropdown-item py-2" href="{{ route('user.dashboard') }}">AI Traders</a>
            </div>
          </div>
        </li>
        <li class="nav-item">
          <div class="dropdown">
            <a href="#" class="nav-link" data-toggle="dropdown">Company</a>
            <div class="dropdown-menu dropdown-menu-right p-3">
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/about") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/flag.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>About us</h5>
                  <p class="mb-0">Learn more about our mission</p>
                </div>
              </a>
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/privacy-policy") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/smile.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>Privacy Policy</h5>
                  <p class="mb-0">Learn we handle your data</p>
                </div>
              </a>
            </div>
          </div>
        </li>
        <li class="nav-item">
          <div class="dropdown">
            <a href="#" class="nav-link" data-toggle="dropdown">Resources</a>
            <div class="dropdown-menu dropdown-menu-right p-3">
            <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/faqs") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/book.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>FAQ</h5>
                  <p class="mb-0">Questions we get asked most</p>
                </div>
              </a>
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/stocks") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/dollar.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>Stock Trading</h5>
                  <p class="mb-0">Trading the stock markets</p>
                </div>
              </a>
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/forex") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/bank.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>Forex Trading</h5>
                  <p class="mb-0">Trading foreign exchange</p>
                </div>
              </a>
              <a class="dropdown-item px-2 d-flex py-3" href="{{ url("/crypto") }}">
                <div class="mx-2" style="flex: 0 0 30px">
                  <img src="images/eth.svg" alt="">
                </div>
                <div class="pr-2">
                  <h5>Crypto Trading</h5>
                  <p class="mb-0">Trading cryptocurrencies</p>
                </div>
              </a>
            </div>
          </div>
        </li>
      </ul>
      <form class="form-inline my-2 ml-auto my-lg-0 wow slideInLeft">
        
          <a href="{{ route("login") }}" class="btn btn-link rounded-md my-2 mx-3">Login</a>
          <a href="{{ route("register") }}" class="btn btn-primary rounded-md my-2">Get started</a>      </form>
    </div>
    <!-- Toggler/collapsibe Button -->
    <button class="navbar-toggler py-4" type="button">
    <i class="ri-menu-3-line text-white"></i>
    </button>
</nav><div class="hero-image">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6 col-12">
				<div class="hero-text wow slideInLeft">
					<div class="mt-4 display-4 font-bolder">All your trading essentials integrated into one app</div>
					<p class="text-lightGray leading-[180%] pt-sm text-pretty text-lg">4000+ of your favourite assets, superior charts, and advanced trading tools to help you get ahead.</p>
					<div style="max-width: 800px" class="mx-auto">
						<p class="wow slideInLeft my-4"> </p>
					</div>
					<a href="{{ route("register") }}" class="btn btn-primary btn-lg px-4 rounded-md mr-1">Get Started</a>
					<a href="{{ route("login") }}" class="btn btn-outline-light btn-lg px-4 rounded-md">Login</a>
				</div>
			</div>
			<div class="col-md-6 col-12 wow slideInRight">
			<video src="medias/play_720p.mp4" preload="auto" autoplay loop muted playsinline webkit-playsinline x5-playsinline style="width: 100%; height: 100%;"></video>
			</div>
		</div>
	</div>
</div>
<div class='hero-image'>
<div class="container text-center py-4 text-white">
	<section class="py-5 wow slideInUp">
		<h2 class='text-center text-white h2 font-bold mb-4 hyphenated'>Multiple ways to trade</h2>
		<p class='text-center text-lightGray text-lg font-normal leading-8'>Experience trading excellence on the go, from any browser or desktop, with {{ config('app.name') }} .

Trade your way, every day!</p>

<div class=''>
<video preload="auto" autoplay loop muted  playsinline webkit-playsinline x5-playsinline style="width: 100%; height: 100%;" src="medias/play_721p.mp4"></video>
<a href="{{ route("register") }}" class="btn btn-primary mx-auto h2 px-2 mt-3">Try Now</a>
</div>
<!-- TradingView Market Data Widget -->
<script type="module" src="https://widgets.tradingview-widget.com/w/en/tv-market-data.js"></script>
<tv-market-data theme="dark" symbol-sectors='[{"sectionName":"Forex","symbols":["FX:EURUSD","FX:GBPUSD","FX:USDJPY","FX:USDCHF","FX:AUDUSD","FX:USDCAD"]},{"sectionName":"Cryptocurrency","symbols":["BINANCE:BTCUSDT","BINANCE:ETHUSDT","BINANCE:SOLUSDT","BINANCE:BNBUSDT","KUCOIN:HYPEUSDT","BITSTAMP:XRPUSD"]},{"sectionName":"Stocks","symbols":["NASDAQ:TSLA","NASDAQ:NVDA","NASDAQ:AAPL","NASDAQ:MU","NASDAQ:MSFT","NASDAQ:SPCX","NASDAQ:META","NASDAQ:AMZN","NASDAQ:GOOGL","NSE:TRENT"]}]'></tv-market-data>



		<div class="row">
		<h2 class="text-center text-white h2 mb-md hyphenated mx-auto mt-5 mb-2">4000+ assets. A world of trading possibilities</h2>
		<div class="text-center text-lightGray text-lg leading-8 mx-auto lg:max-w-[750px]">Access the world’s most sought-after assets. CFDs on stocks, Forex, indices, commodities, ETFs, bonds and crypto — you can trade them all.</div>
		<a href="{{ route("register") }}" class="btn btn-primary mx-auto my-3">Get started</a>
		</div>
	</section>
</div></div>

<!-- Start Trading Now -->
<section class="py-5" style="background:#000;">
  <div class="text-center" style="max-width:800px; margin:0 auto; padding:0 16px;">
    <p style="color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; font-size:0.9rem; margin-bottom:16px;">Trusted by thousands of users worldwide</p>
    <h2 style="color:#fff; font-weight:600; font-size:2.25rem; margin-bottom:12px;">Start Trading <span style="color:#2563eb !important;">Now</span></h2>
    <p style="color:#9ca3af; font-size:1.125rem; margin-bottom:40px;">Choose how you want to trade and invest in stocks and financial markets</p>
  </div>
  <div class="container">
    <div class="row" style="display:flex; flex-wrap:wrap; gap:24px; justify-content:center;">
      <div class="col-md-4" style="flex:1 1 300px; max-width:380px;">
        <div style="background:#0d0f13; border:1px solid #1f2937; border-radius:8px; padding:32px; height:100%;">
          <div style="width:48px; height:48px; border-radius:50%; background:#1a1428; display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
            <i class="fas fa-chart-line" style="color:#2563eb !important; font-size:1.25rem;"></i>
          </div>
          <h3 style="color:#fff; font-size:1.25rem; font-weight:600; margin-bottom:8px;">Stock Trading</h3>
          <p style="color:#9ca3af; margin-bottom:16px;">Trade stocks with advanced charts and real-time market data</p>
          <a href="{{ route('register') }}" style="color:#2563eb !important; text-decoration:none; font-weight:500;">Trade Now <i class="fas fa-arrow-right"></i></a>
        </div>
      </div>
      <div class="col-md-4" style="flex:1 1 300px; max-width:380px;">
        <div style="background:#0d0f13; border:1px solid #1f2937; border-radius:8px; padding:32px; height:100%;">
          <div style="width:48px; height:48px; border-radius:50%; background:#1a1428; display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
            <i class="fas fa-copy" style="color:#2563eb !important; font-size:1.25rem;"></i>
          </div>
          <h3 style="color:#fff; font-size:1.25rem; font-weight:600; margin-bottom:8px;">Copy Trading</h3>
          <p style="color:#9ca3af; margin-bottom:16px;">Automatically copy the trades of successful traders</p>
          <a href="{{ route('register') }}" style="color:#2563eb !important; text-decoration:none; font-weight:500;">Start Copying <i class="fas fa-arrow-right"></i></a>
        </div>
      </div>
      <div class="col-md-4" style="flex:1 1 300px; max-width:380px;">
        <div style="background:#0d0f13; border:1px solid #1f2937; border-radius:8px; padding:32px; height:100%;">
          <div style="width:48px; height:48px; border-radius:50%; background:#1a1428; display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
            <i class="fas fa-robot" style="color:#2563eb !important; font-size:1.25rem;"></i>
          </div>
          <h3 style="color:#fff; font-size:1.25rem; font-weight:600; margin-bottom:8px;">Bot Trading</h3>
          <p style="color:#9ca3af; margin-bottom:16px;">Automate your trading with AI-powered bots and strategies</p>
          <a href="{{ route('register') }}" style="color:#2563eb !important; text-decoration:none; font-weight:500;">Start Bot <i class="fas fa-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>

	<section class="py-5 row">
	<div class="text-center px-4 px-md-0 col-md-6 col-12 mx-auto wow slideInUp">
		<video preload="auto" autoplay loop muted  playsinline webkit-playsinline x5-playsinline style="width: 100%; height: 100%;display: block;
margin: 0 auto;" src="medias/play_723p.mp4"></video>
		</div>
	<div class="col-md-6 col-12 py-4 section-title">
			<div class='my-auto text-center'><h2 class="mb-4">Introducing Autocopy</h2>
			<p class="text-muted">Learn, copy, and trade in one click. Tap into the experience of others and collaborate with a growing community of traders.</p>
			<a href="{{ route("register") }}" class="btn btn-primary mx-auto my-3">Try Autocopy</a></div>
		</div>
		<div class='row  gap-3'>
		<div class='col-mg-6 col-12'><video preload="auto" autoplay loop muted  playsinline webkit-playsinline x5-playsinline style="width: 100%; height: 100%;display: block;
margin: 0 auto;" src="medias/play_724p.mp4"></video></div>
		</div>
	</section>

<!-- Trading Advantages -->
<section class="py-5" style="background:#0C0F19;">
  <div class="container">
    <div class="text-center mb-5">
      <h2 style="color:#fff; font-weight:700; font-size:2.25rem;">Trading <span style="color:#2563eb !important;">Advantages</span></h2>
      <p style="color:#9ca3af; font-size:1.125rem; max-width:800px; margin:0 auto;">
        Discover the key benefits that make our platform the preferred choice for successful traders worldwide.
      </p>
    </div>
    <div class="row">
      <div class="col-md-4 mb-4">
        <div style="background:#151821; border:1px solid #1f2937; border-radius:16px; overflow:hidden; height:100%;">
          <div style="height:180px; background:linear-gradient(135deg,#1a1428,#0d0f13); display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-percent" style="color:#2563eb !important; font-size:3.5rem; opacity:0.85;"></i>
          </div>
          <div style="padding:24px; position:relative;">
            <div style="position:absolute; top:-24px; left:24px; width:48px; height:48px; border-radius:50%; background:#1a1428; border:2px solid #2563eb !important; display:flex; align-items:center; justify-content:center;">
              <i class="fas fa-percent" style="color:#2563eb !important; font-size:1.1rem;"></i>
            </div>
            <h3 style="color:#fff; font-size:1.25rem; font-weight:600; margin-top:24px; margin-bottom:12px;">Low Trading Fees</h3>
            <p style="color:#9ca3af; margin-bottom:16px;">Maximize your profits with our competitive fee structure. We offer some of the lowest trading fees in the industry, ensuring more of your gains stay in your pocket.</p>
            <ul style="list-style:none; padding-left:0; color:#9ca3af; margin-bottom:0;">
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#2563eb !important; margin-right:8px;"></i> Low trading fees</li>
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#2563eb !important; margin-right:8px;"></i> No hidden charges</li>
              <li><i class="fas fa-check-circle" style="color:#2563eb !important; margin-right:8px;"></i> Volume discounts available</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div style="background:#151821; border:1px solid #1f2937; border-radius:16px; overflow:hidden; height:100%;">
          <div style="height:180px; background:linear-gradient(135deg,#1a1428,#0d0f13); display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-headset" style="color:#2563eb !important; font-size:3.5rem; opacity:0.85;"></i>
          </div>
          <div style="padding:24px; position:relative;">
            <div style="position:absolute; top:-24px; left:24px; width:48px; height:48px; border-radius:50%; background:#1a1428; border:2px solid #2563eb !important; display:flex; align-items:center; justify-content:center;">
              <i class="fas fa-headset" style="color:#2563eb !important; font-size:1.1rem;"></i>
            </div>
            <h3 style="color:#fff; font-size:1.25rem; font-weight:600; margin-top:24px; margin-bottom:12px;">24/7 Support</h3>
            <p style="color:#9ca3af; margin-bottom:16px;">Get help whenever you need it with our round-the-clock customer support. Our expert team is always ready to assist you with any trading questions or technical issues.</p>
            <ul style="list-style:none; padding-left:0; color:#9ca3af; margin-bottom:0;">
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#2563eb !important; margin-right:8px;"></i> Live chat support</li>
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#2563eb !important; margin-right:8px;"></i> Expert trading guidance</li>
              <li><i class="fas fa-check-circle" style="color:#2563eb !important; margin-right:8px;"></i> Multilingual support</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div style="background:#151821; border:1px solid #1f2937; border-radius:16px; overflow:hidden; height:100%;">
          <div style="height:180px; background:linear-gradient(135deg,#1a1428,#0d0f13); display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-globe" style="color:#2563eb !important; font-size:3.5rem; opacity:0.85;"></i>
          </div>
          <div style="padding:24px; position:relative;">
            <div style="position:absolute; top:-24px; left:24px; width:48px; height:48px; border-radius:50%; background:#1a1428; border:2px solid #2563eb !important; display:flex; align-items:center; justify-content:center;">
              <i class="fas fa-globe" style="color:#2563eb !important; font-size:1.1rem;"></i>
            </div>
            <h3 style="color:#fff; font-size:1.25rem; font-weight:600; margin-top:24px; margin-bottom:12px;">Global Markets</h3>
            <p style="color:#9ca3af; margin-bottom:16px;">Access global financial markets from a single platform. Trade stocks, cryptocurrencies, and other assets from major exchanges worldwide with real-time data and execution.</p>
            <ul style="list-style:none; padding-left:0; color:#9ca3af; margin-bottom:0;">
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#2563eb !important; margin-right:8px;"></i> 120+ trading pairs</li>
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#2563eb !important; margin-right:8px;"></i> Multiple asset classes</li>
              <li><i class="fas fa-check-circle" style="color:#2563eb !important; margin-right:8px;"></i> Real-time market data</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container text-center py-4">
	<section class="py-5">
		<div class="py-4 section-title">
			<h2 class="mb-4">Built for today's ambitious earners</h2>
			<p class="text-muted">Thousands of forward-thinking users rely on {{ config('app.name') }} everyday to turbo-charge their financial operations</p>
		</div>
		<div class="marquee-parent">
			<div class="row justify-content-center wow slideInLeft">
				<div class="col-md-4 py-3">
					<div class="marquee " style="height: 800px; overflow: hidden"><div class="card m-1 mb-3">
						<div class="card-body text-left shadow-sm rounded-lg">
							<div style="color:#e94a15"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star" style="color: #ccc"></i></div>
							<h5 class="my-3">"Really Good Experience"</h5>
							<p class="text-muted">Amazing experience, i have a really wonderful experience using this platfrom.</p>
							<hr/>
							<div class="d-flex justify-content-center">
								<div class="mr-auto my-auto">
									<h6 class="mb-0">Laurent Correia</h6>
								</div>
								<div>
									<img src="{{ asset('frontpage/uploads/small/7331718407651.jpg') }}" style="width: 60px; height: 60px" class="rounded-circle" alt="Laurent Correia">
								</div>
							</div>
						</div>
					</div><div class="card m-1 mb-3">
						<div class="card-body text-left shadow-sm rounded-lg">
							<div style="color: #e94a15"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
							<h5 class="my-3">"Highly Recommended"</h5>
							<p class="text-muted">Great company, no issues with payouts, amazing customers support. Highly recommended  to experienced traders</p>
							<hr/>
							<div class="d-flex justify-content-center">
								<div class="mr-auto my-auto">
									<h6 class="mb-0"></h6>
								</div>
								<div>
									<img src="{{ asset('frontpage/images/avatar.png') }}" style="width: 60px; height: 60px" class="rounded-circle" alt="Profile avatar">
								</div>
							</div>
						</div>
					</div><div class="card m-1 mb-3">
						<div class="card-body text-left shadow-sm rounded-lg">
							<div style="color: #e94a15"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
							<h5 class="my-3">"Compañía perfecta"</h5>
							<p class="text-muted">Este corredor es perfecto para un principiante, es fácil de entender y su equipo de soporte siempre está disponible para ayudar.</p>
							<hr/>
							<div class="d-flex justify-content-center">
								<div class="mr-auto my-auto">
									<h6 class="mb-0">Yanira Cordero</h6>
								</div>
								<div>
									<img src="{{ asset('frontpage/uploads/small/9771718406954.jpg') }}" style="width: 60px; height: 60px" class="rounded-circle" alt="Yanira Cordero">
								</div>
							</div>
						</div>
					</div><div class="card m-1 mb-3">
						<div class="card-body text-left shadow-sm rounded-lg">
							<div style="color: #e94a15"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
							<h5 class="my-3">"Quick Funding and Withdrawal"</h5>
							<p class="text-muted">One of the most difficult situations for traders is funding their account and withdrawing, however eliteglobalhub have one of the quickest funding and withdrawal system. You literally get your withdrawal instantly in your crypto wallet.</p>
							<hr/>
							<div class="d-flex justify-content-center">
								<div class="mr-auto my-auto">
									<h6 class="mb-0">Aman Natt</h6>
								</div>
								<div>
									<img src="{{ asset('frontpage/uploads/small/6441718405442.jpg') }}" style="width: 60px; height: 60px" class="rounded-circle" alt="Aman Natt">
								</div>
							</div>
						</div>
					</div>
					</div>
				</div>				<div class="col-md-4 py-3">
					<div class="marquee" style="height: 800px; overflow: hidden"><div class="card m-1 mb-3">
						<div class="card-body text-left shadow-sm rounded-lg">
							<div style="color: #e94a15"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star" style="color: #ccc"></i></div>
							<h5 class="my-3">"Easy to use"</h5>
							<p class="text-muted">This broker is very easy to use and very easy to understand, I must say this is one of the best brokers I’ve used.</p>
							<hr/>
							<div class="d-flex justify-content-center">
								<div class="mr-auto my-auto">
									<h6 class="mb-0">Kamila</h6>
								</div>
								<div>
									<img src="{{ asset('frontpage/uploads/small/8861718405256.jpg') }}" style="width: 60px; height: 60px" class="rounded-circle" alt="Kamila">
								</div>
							</div>
						</div>
					</div><div class="card m-1 mb-3">
						<div class="card-body text-left shadow-sm rounded-lg">
							<div style="color: #e94a15"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star" style="color: #ccc"></i></div>
							<h5 class="my-3">"Amazing Platform"</h5>
							<p class="text-muted">This is an amazing platform which gives you opportunity to achieve your goals and become successful trader.</p>
							<hr/>
							<div class="d-flex justify-content-center">
								<div class="mr-auto my-auto">
									<h6 class="mb-0">CryptoPUNK</h6>
								</div>
								<div>
									<img src="{{ asset('frontpage/uploads/small/6251718405154.jpg') }}" style="width: 60px; height: 60px" class="rounded-circle" alt="CryptoPUNK">
								</div>
							</div>
						</div>
					</div><div class="card m-1 mb-3">
						<div class="card-body text-left shadow-sm rounded-lg">
							<div style="color: #e94a15"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
							<h5 class="my-3">"Highly Recommended"</h5>
							<p class="text-muted">Amazing broker, fast withdrawal and good excellent customer support. Highly recommended for pros and newbies</p>
							<hr/>
							<div class="d-flex justify-content-center">
								<div class="mr-auto my-auto">
									<h6 class="mb-0">Jeff Lobeck</h6>
								</div>
								<div>
									<img src="{{ asset('frontpage/uploads/small/4871718271112.jpg') }}" style="width: 60px; height: 60px" class="rounded-circle" alt="Jeff Lobeck">
								</div>
							</div>
						</div>
					</div><div class="card m-1 mb-3">
						<div class="card-body text-left shadow-sm rounded-lg">
							<div style="color: #e94a15"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
							<h5 class="my-3">"Amazing broker"</h5>
							<p class="text-muted">{{ config('app.name') }} is one of the best trading broker I’ve used since i started crypto trading. With their advanced trading software and my personal knowledge I’ve managed to earn over $6m</p>
							<hr/>
							<div class="d-flex justify-content-center">
								<div class="mr-auto my-auto">
									<h6 class="mb-0">Mitch Ross</h6>
								</div>
								<div>
									<img src="{{ asset('frontpage/uploads/small/3341718223475.jpg') }}" style="width: 60px; height: 60px" class="rounded-circle" alt="Mitch Ross">
								</div>
							</div>
						</div>
					</div>
					</div>
				</div>							</div>
		</div>
	</section>
</div>


<script>
$('.marquee').marquee({
	duration: 15000,
	gap: 20,
	delayBeforeStart: 0,
	direction: 'up',
	duplicated: true,
	startVisible: true
});
</script>

<style>
	.marquee {
		position: relative;
	}
	.marquee:before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 150px;
		display: block;
		background: linear-gradient(white 20%, transparent);
		z-index: 10;
	}
	.marquee:after{
		content: '';
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		height: 150px;
		display: block;
		background: linear-gradient(transparent, white 80%);
		z-index: 10;
	}
</style>

<div class="bg-dark text-white border-bottom" style="border-color: rgba(255, 255, 255, 0.2) !important">
  <div class="container py-4 text-center">
    <section class="py-5">
      <div class="py-4 section-title">
        <h2 class="mb-4 text-white">Start your trading journey now</h2>
        <p class="text-muted">It’s easy to get started. Sign up today to start trading with us.</p>
      </div>
      <a href="{{ url("/about") }}" class="btn btn-outline-light mr-2">Learn more</a>
      <a href="{{ route("register") }}" class="btn btn-primary mr-2">Get started</a>
    </section>
  </div>
</div>
<footer>
	<div class="container">
    @include('frontpage.partials.logo', ['class' => 'wow slideInLeft d-inline-block', 'height' => '44px'])<br />
    <div class="row mt-4 mb-5 wow slideInLeft">
      <div class="col-6 col-md-3 mb-3">
        <h5 class="font-weight-bold text-white mb-3">Products</h5>
        <ul class="list-unstyled">
          <li><a href="{{ route('user.dashboard') }}" class="text-light">Spot Trading</a></li>
          <li><a href="{{ route('user.dashboard') }}" class="text-light">Margin Trading</a></li>
          <li><a href="{{ route('user.dashboard') }}" class="text-light">Bot Trading</a></li>
          <li><a href="{{ route('user.dashboard') }}" class="text-light">Copy Trading</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-3 mb-3">
        <h5 class="font-weight-bold text-white mb-3">Markets</h5>
        <ul class="list-unstyled">
          <li><a href="{{ url("/forex") }}" class="text-light">Forex</a></li>
          <li><a href="{{ url("/crypto") }}" class="text-light">Crypto</a></li>
          <li><a href="{{ url("/stocks") }}" class="text-light">Stocks</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-3 mb-3">
        <h5 class="font-weight-bold text-white mb-3">Company</h5>
        <ul class="list-unstyled">
          <li><a href="{{ url("/about") }}" class="text-light">About us</a></li>
          <li><a href="{{ url("/privacy-policy") }}" class="text-light">Privacy</a></li>
          <li><a href="{{ url("/rules") }}" class="text-light">Terms of service</a></li>
        </ul>
      </div>
    </div>
   <div class='wow slideInUp'>
    <p>Trading carries substantial risk. You may lose more than your initial investment. Before trading, please read the relevant risk disclosure statements.</p>
    <hr class="my-3 my-md-5" style="border-color: rgba(255, 255, 255, 0.2) !important">
</div>
    <div class="gtranslate_wrapper mx-auto d-flex justify-content-center"></div>
<script>window.gtranslateSettings = {"default_language":"en","detect_browser_language":true,"wrapper_selector":".gtranslate_wrapper","flag_size":24,"switcher_horizontal_position":"inline","alt_flags":{"en":"usa"}}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/dwf.js" defer></script>
@if(!empty($livechatWidgetCode))
{!! $livechatWidgetCode !!}
@endif
    <p class="mb-0 mt-md-4 wow slideInLeft text-uppercase" >© 2016 - 2026 {{ config('app.name') }}. All rights reserved.</p>
  </div>

</footer>

<div id="google_translate_element" style="display:none"></div>
<script type="text/javascript">

function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: "en"}, 'google_translate_element');
}

function changeLanguageByButtonClick() {
  var language = document.getElementById("language").value;
  var selectField = document.querySelector("#google_translate_element select");
  for(var i=0; i < selectField.children.length; i++){
    var option = selectField.children[i];
    // find desired langauge and change the former language of the hidden selection-field 
    if(option.value==language){
       selectField.selectedIndex = i;
       // trigger change event afterwards to make google-lib translate this side
       selectField.dispatchEvent(new Event('change'));
       break;
    }
  }
}

function changeLanguageByButtonClick2() {
  var language = document.getElementById("language2").value;
  var selectField = document.querySelector("#google_translate_element select");
  for(var i=0; i < selectField.children.length; i++){
    var option = selectField.children[i];
    // find desired langauge and change the former language of the hidden selection-field 
    if(option.value==language){
       selectField.selectedIndex = i;
       // trigger change event afterwards to make google-lib translate this side
       selectField.dispatchEvent(new Event('change'));
       break;
    }
  }
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.js" integrity="sha512-Rd5Gf5A6chsunOJte+gKWyECMqkG8MgBYD1u80LOOJBfl6ka9CtatRrD4P0P5Q5V/z/ecvOCSYC8tLoWNrCpPg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$( document ).ready(function() { 
  new WOW().init();
});
</script>

</body>

</html>
