<?php

namespace App\Importers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade;

class Vga2018 extends Vga2017
{
    protected function year(): string
    {
        return '2018';
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
                    'steam_id' => $row['steam_id'],
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
