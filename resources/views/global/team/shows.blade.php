@extends('base.standard')

@section('content')
    <h1 class="page-header board-header mb-4">Shows</h1>

    @if (Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Year</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($shows as $show)
            <tr>
                <td>{{ $show->name }}</td>
                <td>
                    <a href="{{ route('show', ['show' => $show]) }}" class="btn btn-outline-dark">
                        View
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if ($createCurrentYear)
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info">
            <p>
                You can create a new show for {{ $createCurrentYear }}.
            </p>
            <form method="post" action="{{ route('team.shows.create') }}">
                @csrf
                <input type="hidden" name="year" value="{{ $createCurrentYear }}">
                <button type="submit" class="btn btn-outline-dark">
                    Create Show
                </button>
            </form>
        </div>
    @endif

@endsection
