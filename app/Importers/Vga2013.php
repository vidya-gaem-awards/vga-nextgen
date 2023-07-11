<?php

namespace App\Importers;

use App\Models\Award;
use App\Models\Nominee;
use Illuminate\Support\Facades\DB;

class Vga2013 extends Vga2012
{
    protected function year(): string
    {
        return '2013';
    }

    public function nominees(): void
    {
        $nominees = $this->query('SELECT * FROM nominees');

        foreach ($nominees as $original) {
            Nominee::updateOrCreate(
                [
                    'award_id' => Award::where('show_id', $this->show->id)
                        ->where('slug', $original['category_id'])
                        ->first()
                        ->id,
                    'slug' => $original['nominee_id']
                ],
                [
                    'name' => $original['name'],
                    'subtitle' => $original['subtitle'],
                    'flavour_text' => $original['flavor_text'],
                ]
            );
        }

        $this->results();
    }


}
