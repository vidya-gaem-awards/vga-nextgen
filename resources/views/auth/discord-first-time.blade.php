@extends('base.standard')

@section('content')
    <div class="row">
        <div class="col-6 offset-3">
            <div class="card bg-white">
                <div class="card-header">
                    <strong>First time login with Discord</strong>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 align-items-center">
                        <img class="profile-pic" src="{{ $socialiteUser->avatar }}" style='height: 40px;'>
                        <div style="line-height: 1.3;">
                            <div class="fw-semibold">{{ $socialiteUser->user['global_name'] }}</div>
                            <small class="opacity-50">{{ $socialiteUser->user['username'] }}</small>
                        </div>
                    </div>
                    <hr>
                    <p>
                        This is your first time logging into the /v/GA website with Discord.
                    </p>
                    <p>
                        If you've previously logged into the website before using Steam (in any year),
                        you can connect Discord to your existing account. Otherwise, you can create a new account.
                    </p>
                    <p class="mb-0">
                        Choose wisely, as it can't be changed later.
                    </p>
                </div>
                <form method="post" action="{{ route('login.discord.first-time.submit') }}">
                    <div class="card-footer d-flex gap-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark" name="action" value="existing">
                            <i class="fab fa-fw fa-steam"></i> Connect Existing Account
                        </button>
                        <button type="submit" class="btn btn-outline-dark" name="action" value="new">
                            Create New Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
