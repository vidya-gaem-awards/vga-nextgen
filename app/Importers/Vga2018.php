<?php

namespace App\Importers;

use Illuminate\Support\Facades\DB;

class Vga2018 extends Vga2017
{
    protected function year(): string
    {
        return '2018';
    }
}
