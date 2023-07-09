<?php

namespace App\Importers;

use App\Models\Award;
use App\Models\Nominee;
use Illuminate\Support\Facades\DB;

class Vga2012 extends Vga2011
{
    protected static ?string $resultFilter = '05combined2';

    protected function year(): string
    {
        return '2012';
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
                    'enabled' => $original['enabled'],
                ]
            );
        }
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
                ]
            );
        }

        $this->results();
    }

    public function results(): void
    {
        $results = $this->processResult(
            $this->db->table('winner_cache')->where('Filter', static::$resultFilter)->get()
        );

        foreach ($results as $row) {
            $results = json_decode($row['results'], true);

            foreach ($results as $placement => $slug) {
                $award = Award::where('show_id', $this->show->id)
                    ->where('slug', $row['category_id'])
                    ->first();

                $nominee = Nominee::where('award_id', $award->id)
                    ->where('slug', $slug)
                    ->first();

                $nominee->update([
                    'result' => $placement
                ]);
            }
        }
    }
}
