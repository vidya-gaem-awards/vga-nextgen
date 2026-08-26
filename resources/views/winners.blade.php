@extends('base.standard')

@include('overrides.2021.winners');

@section('content')
    <h1 class="page-header board-header mb-4">Winners of the {{ $selectedShow->name }}</h1>
    @foreach ($selectedShow->awards->sortBy('order')->where('enabled') as $award)
        <div class="mb-4">
            <h2 class="text-center">{{ $award->name }}</h2>
            <h4 class="text-center">{{ $award->subtitle }}</h4>
            <div class="row my-4">
                <div class="col-4">
                    @if ($award->winnerImage)
                        <img src="{{ $award->winnerImage->getUrl() }}" style="max-width: 100%;">
                    @endif
                </div>
                <div class="col-8">
                    <div class="row">
                    @foreach ($award->nominees->sortBy('result') as $nominee)

                        @yield('winnerIteration')
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
