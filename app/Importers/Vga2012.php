<?php

namespace App\Importers;

use App\Models\Award;
use App\Models\Nominee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

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

    public function permissions(): void
    {
        BouncerFacade::scope()->onceTo($this->show->id, function () {
            $permissions = $this->query('SELECT * FROM user_rights');

            foreach ($permissions as $row) {
                $role = BouncerFacade::role()->firstOrCreate([
                    'name' => $row['group_name'],
                ]);
                preg_match('/\d+$/', $row['group_name'], $matches);
                $role->level = (int)$matches[0];
                $role->save();

                $ability = BouncerFacade::ability()->firstOrCreate([
                    'name' => $row['can_do'],
                ]);
                $ability->description = $row['description'];
                $ability->level = $role->level;
                $ability->save();
            }

            for ($level = 1; $level <= 5; $level++) {
                $role = BouncerFacade::role()->where([
                    'level' => $level
                ])->first();

                Ability::where('level', '<=', $level)
                    ->each(fn ($ability) => $role->allow($ability));
            }
        });
    }

    public function users(): void
    {
        $users = $this->query(<<<SQL
            SELECT users.*, GROUP_CONCAT(GroupName) as permissions FROM users
            LEFT JOIN user_groups ON users.SteamID = user_groups.UserID
            WHERE special = 1
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
                $permissions = explode(',', $row['permissions']);
                foreach ($permissions as $permission) {
                    if (str_starts_with($permission, 'level')) {
                        BouncerFacade::assign($permission)->to($user);
                    } else {
                        BouncerFacade::allow($user)->to($permission);
                    }
                }
            });
        }
    }
}
