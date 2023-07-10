@extends('base.standard')

@pushonce('css')
    <style>
        .jumbotron {
            color: #AE1216;
            font-family: Tahoma, sans-serif;
            padding-bottom: 2rem;
        }
        .jumbotron h1 {
            font-weight: 700;
            font-size: 50px !important;
        }
        .jumbotron p {
            font-size: 25px !important;
        }
        .masthead {
            margin-bottom: 0;
        }
    </style>
@endpushonce

@section('content')
    <header class="jumbotron masthead text-center" style="background: none;">
        <h1>{{ $show->name }}</h1>
        <p>&gt;implying you're opinion is worth shit</p>
    </header>
@endsection
