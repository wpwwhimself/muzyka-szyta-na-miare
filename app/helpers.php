<?php

use App\Models\CalendarFreeDay;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Song;
use App\Models\User;
use App\Models\FileTag;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if(!function_exists("QUEST_MINIMAL_PRICES")){
    function QUEST_MINIMAL_PRICES(){
        return array_combine(
            [1, 2, 3],
            explode(",", setting("msznm_quest_minimal_price"))
        );
    }
}

/**
 * "CONSTANTS"
 */
if(!function_exists("VETERAN_FROM")){
    function VETERAN_FROM(){
        return setting("msznm_veteran_from");
    }
}
if(!function_exists("CURRENT_PRICING")){
    function CURRENT_PRICING(){
        return setting("msznm_current_pricing");
    }
}
if(!function_exists("BEGINNING")){
    function BEGINNING(){
        return Carbon::parse('2023-03-13');
    }
}
if(!function_exists("INCOME_LIMIT")){
    function INCOME_LIMIT(){
        $thresholds = [
            "2025-01-01" => 3499.5,
            "2024-07-01" => 3225,
            "2024-01-01" => 3181.5,
            "2023-07-01" => 2700,
        ];
        foreach ($thresholds as $date => $threshold) {
            if(Carbon::parse($date)->diffInDays(Carbon::today(), false) >= 0) return $threshold;
        }
        return 1745;
    }
}
if(!function_exists("OBSERVER_ERROR")){
    function OBSERVER_ERROR(){
        return "Jako obserwator nie możesz tego zrobić";
    }
}
if(!function_exists("STATUSES_WITH_ELEVATED_HISTORY")){
    function STATUSES_WITH_ELEVATED_HISTORY(){
        return [6, 12, 16, 21, 26, 96];
    }
}
if(!function_exists("STATUSES_WAITING_FOR_ME")){
    function STATUSES_WAITING_FOR_ME(){
        return [1, 6, 11, 12, 13, 14, 16, 21, 26, 96];
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
        $user_id ??= Auth::id();
        return User::find($user_id)?->hasRole("archmage") ?? false;
    }
}

/**
 * Censor data for showcase account
 */
if(!function_exists("_c_")){
    function _c_($data){
        return (Auth::id() === 0) ? preg_replace("/[\d, zł]/", "⁎", $data) : $data;
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
        $newest_id = Song::where("id", "like", "$letter%")->orderBy("id", "desc")->value("id") ?? $letter . "000";
        $newest_id_last = substr($newest_id, 1);
        if(in_array($newest_id_last, ["000", "ZZZ"])){
            return $letter . "000";
        }
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
        $existing_passwords = User::all()->map(fn ($u) => $u->notes?->password)->filter()->toArray();
        $chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789";
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
 * Next working day
 */
if(!function_exists("get_next_working_day")){
    function get_next_working_day(){
        $workdays_capacity = explode(",", setting("msznm_available_day_until"));
        $free_days_soon = CalendarFreeDay::where("date", ">=", Carbon::today())
            ->get()->pluck("date")->toArray();

        $day = Carbon::today()->addDay();
        while(true){
            if($workdays_capacity[$day->format("w")] > 0 && !in_array($day, $free_days_soon))
                return $day;
            $day = $day->addDay();
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
        $client = User::findOrFail($client_id);
        $quest = Quest::findOrFail($quest_id);
        return
            $client->can_see_files
            && (
                $client->is_veteran
                || $client->trust >= 1
                || $quest->paid
                || (
                    ($quest->delayed_payment?->diffInDays(Carbon::today(), false) <= 0 ?? true)
                    && $quest->status_id === 19
                )
            );
    }
}
if(!function_exists("pricing")){
    function pricing($client_id){
        if($client_id == "") return CURRENT_PRICING();
        else{
            $client_since = User::find($client_id)->created_at;
            //loop for cycling through pricing schemas
            for($letter = "A"; $letter != CURRENT_PRICING(); $letter = $next_letter){
                $next_letter = chr(ord($letter) + 1);
                $this_pricing_since = Carbon::parse(setting("msznm_pricing_".$next_letter."_since"));
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
            ->whereHas("user.notes", function($query){
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

/**
 * takes a full, nested list of warnings and returns whether at least one of them is active
 */
if(!function_exists("sumWarnings")){
    function sumWarnings($warnings, $shallow = false) {
        return array_reduce($warnings, function ($carry, $innerArray) use ($shallow) {
            if ($shallow) {
                return $carry || (bool) $innerArray;
            }

            return $carry || array_reduce($innerArray, function ($innerCarry, $element) {
                return $innerCarry || (bool) $element;
            }, false);
        }, false);
    }
}

/**
 * calculating tax
 */
if(!function_exists("tax_calc")){
    function tax_calc($income) {
        $threshold = 120e3;
        $tax_deduction = 3.6e3;

        $calculated = $income > $threshold
            ? (10.8e3 + ($income - $threshold) * 0.32)
            : 0.12 * $income - $tax_deduction
        ;

        return max(0, $calculated);
    }
}

/**
 * take ID and say whether it's a request
 */
if(!function_exists("is_request")){
    function is_request($id) {
        return strlen($id) == 36;
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
        return number_format($value, 2, ",", " ")." zł";
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

/**
 * cleanup of tracking data from youtube links
 */
if(!function_exists("yt_cleanup")){
    function yt_cleanup($urls){
        return preg_replace("/[?&]si=[\d\w]{16}/", "", $urls ?? "");
    }
}
