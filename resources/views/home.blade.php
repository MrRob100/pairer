@extends('layouts.app')

@section('content')
<div class="container">

    <!-- TradingView Widget BEGIN -->
{{--    <div class="tradingview-widget-container">--}}
{{--        <div id="tradingview_50835"></div>--}}
{{--        <div class="tradingview-widget-copyright"><a href="https://uk.tradingview.com/symbols/XMRBNB/?exchange=BINANCE" rel="noopener" target="_blank"><span class="blue-text">XMRBNB Chart</span></a> by TradingView</div>--}}
{{--        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>--}}
{{--        <script type="text/javascript">--}}
{{--            new TradingView.widget(--}}
{{--                {--}}
{{--                    "width": 980,--}}
{{--                    "height": 610,--}}
{{--                    "symbol": "BINANCE:XMRBNB",--}}
{{--                    "interval": "60",--}}
{{--                    "timezone": "Etc/UTC",--}}
{{--                    "theme": "light",--}}
{{--                    "style": "1",--}}
{{--                    "locale": "uk",--}}
{{--                    "toolbar_bg": "#f1f3f6",--}}
{{--                    "enable_publishing": false,--}}
{{--                    "hide_top_toolbar": true,--}}
{{--                    "hide_side_toolbar": false,--}}
{{--                    "container_id": "tradingview_50835"--}}
{{--                }--}}
{{--            );--}}
{{--        </script>--}}
{{--    </div>--}}
    <!-- TradingView Widget END -->

    <br>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="post" action="{{ route('target.set') }}">
                @method('put')
                @csrf
                <input name="upper" type="number" step="0.01" value="{{ $target->upper }}" placeholder="upper">
                <input name="lower" type="number" step="0.01" value="{{ $target->lower }}" placeholder="lower">
                <input name="target" type="hidden" value="{{ $target->id }}">
                <button type="submit">Set</button>
            </form>
        </div>

        <a href="/check" target="_blank">Manually check</a>

        <br>

        <table class="table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Message</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log['timestamp'] }}</td>
                <td>{{ $log['type'] }}</td>
                <td>{{ $log['message'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection
