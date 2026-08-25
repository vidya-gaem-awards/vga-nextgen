@section('winnerIteration')
    <div class="col-3">
        @if($nominee->votingImage)
            <img src="{{ $nominee->votingImage->getUrl() }}" style="height: 40px;" class="mb-2">
        @endif
        <div>{{ $nominee->result }}. {{ $nominee->name }}</div>
    </div>
@endsection
