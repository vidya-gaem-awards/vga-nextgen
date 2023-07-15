@extends('base.standard')

@section('content')
    <h1 class="page-header board-header mb-4">Winners of the {{ $selectedShow->name }}</h1>
    @foreach ($selectedShow->awards->sortBy('order')->where('enabled') as $award)
        <div class="mb-4">
            <h2>{{ $award->name }}</h2>
            @if ($award->winnerImage)
                <img src="{{ $award->winnerImage->getUrl() }}">
            @endif
            @foreach ($award->nominees->sortBy('result') as $nominee)
                <div>
                    @if($nominee->votingImage)
                        <img src="{{ $nominee->votingImage->getUrl() }}" style="height: 20px;">
                    @endif
                    <div>{{ $nominee->result }}. {{ $nominee->name }}</div>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
