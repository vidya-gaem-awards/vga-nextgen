<?php

namespace App\Importers;

use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Exceptions\UnreachableUrl;

class Vga2014 extends Vga2013
{
    protected function year(): string
    {
        return '2014';
    }

    public function files(): void
    {
        $nominees = $this->query('SELECT * FROM nominees');

        foreach ($nominees as $row) {
            $nominee = $this->show->nominees
                ->where('slug', $row['nominee_id'])
                ->where('award.slug', $row['category_id'])
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

        $awards = $this->query('SELECT * FROM categories WHERE enabled = 1');

        foreach ($awards as $row) {
            $award = $this->show->awards
                ->where('slug', $row['id'])
                ->first();

            if (!$award || $award->winnerImage) {
                continue;
            }

            $url = sprintf(
                "https://%d.vidyagaemawards.com/assets/winners/01189998819991197253/%s.png",
                $this->show->year,
                $row['id']
            );

            $award->addMediaFromUrl($url)->toMediaCollection('winner-image');
        }
    }
}
