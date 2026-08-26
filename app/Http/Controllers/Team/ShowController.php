<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Show;

class ShowController extends Controller
{
    public function index()
    {
        $shows = Show::all();

        $createCurrentYear = Show::where('year', date('Y'))->exists() ? false : date('Y');

        return view('global.team.shows', [
            'shows' => $shows,
            'createCurrentYear' => $createCurrentYear,
        ]);
    }

    public function create()
    {
        // validate request: year must be current year and show must not exist
        $this->validate(request(), [
            'year' => 'required|unique:shows,year|in:' . date('Y'),
        ]);

        $year = request('year');

        Show::create([
            'id' => $year,
            'year' => $year,
            'name' => $year . ' Vidya Gaem Awards'
        ]);

        return redirect()
            ->route('team.shows')
            ->with('success', 'Show created successfully.');
    }
}
