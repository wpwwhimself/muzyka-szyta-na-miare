<?php

namespace App\Scaffolds;

use App\Scaffolds\Shipyard\Role as ShipyardRole;

class Role extends ShipyardRole
{
    protected static function items(): array
    {
        return [
            [
                "name" => "client",
                "icon" => "account-tie",
                "description" => "Ma dostęp do swoich zapytań i zleceń",
            ],
        ];
    }
}
