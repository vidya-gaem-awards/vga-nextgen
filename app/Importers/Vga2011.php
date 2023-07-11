<?php

namespace App\Importers;

use App\Models\Award;
use App\Models\Nominee;
use App\Models\Show;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Silber\Bouncer\BouncerFacade;
use Spatie\MediaLibrary\MediaCollections\Exceptions\UnreachableUrl;

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

    public function permissions(): void
    {
        BouncerFacade::scope()->onceTo($this->show->id, function () {
            $admin = BouncerFacade::role()->firstOrCreate(['name' => 'admin']);

            $abilities = ['feedback', 'results', 'secretclub', 'special'];
            foreach ($abilities as $ability) {
                $_ability = BouncerFacade::ability()->firstOrCreate(['name' => $ability]);
                BouncerFacade::allow($admin)->to($_ability);
            }
        });
    }

    public function users(): void
    {
        $users = $this->query(<<<SQL
            SELECT users.*, GROUP_CONCAT(Privilege) as Privileges FROM users
            JOIN user_rights
                ON users.SteamID = user_rights.UserID
            GROUP BY users.SteamID
        SQL);

        foreach ($users as $row) {
            $user = User::updateOrCreate(
                [
                    'steam_id' => $row['steam_id'],
                ],
                [
                    'name' => $row['name'],
                ]
            );

            BouncerFacade::scope()->onceTo($this->show->id, function () use ($user, $row) {
                $privileges = explode(',', $row['privileges']);
                foreach ($privileges as $privilege) {
                    if ($privilege === 'admin') {
                        BouncerFacade::assign($privilege)->to($user);
                    } else {
                        BouncerFacade::allow($user)->to($privilege);
                    }
                }
            });
        }
    }

    public function files(): void
    {
        $nominees = $this->query(<<<SQL
            SELECT nominees.*, categories.type
            FROM nominees
            JOIN categories ON nominees.CategoryID = categories.ID
        SQL);

        foreach ($nominees as $row) {
            $nominee = $this->show->nominees
                ->where('slug', $row['nominee'])
                ->where('award.slug', $row['category_id'])
                ->first();

            if (!$nominee || $nominee->votingImage) {
                continue;
            }

            // The 2011 site had this annoying thing where an image could be in one of three
            // locations, and the only way to tell which one was used is to check if the
            // file exists.
            //
            // Luckily, the third option was never used, so we only have to check two.
            $urls = [
                sprintf(
                    "https://%d.vidyagaemawards.com/images/nominees/%s-%s.png",
                    $this->show->year,
                    mb_strtolower($row['category_id']),
                    $row['nominee'],
                ),
                sprintf(
                    "https://%d.vidyagaemawards.com/images/nominees/%s-%s.png",
                    $this->show->year,
                    $row['type'],
                    $row['nominee'],
                ),
            ];

            foreach ($urls as $url) {
                try {
                    $nominee->addMediaFromUrl($url)->toMediaCollection('voting-image');
                } catch (UnreachableUrl) {
                    // ignore if file doesn't exist
                }
            }
        }
    }
}
