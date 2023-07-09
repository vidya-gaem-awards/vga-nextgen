<?php

namespace App\Importers;

use Illuminate\Support\Facades\DB;

class Vga2020 extends Vga2019
{
    protected function year(): string
    {
        return '2020';
    }
}
