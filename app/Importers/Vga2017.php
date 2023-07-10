<?php

namespace App\Importers;

use App\Models\Award;
use App\Models\Nominee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability;

class Vga2017 extends Vga2016
{
    protected function year(): string
    {
        return '2017';
    }

    public function nominees(): void
    {
        $nominees = $this->query('SELECT * FROM nominees');

        foreach ($nominees as $original) {
            Nominee::updateOrCreate(
                [
                    'award_id' => Award::where('show_id', $this->show->id)
                        ->where('slug', $original['award_id'])
                        ->first()
                        ->id,
                    'slug' => $original['short_name']
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

    public function results(): void
    {
        $results = $this->processResult(
            $this->db->table('result_cache')->where('filter', static::$resultFilter)->get()
        );

        foreach ($results as $row) {
            $results = json_decode($row['results'], true);

            foreach ($results as $placement => $slug) {
                $award = Award::where('show_id', $this->show->id)
                    ->where('slug', $row['award_id'])
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

    public function users(): void
    {
        $users = $this->query(<<<SQL
            SELECT users.*, GROUP_CONCAT(permissionID) as permissions FROM users
            LEFT JOIN user_permissions ON users.id = user_permissions.userID
            WHERE special = 1
            GROUP BY users.id
        SQL);

        foreach ($users as $row) {
            $user = User::updateOrCreate(
                [
                    'steam_id' => $row['username'],
                ],
                [
                    'name' => $row['name'],
                    'avatar' => $row['avatar'],
                ]
            );

            BouncerFacade::scope()->onceTo($this->show->id, function () use ($user, $row) {
                $permissions = explode(',', $row['permissions']);
                foreach ($permissions as $permission) {
                    if (str_starts_with(strtolower($permission), 'level')) {
                        BouncerFacade::assign($permission)->to($user);
                    } else {
                        BouncerFacade::allow($user)->to($permission);
                    }
                }
            });
        }
    }
}
