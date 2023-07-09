<?php

namespace App\Importers;

use Illuminate\Support\Facades\DB;

class Vga2019 extends Vga2018
{
    protected function year(): string
    {
        return '2019';
    }
}
