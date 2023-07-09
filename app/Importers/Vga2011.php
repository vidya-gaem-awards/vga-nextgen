<?php

namespace App\Importers;

use App\Models\Award;
use App\Models\Nominee;
use App\Models\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Vga2011 extends Importer
{
    protected function year(): string
    {
        return '2011';
    }

    public function awards()
    {
        $awards = $this->query('select * from categories');

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
                    'enabled' => $original['active'],
                ]
            );
        }
    }

    public function nominees(): void
    {
        $nominees = $this->query(<<<SQL
            SELECT nominees_all.*, nominees.*
            FROM nominees
            JOIN categories
                ON nominees.CategoryID = categories.ID
            JOIN nominees_all
                ON nominees.Nominee = nominees_all.ID
                AND categories.Type = nominees_all.Type
        SQL);

        foreach ($nominees as $original) {
            Nominee::updateOrCreate(
                [
                    'award_id' => Award::where('show_id', $this->show->id)
                        ->where('slug', $original['category_id'])
                        ->first()
                        ->id,
                    'slug' => $original['id']
                ],
                [
                    'name' => $original['name'],
                    'subtitle' => $original['extra_info'],
                    'result' => $original['ranking'] ?: null,
                ]
            );
        }
    }
}
