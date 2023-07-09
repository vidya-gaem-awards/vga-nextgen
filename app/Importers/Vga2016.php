<?php

namespace App\Importers;

use App\Models\Award;
use Illuminate\Support\Facades\DB;

class Vga2016 extends Vga2015
{
    protected function year(): string
    {
        return '2016';
    }

    public function awards()
    {
        $awards = $this->query('select * from awards');

        foreach ($awards as $original) {
            Award::updateOrCreate(
                [
                    'show_id' => $this->show->id,
                    'slug' => $original['id']
                ],
                [
                    'name' => $original['name'],
                    'subtitle' => $original['subtitle'],
                    'order' => $original['order'],
                    'enabled' => $original['enabled'],
                ]
            );
        }
    }
}
