<?php

namespace App\Importers;

use App\Models\Award;
use App\Models\Nominee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class Vga2015 extends Vga2014
{
    protected static ?string $resultFilter = '08-4chan-or-null-with-voting-code';

    protected function year(): string
    {
        return '2015';
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
            $this->db->table('result_cache')->where('Filter', static::$resultFilter)->get()
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
            $permissions = $this->query(<<<SQL
                SELECT *
                FROM permissions AS p
                LEFT JOIN permission_children AS pc
                    ON p.id = pc.childID
            SQL);

            foreach ($permissions as $row) {
                if (str_starts_with(mb_strtolower($row['id']), 'level')) {
                    $role = BouncerFacade::role()->firstOrCreate([
                        'name' => $row['id'],
                    ]);
                    preg_match('/\d+$/', $row['id'], $matches);
                    $role->level = (int)$matches[0];
                    $role->description = $row['description'];
                    $role->save();

                    continue;
                }

                $ability = BouncerFacade::ability()->firstOrCreate([
                    'name' => $row['id'],
                ]);
                $ability->description = $row['description'];
                if ($row['parent_id']) {
                    preg_match('/\d+$/', $row['parent_id'], $matches);
                }
                $ability->level = $matches[0];
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
            SELECT users.*, GROUP_CONCAT(permissionID) as permissions FROM users
            LEFT JOIN user_permissions ON users.steamID = user_permissions.userID
            WHERE special = 1
            GROUP BY users.steamID
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
