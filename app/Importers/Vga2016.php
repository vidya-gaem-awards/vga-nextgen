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

    public function files(): void
    {
        $nominees = $this->query('SELECT * FROM nominees');

        foreach ($nominees as $row) {
            $nominee = $this->show->nominees
                ->where('slug', $row['short_name'])
                ->where('award.slug', $this->year() === '2016'
                    ? $row['category_id']
                    : $row['award_id']
                )
                ->first();

            if (!$nominee || $nominee->votingImage) {
                continue;
            }

            $url = sprintf(
                "https://%d.vidyagaemawards.com/%s",
                $this->show->year,
                ltrim($row['image'], '/'),
            );

            $nominee->addMediaFromUrl($url)->toMediaCollection('voting-image');
        }

        $awards = $this->query('SELECT * FROM awards WHERE enabled = 1');

        foreach ($awards as $row) {
            $award = $this->show->awards
                ->where('slug', $row['id'])
                ->first();

            if (!$award || $award->winnerImage) {
                continue;
            }

            $url = sprintf(
                "https://%d.vidyagaemawards.com/%s",
                $this->show->year,
                ltrim($row['winner_image'], '/')
            );

            $award->addMediaFromUrl($url)->toMediaCollection('winner-image');
        }
    }
}
