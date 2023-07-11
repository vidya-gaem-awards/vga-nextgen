<?php

namespace App\Importers;

use App\Models\Nominee;
use Illuminate\Support\Facades\DB;

class Vga2019 extends Vga2018
{
    protected function year(): string
    {
        return '2019';
    }

    public function files(): void
    {
        $files = $this->query(<<<SQL
            SELECT * FROM nominees
            JOIN files ON files.id = nominees.image_id
        SQL)->merge($this->query(<<<SQL
            SELECT *, awards.id as award_id FROM awards
            JOIN files ON files.id = awards.winner_image_id
        SQL));

        foreach ($files as $row) {
            $url = sprintf(
                "https://%d.vidyagaemawards.com/uploads/%s/%s.%s",
                $this->show->year,
                $row['subdirectory'],
                $row['filename'],
                $row['extension'],
            );

            if ($row['entity'] === 'Nominee' || $row['entity'] === 'Nominee.image') {
                $nominee = $this->show->nominees
                    ->where('slug', $row['short_name'])
                    ->where('award.slug', $row['award_id'])
                    ->first();

                if ($nominee && $nominee->getMedia('voting-image')->isEmpty()) {
                    $nominee->addMediaFromUrl($url)->toMediaCollection('voting-image');
                }

                continue;
            }

            if ($row['entity'] === 'Award' || $row['entity'] === 'Award.winnerImage') {
                $award = $this->show->awards
                    ->where('slug', $row['award_id'])
                    ->first();

                if ($award && $award->getMedia('winner-image')->isEmpty()) {
                    $award->addMediaFromUrl($url)->toMediaCollection('winner-image');
                }

                continue;
            }

            // Not yet supported:
            // - FantasyUser
            // - FantasyUser.avatar
            // - InventoryItem.image
            // - InventoryItem.music_file
            // - Advertisement.image
            // - LootboxItem.image
            // - LootboxItem.musicFile
        }
    }
}
