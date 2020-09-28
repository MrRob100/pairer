@extends('layouts.app')

@section('content')
<div class="container">

    <!-- TradingView Widget BEGIN -->
    <div class="tradingview-widget-container">
        <div id="tradingview_695cc"></div>
        <div class="tradingview-widget-copyright"><a href="https://uk.tradingview.com/symbols/NASDAQ-AAPL/" rel="noopener" target="_blank"><span class="blue-text">AAPL Chart</span></a> by TradingView</div>
        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
        <script type="text/javascript">
        new TradingView.widget(
        {
        "width": 980,
        "height": 610,
        "symbol": "kucoin:AMPLUSDT",
        "interval": "D",
        "timezone": "Etc/UTC",
        "theme": "light",
        "style": "1",
        "locale": "uk",
        "toolbar_bg": "#f1f3f6",
        "enable_publishing": false,
        "allow_symbol_change": true,
        "container_id": "tradingview_695cc"
        }
        );
        </script>
    </div>
    <!-- TradingView Widget END -->

    <div class="row justify-content-center">
        <div class="col-md-8">
            <order></order>
        </div>
    </div>
</div>
@endsection
