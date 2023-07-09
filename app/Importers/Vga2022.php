<?php

namespace App\Importers;

class Vga2022 extends Vga2021
{
    protected function year(): string
    {
        return '2022';
    }
}
