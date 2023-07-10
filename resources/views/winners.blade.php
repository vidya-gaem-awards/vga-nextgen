@extends('base.standard')

@section('content')
    <h1 class="page-header board-header mb-4">Winners of the {{ $show->name }}</h1>
    @foreach ($show->awards->sortBy('order')->where('enabled') as $award)
        <div class="mb-4">
            <h2>{{ $award->name }}</h2>
            @foreach ($award->nominees->sortBy('result') as $nominee)
                <div>{{ $nominee->result }}. {{ $nominee->name }}</div>
            @endforeach
        </div>
    @endforeach
@endsection
