@extends('base.standard')

@section('content')
    <h1 class="page-header board-header mb-4">Your Account</h1>

    <h2>Permissions</h2>

    @foreach ($shows as $show)
        <h3 class="mt-4">{{ $show->name }}</h3>
        <dl class="row">
            @foreach ($permissions[$show->year]['roles'] as $role)
                <dt class="col-sm-3 text-primary">
                    {{ $role->name }}
                </dt>
                <dd class="col-sm-9 text-primary">
                    {{ $role->description ?: '[no description]' }}&nbsp;
                </dd>
            @endforeach
            @foreach ($permissions[$show->year]['abilities'] as $ability)
                <dt class="col-sm-3">
                    {{ $ability->name }}
                </dt>
                <dd class="col-sm-9">
                    {{ $ability->description ?: '[no description]' }} &nbsp;
                </dd>
            @endforeach
        </dl>
    @endforeach

    <form method="post">
        @csrf
        <input type="radio" id="status_1" name="status" value=1 class="form-check-input" required {{ old("status") === "1" ? 'checked' : '' }}>
        <input type="radio" id="status_2" name="status" value=2 class="form-check-input" required {{ old("status") === "2" ? 'checked' : '' }}>
        <input type="submit" />
    </form>
@endsection
