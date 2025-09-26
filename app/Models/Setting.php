<?php

namespace App\Models;

use App\Models\Shipyard\Setting as ShipyardSetting;

class Setting extends ShipyardSetting
{
    public static function fields(): array
    {
        /**
         * * hierarchical structure of the page *
         * grouped by sections (title, subtitle, icon, identifier)
         * each section contains fields (name, label, hint, icon)
         */
        return [

        ];
    }

}