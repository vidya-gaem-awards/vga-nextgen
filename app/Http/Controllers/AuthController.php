<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function loginDiscord(Request $request): RedirectResponse
    {
        Redirect::setIntendedUrl($request->get('redirect'));
        return Socialite::driver('discord')->redirect();
    }

    public function callbackDiscord()
    {
        $socialiteUser = Socialite::driver('discord')->user();

        $user = User::where(['discord_id' => $socialiteUser->id])->first();

        if (!$user) {
            Session::put('socialiteUser', $socialiteUser);
            return redirect()->route('login.discord.first-time');
        }

        if ($user->primary_connection === 'discord' || !$user->steam_id) {
            $user->name = $socialiteUser->user['global_name'];
            $user->avatar = $socialiteUser->avatar;
        }

        $user->last_login = new CarbonImmutable();
        $user->save();
        Auth::login($user, remember: true);

        return redirect()->intended();
    }

    public function discordFirstTime(): View
    {
        if (Auth::user()) {
            throw new \Exception('Already logged in. Cannot perform first time setup.');
        }

        $socialiteUser = Session::get('socialiteUser');

        return view('auth.discord-first-time', [
            'socialiteUser' => $socialiteUser,
        ]);
    }

    public function discordFirstTimeSubmit(Request $request): RedirectResponse
    {
        if ($request->input('action') === 'new') {
            $socialiteUser = Session::get('socialiteUser');

            $user = User::where(['discord_id' => $socialiteUser->id])->first();
            if ($user) {
                throw new \Exception('User already exists.');
            }

            $user                       = new User();
            $user->discord_id           = $socialiteUser->id;
            $user->name                 = $socialiteUser->user['global_name'];
            $user->avatar               = $socialiteUser->avatar;
            $user->first_login          = new CarbonImmutable();
            $user->last_login           = new CarbonImmutable();
            $user->primary_connection   = 'discord';
            $user->save();

            Auth::login($user, remember: true);

            return redirect()->intended();
        }

        return Socialite::driver('steam')
            ->with(['redirect_uri' => route('login.discord.first-time.callback')])
            ->redirect();
    }

    public function discordFirstTimeCallback(): RedirectResponse
    {
        $response = $this->callbackSteam();

        $user = Auth::user();
        $user->discord_id = Session::get('socialiteUser')->id;
        $user->primary_connection = 'steam';
        $user->save();

        return $response;
    }

    public function loginSteam(Request $request): RedirectResponse
    {
        Redirect::setIntendedUrl($request->get('redirect'));
        return Socialite::driver('steam')->redirect();
    }

    public function callbackSteam(): RedirectResponse
    {
        $socialiteUser = Socialite::driver('steam')->user();

        $user = User::where(['steam_id' => $socialiteUser->id])->first();

        if (!$user) {
            $user = new User();
            $user->first_login = new CarbonImmutable();
            $user->steam_id = $socialiteUser->id;
        }

        if ($user->primary_connection === 'steam' || !$user->discord_id) {
            $user->name = $socialiteUser->nickname;
            $user->avatar = $socialiteUser->avatar;
        }

        $user->last_login = new CarbonImmutable();
        $user->save();
        Auth::login($user, remember: true);

        return redirect()->intended();
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->get('redirect')
            ? redirect($request->get('redirect'))
            : redirect()->route('shows');
    }
}
