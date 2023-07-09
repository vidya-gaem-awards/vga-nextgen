<?php

namespace App\Importers;

use Illuminate\Support\Facades\DB;

class Vga2014 extends Vga2013
{
    protected function year(): string
    {
        return '2014';
    }
}
