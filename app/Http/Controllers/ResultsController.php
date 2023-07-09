<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    public function winners(Show $show)
    {
        return view('winners', ['show' => $show]);
    }
}
