<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Role;

class AccountController extends Controller
{
    public function account()
    {
        $user = Auth::user();

        $shows = Show::orderBy('year', 'desc')->get();

        $permissions = [];

        foreach ($shows as $show) {
            $roles = Role::where('scope', $show->id)->get()->keyBy('name');
            BouncerFacade::scope()->onceTo($show->id, function () use ($user, &$permissions, $show, $roles) {
                $permissions[$show->year] = [
                    'roles' => $user->getRoles()->map(fn ($role) => $roles[$role]),
                    'abilities' => $user->getAbilities(),
                ];
            });
        }

        $shows = $shows->filter(fn () => $permissions[$show->year]['abilities']);

        return view('account', [
            'shows' => $shows,
            'permissions' => $permissions,
        ]);
    }

    public function post(Request $request)
    {
        $request->validate([
            'yes' => 'required'
        ]);
    }
}
