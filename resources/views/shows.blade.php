@extends('base.standard')

@section('content')
    <h1 class="page-header board-header mb-4">The Vidya Gaem Awards</h1>

    <div class="text-center">
        @foreach ($shows as $show)
            <div class="my-2"><a href="{{ route('show', ['show' => $show]) }}">{{ $show->name }}</a></div>
        @endforeach
    </div>
@endsection
