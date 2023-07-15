<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\View\View;

class ShowController extends Controller
{
    public function shows(): View
    {
        return view('shows', [
            'shows' => Show::get()
        ]);
    }

    public function view(Show $show): View
    {
        return view('show', [
            'selectedShow' => $show,
        ]);
    }
}
