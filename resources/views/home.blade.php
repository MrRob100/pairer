@extends('layouts.app')

@section('content')
    <pair-page
        cr="{{ route('chart.data') }}"
        br="{{ route('balance') }}"
        pr="{{ route('price') }}"
        tr="{{ route('transfer') }}"
    >
    </pair-page>
@endsection
