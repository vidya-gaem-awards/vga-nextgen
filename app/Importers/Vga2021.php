<?php

namespace App\Importers;

use Illuminate\Support\Facades\DB;

class Vga2021 extends Vga2020
{
    protected function year(): string
    {
        return '2021';
    }
}
