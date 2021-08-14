@extends('layouts.app')

@section('content')
    <pair-page
        cr="{{ route('chart.data') }}"
        br="{{ route('balance') }}"
        pr="{{ route('price') }}"
        tr="{{ route('transfer') }}"
        spr="{{ route('saved.pairs') }}"
        cpr="{{ route('create.pair') }}"
        dlr="{{ route('delete.pair') }}"
        bdr="{{ route('brecord') }}"
        dr="{{ route('download') }}"
        rand="{{ route('randomize') }}"
        dp="{{ route('trash') }}"
        rr="{{ route('inputs.create') }}"
    >
    </pair-page>
{{--    @include('box')--}}
@endsection
