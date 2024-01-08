<?php

use App\Models\CalendarFreeDay;
use App\Models\Client;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Song;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * SETTING GETTER
 */
if(!function_exists("setting")){
    function setting($setting_name){
        return DB::table("settings")->where("setting_name", $setting_name)->value("value_str");
    }
}
if(!function_exists("QUEST_MINIMAL_PRICES")){
    function QUEST_MINIMAL_PRICES(){
        return array_combine(
            [1, 2, 3],
            explode(",", setting("quest_minimal_price"))
        );
    }
}

/**
 * "CONSTANTS"
 */
if(!function_exists("VETERAN_FROM")){
    function VETERAN_FROM(){
        return setting("veteran_from");
    }
}
if(!function_exists("CURRENT_PRICING")){
    function CURRENT_PRICING(){
        return setting("current_pricing");
    }
}
if(!function_exists("BEGINNING")){
    function BEGINNING(){
        return Carbon::parse('2023-03-13');
    }
}
if(!function_exists("INCOME_LIMIT")){
    function INCOME_LIMIT(){
        if(Carbon::parse("2023-07-01")->diffInDays(Carbon::today(), false) >= 0) return 2700;
        return 1745;
    }
}
if(!function_exists("OBSERVER_ERROR")){
    function OBSERVER_ERROR(){
        return "Jako obserwator nie możesz tego zrobić";
    }
}

/**
 * Converts user ID to string depicting, which kind of view it is supposed to see. Works in role-specific views (like dashboard)
 */
if(!function_exists("user_role")){
    function user_role(){
        $role = (is_archmage()) ? "archmage" : "client";
        return $role;
    }
}
if(!function_exists("is_archmage")){
    function is_archmage($user_id = null){
        return in_array($user_id ?? Auth::id(), [0, 1], true);
    }
}

/**
 * Censor data for showcase account
 */
if(!function_exists("_c_")){
    function _c_($data){
        return (Auth::id() === 0) ? preg_replace("/(\d)/", "⁎", $data) : $data;
    }
}
if(!function_exists("_ct_")){
    function _ct_($data){
        return (Auth::id() === 0) ? ($data ? preg_replace("/[\wąćęłńóśźżĄĆĘŁŃÓŚŹŻ]/", "⁎", $data) : null) : $data;
    }
}

/**
 * If you wish to add a new quest/song, what's its ID gonna be?
 */
if(!function_exists("next_quest_id")){
    function next_quest_id($quest_type_id){
        $letter = (is_numeric($quest_type_id)) ? QuestType::find($quest_type_id)->code : $quest_type_id;
        $newest_id = Quest::where("id", "like", "$letter%")->orderBy("id", "desc")->value("id");
        if(!$newest_id || date("y") != substr($newest_id, 1, 2)){
            return $letter . date("y") . "-00";
        }
        $dash_pos = strpos($newest_id, "-");
        $newest_id_last = substr($newest_id, $dash_pos + 1);
        return $letter . date("y") . "-" . to_base36(from_base36($newest_id_last) + 1);
    }
}
if(!function_exists("next_song_id")){
    function next_song_id($quest_type_id){
        $letter = (is_numeric($quest_type_id)) ? QuestType::find($quest_type_id)->code : $quest_type_id;
        $newest_id = Song::where("id", "like", "$letter%")->orderBy("id", "desc")->value("id");
        if(!$newest_id){
            return $letter . "000";
        }
        $newest_id_last = substr($newest_id, 1);
        return $letter . to_base36(from_base36($newest_id_last) + 1, 3);
    }
}
if(!function_exists("to_base36")){
    function to_base36($number, $pad = 2){
        $code = "";
        if($number == 0) $code = $number;
        while($number > 0){
            $remainder = $number % 36;
            if($remainder <= 9){
                $code = $remainder . $code;
            }else{
                //number dictates ascii code
                $remainder += 55; //shift to ASCII A-Z
                $code = chr($remainder) . $code;
            }
            $number = intdiv($number, 36);
        }
        return str_pad($code, $pad, "0", STR_PAD_LEFT);
    }
}
if(!function_exists("from_base36")){
    function from_base36($code){
        $number = 0; $multiplier = 1;

        while($code != ""){
            $digit = substr($code, -1);
            if(!is_numeric($digit)){
                $digit = ord($digit) - 55;
            }
            $number += $digit * $multiplier;
            $multiplier *= 36;
            $code = substr($code, 0, -1);
        }
        return $number;
    }
}

/**
 * Password generation for new clients
 */
if(!function_exists("generate_password")){
    function generate_password(){
        $existing_passwords = User::pluck("password")->toArray();
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        do{
            //sprawdź unikatowość
            $flag = false;
            $haslo = substr(str_shuffle($chars),0,12);
            foreach($existing_passwords as $ptc){ //password to check
                if($ptc == $haslo) $flag = true;
            }
        }while($flag);
        return $haslo;
    }
}

/**
 * Big ol' calculation of quests' prices
 */
if(!function_exists("price_calc")){
    function price_calc($labels, $client_id, $quoting = false){
        if($client_id == null) $client_id = $_POST['client_id'] ?? null; //odczyt tak, bo nie chce złapać argumentu
        $client = Client::find($client_id);
        $price_schema = pricing($client_id);

        $price = 0; $multiplier = 1; $positions = [];

        $price_list = DB::table("prices")
            ->select(["indicator", "service", "quest_type_id", "operation", "price_$price_schema AS price"])
            ->get();

        if($quoting){
            if($client?->is_veteran && !preg_match_all("/=/", $labels)) $labels .= "=";
            if($client?->is_patron && !preg_match_all("/-/", $labels)) $labels .= "-";
        }

        $quest_type_present = null;
        foreach($price_list as $cat){
            preg_match_all("/$cat->indicator/", $labels, $matches);
            if(count($matches[0]) > 0):
                // nuty do innego typu zlecenia za pół ceny
                $quest_type_present ??= $cat->quest_type_id;
                $price_to_add = $cat->price;
                if($cat->quest_type_id == 2 && $quest_type_present != 2) $price_to_add /= 2;

                switch($cat->operation){
                    case "+":
                        $price += $price_to_add * count($matches[0]);
                        array_push($positions, [$cat->service, _c_(as_pln($price_to_add * count($matches[0])))]);
                        break;
                    case "*":
                        $multiplier += $price_to_add * count($matches[0]);
                        $sign = ($price_to_add >= 0) ? "+" : "-";
                        array_push($positions, [$cat->service, $sign._c_(count($matches[0]) * abs($price_to_add) * 100)."%"]);
                        break;
                }
            endif;
        }

        $price *= $multiplier;
        $override = false;

        // minimal price
        $minimal_price = $quest_type_present ? QUEST_MINIMAL_PRICES()[$quest_type_present] : 0;
        $minimal_price_output = 0;
        if($price < $minimal_price){
            $price = $minimal_price;
            $minimal_price_output = $minimal_price;
            $override = true;
        }

        // manual price override
        if(preg_match_all("/\d+[\.\,]?\d+/", $labels, $matches)){
            $price = floatval(str_replace(",",".",$matches[0][0]));
            $override = true;
        }

        return [
            "price" => _c_(round($price, 2)),
            "positions" => $positions,
            "override" => $override,
            "labels" => $labels,
            "minimal_price" => $minimal_price_output,
        ];
    }
}

/**
 * Next working day
 */
if(!function_exists("get_next_working_day")){
    function get_next_working_day(){
        $workdays_capacity = explode(",", setting("available_day_until"));
        $free_days_soon = CalendarFreeDay::whereBetween("date", [Carbon::today(), Carbon::today()->addWeek()])
            ->get()->pluck("date")->toArray();

        for($day = Carbon::today()->addDay(); $day->lte(Carbon::today()->addWeek()); $day = $day->addDay()){
            if($workdays_capacity[$day->format("w")] > 0 && !in_array($day, $free_days_soon))
                return $day;
        }

        return null;
    }
}

/*************************
 * DETECTIVE FUNCTIONS
 */

 /**
 * for clients
 */
if(!function_exists("can_download_files")){
    function can_download_files($client_id, $quest_id){
        if($client_id == "") return false;
        $trust = Client::findOrFail($client_id)->trust;
        $quest = Quest::findOrFail($quest_id);
        return
            $trust >= 0
            && (
                Client::find($client_id)->is_veteran
                || $trust == 1
                || (
                    $quest->delayed_payment !== null
                    && $quest->delayed_payment?->diffInDays(Carbon::today(), false) <= 0
                    && $quest->status_id === 19
                )
            );
    }
}
if(!function_exists("pricing")){
    function pricing($client_id){
        if($client_id == "") return CURRENT_PRICING();
        else{
            $client_since = Client::find($client_id)->created_at;
            //loop for cycling through pricing schemas
            for($letter = "A"; $letter != CURRENT_PRICING(); $letter = $next_letter){
                $next_letter = chr(ord($letter) + 1);
                $this_pricing_since = Carbon::parse(setting("pricing_".$next_letter."_since"));
                if($client_since->lt($this_pricing_since)) return $letter;
            }
        }
        return CURRENT_PRICING();
    }
}
if(!function_exists("quests_unpaid")){
    function quests_unpaid($client_id, $all = false){
        $allowed_statuses = ($all) ? array_diff(range(11, 26), [18]) : [17, 19];

        $quests = Quest::where("paid", 0)
            ->whereIn("status_id", $allowed_statuses)
            ->whereHas("client", function($query){
                $query->where("trust", ">", -1);
            })
            ;
        if($client_id != 1){
            $quests = $quests->where("client_id", $client_id);
        }
        // if($all) dd(...($quests_val->get()->toArray()));
        $quests_val = $quests->sum("price");

        foreach($quests->get() as $quest){
            $quests_val -= $quest->payments_sum;
        }

        return $quests_val;
    }
}

/**
 * Odmienianie imion klientów
 */
if(!function_exists("client_polonize")){
    function client_polonize($name){
        $imie = (strpos($name, " ") == FALSE) ? $name : substr($name, 0, strpos($name, " "));
        $kobieta = (substr($imie, -1) == "a" || in_array($imie, ["Agnes"])) ? true : false;

        //odmieniacz imion
        $imiewolacz = $imie; //failsafe
        if(preg_match("/a$/", $imie)) $imiewolacz = substr($imie, 0, -1)."o";
        if(!$kobieta){
            if(preg_match("/r$/", $imie)) $imiewolacz = $imie."ze";
                if(preg_match("/er$/", $imie)) $imiewolacz = substr($imie, 0, -2)."rze";
            if(preg_match("/d$/", $imie)) $imiewolacz = $imie."zie";
            if(preg_match("/t$/", $imie)) $imiewolacz = substr($imie, 0, -1)."cie";
                if(preg_match("/st$/", $imie)) $imiewolacz = substr($imie, 0, -2)."ście";
            if(preg_match("/[bzmnsfwp]$/", $imie)) $imiewolacz = $imie."ie";
            if(preg_match("/(l|j|h|k|g|sz|cz|rz)$/", $imie)) $imiewolacz = $imie."u";
            if(preg_match("/v$/", $imie)) $imiewolacz = substr($imie, 0, -1)."wie";
            if(preg_match("/x$/", $imie)) $imiewolacz = substr($imie, 0, -1)."ksie";
            if(preg_match("/(ei|ai)$/", $imie)) $imiewolacz = substr($imie, 0, -1)."ju";
            if(preg_match("/(ek|eg)$/", $imie)) $imiewolacz = substr($imie, 0, -2)."ku";
            if(preg_match("/niec$/", $imie)) $imiewolacz = substr($imie, 0, -4)."ńcu";
            if(preg_match("/yk$/", $imie)) $imiewolacz = $imie."u";
            if(preg_match("/ł$/", $imie)) $imiewolacz = substr($imie, 0, -1)."le";
                if(preg_match("/eł$/", $imie)) $imiewolacz = substr($imie, 0, -3)."le";
        }

        return [
            'imiewolacz' => $imiewolacz,
            'kobieta' => $kobieta,
        ];
    }
}

/*************************
 * DECORATIVE FUNCTIONS
 */


/**
 * Show number as PLN
 */
if(!function_exists("as_pln")){
    function as_pln($value){
        return number_format($value, 2, ",", " ")." zł";
    }
}

/**
 * Order file array, so as the most important to render are first
 */
if(!function_exists("file_order")){
    function file_order($a, $b){
        $correct_order = ["mp4", "mp3", "ogg"];
        $ext_a = preg_replace("/.*\.(.*)$/", "$1", $a);
        $ext_b = preg_replace("/.*\.(.*)$/", "$1", $b);
        return (array_search($ext_a, $correct_order) < array_search($ext_b, $correct_order)) ? -1 : 1;
    }
}

/**
 * Extract file tags from its version name
 */
if(!function_exists("file_name_and_tags")){
    function file_name_and_tags($ver_sub){
        $tags_raw = preg_replace("/^.*\[(.*)\]$/", "$1", $ver_sub);
        $ver_sub = preg_replace("/^(.*)\[.*\]$/", "$1", $ver_sub);

        if($tags_raw == $ver_sub) return [$ver_sub, []];

        $tags = null;
        preg_match_all("/([cdm]|t[+-]?\d+)/", $tags_raw, $tags);

        return [$ver_sub, $tags[1]];
    }
}

/**
 * Turn array to html list
 */
if(!function_exists("arr_to_list")){
    function arr_to_list($array, $ordered = false){
        $list_tag = $ordered ? "ol" : "ul";
        echo "<$list_tag>";
        foreach($array as $label => $value) echo "<li><strong>$label</strong>: $value</li>";
        echo "</$list_tag>";
    }
}
