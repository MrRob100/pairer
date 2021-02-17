@extends('layouts.app')

@section('content')
    <pair-page cr="{{ route('chart.data') }}" br="{{ route('balance') }}"></pair-page>
@endsection
