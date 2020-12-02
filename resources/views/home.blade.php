@extends('layouts.app')

@section('content')
<div class="container">

    <!-- TradingView Widget BEGIN -->
    <div class="tradingview-widget-container">
        <div id="tradingview_50835"></div>
        <div class="tradingview-widget-copyright"><a href="https://uk.tradingview.com/symbols/XMRBNB/?exchange=BINANCE" rel="noopener" target="_blank"><span class="blue-text">XMRBNB Chart</span></a> by TradingView</div>
        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
        <script type="text/javascript">
            new TradingView.widget(
                {
                    "width": 980,
                    "height": 610,
                    "symbol": "BINANCE:XMRBNB",
                    "interval": "60",
                    "timezone": "Etc/UTC",
                    "theme": "light",
                    "style": "1",
                    "locale": "uk",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "hide_top_toolbar": true,
                    "hide_side_toolbar": false,
                    "container_id": "tradingview_50835"
                }
            );
        </script>
    </div>
    <!-- TradingView Widget END -->

    <br>

    <div class="row justify-content-center">
        <div class="col-md-8">

        </div>
    </div>
</div>
@endsection
