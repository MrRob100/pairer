@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-12">
                <p class="text-light">Pair Balances</p>
                <table class="table table-striped text-light">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>s1</th>
                            <th>balance_s1</th>
                            <th>balance_s1_usd</th>
                            <th>price_at_trade_s1</th>
                            <th>s2</th>
                            <th>balance_s2</th>
                            <th>balance_s2_usd</th>
                            <th>price_at_trade_s2</th>
                            <th>created_at</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($pairBalances as $pairBalance)
                        <tr>
                            <td>{{ $pairBalance->id }}</td>
                            <td>{{ $pairBalance->s1 }}</td>
                            <td>{{ $pairBalance->balance_s1 }}</td>
                            <td>{{ $pairBalance->balance_s1_usd }}</td>
                            <td>{{ $pairBalance->price_at_trade_s1 }}</td>
                            <td>{{ $pairBalance->s2 }}</td>
                            <td>{{ $pairBalance->balance_s2 }}</td>
                            <td>{{ $pairBalance->balance_s2_usd }}</td>
                            <td>{{ $pairBalance->price_at_trade_s2 }}</td>
                            <td>{{ $pairBalance->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <br>
        <br>
        <div class="row">
            <div class="col-12">
                <p class="text-light">Open Orders</p>
                <table class="table table-striped text-light">
                    <thead>
                        <th>id</th>
                        <th>orderId</th>
                        <th>pair_balance_id</th>
                        <th>fill_time</th>
                        <th>status</th>
                        <th>side</th>
                        <th>pure_price_at_trade</th>
                        <th>created_at</th>
                    </thead>
                    <tbody>
                    @foreach($openOrders as $openOrder)
                        <tr>
                            <td>{{ $openOrder->id }}</td>
                            <td>{{ $openOrder->orderId }}</td>
                            <td>{{ $openOrder->pair_balance_id }}</td>
                            <td>{{ $openOrder->fill_time }}</td>
                            <td>{{ $openOrder->status }}</td>
                            <td>{{ $openOrder->side }}</td>
                            <td>{{ $openOrder->pure_price_at_trade }}</td>
                            <td>{{ $openOrder->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
