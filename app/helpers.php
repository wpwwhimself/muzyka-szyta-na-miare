<?php

use App\Models\Client;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Song;
use App\Models\StatusChange;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * "CONSTANTS"
 */
if(!function_exists("VETERAN_FROM")){
    function VETERAN_FROM(){
        return DB::table("settings")->where("setting_name", "veteran_from")->value("value_str");
    }
}
if(!function_exists("CURRENT_PRICING")){
    function CURRENT_PRICING(){
        return DB::table("settings")->where("setting_name", "current_pricing")->value("value_str");
    }
}

/**
 * Converts user ID to string depicting, which kind of view it is supposed to see. Works in role-specific views (like dashboard)
 */
if(!function_exists("user_role")){
    function user_role(){
        $role = "";
        switch(Auth::id()){
            case 1: $role = "archmage"; break;
            default: $role = "client"; break;
        }
        return $role;
    }
}

if(!function_exists("song_quest_type")){
    function song_quest_type($song_id){
        $type_letter = substr($song_id, 0, 1);
        if($type_letter == "A") return "nie ustalono (archiwalne)";
        return QuestType::where("code", $type_letter)->first();
    }
}

if(!function_exists("next_quest_id")){
    function next_quest_id($quest_type_id){
        $letter = QuestType::find($quest_type_id)->value("code");
        $newest_id = Quest::where("id", "like", "$letter%")->orderBy("id", "desc")->value("id");
        if(!$newest_id){
            return $letter . date("y") . "-00";
        }
        $dash_pos = strpos($newest_id, "-");
        $newest_id_last = substr($newest_id, $dash_pos + 1);
        return $letter . date("y") . "-" . to_base36(from_base36($newest_id_last) + 1);
    }
}
if(!function_exists("next_song_id")){
    function next_song_id($quest_type_id){
        $letter = QuestType::find($quest_type_id)->code;
        $newest_id = Song::where("id", "like", "$letter%")->orderBy("id", "desc")->value("id");
        if(!$newest_id){
            return $letter . "000";
        }
        $newest_id_last = substr($newest_id, 1);
        return $letter . to_base36(from_base36($newest_id_last) + 1, 3);
    }
}

if(!function_exists("generate_password")){
    function generate_password(){
        $existing_passwords = User::pluck("password")->toArray();
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-";
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

if(!function_exists("price_calc")){
    function price_calc($labels, $client_id){
        if($client_id == null) $client_id = $_POST['client_id']; //odczyt tak, bo nie chce złapać argumentu
        $price_schema = pricing($client_id);

        $price = 0; $multiplier = 1; $positions = [];

        $price_list = DB::table("prices")
            ->select(["indicator", "service", "operation", "price_$price_schema AS price"])
            ->get();

        if(is_veteran($client_id)) $labels .= "=";
        if(is_patron($client_id)) $labels .= "-";

        foreach($price_list as $cat){
            preg_match_all("/$cat->indicator/", $labels, $matches);
            if(count($matches[0]) > 0):
                switch($cat->operation){
                    case "+":
                        $price += $cat->price * count($matches[0]);
                        array_push($positions, [$cat->service, count($matches[0])." × ".$cat->price." zł"]);
                        break;
                    case "*":
                        $multiplier += $cat->price * count($matches[0]);
                        array_push($positions, [$cat->service, count($matches[0])." × ".($cat->price*100)."%"]);
                        break;
                }
            endif;
        }

        $price *= $multiplier;

        // price override
        $override = false;
        if(preg_match_all("/\d+[\.\,]?\d+/", $labels, $matches)){
            $price = floatval(str_replace(",",".",$matches[0][0]));
            $override = true;
        }

        return [$price, $positions, $override];
    }
}

if(!function_exists("is_priority")){
    function is_priority($quest_id){
        //requesty mają UUID
        $is_request = strlen($quest_id) == 36;

        return preg_match(
            "/z/",
            ($is_request ? Request::findOrFail($quest_id)->price_code : Quest::findOrFail($quest_id)->price_code_override)
        );
    }
}

if(!function_exists("is_veteran")){
    function is_veteran($client_id){
        if($client_id == "") return false;
        return client_exp($client_id) >= VETERAN_FROM();
    }
}
if(!function_exists("is_patron")){
    function is_patron($client_id){
        if($client_id == "") return false;
        return Client::find($client_id)->helped_showcasing == 2;
    }
}
if(!function_exists("client_exp")){
    function client_exp($client_id){
        if($client_id == "") return 0;
        return Client::find($client_id)->quests->where("status_id", 19)->count();
    }
}
if(!function_exists("upcoming_quests")){
    function upcoming_quests($client_id){
        if($client_id == "") return 0;
        return Client::find($client_id)->quests->whereIn("status_id", [11, 12, 15, 16, 26])->count();
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
                $this_pricing_since = Carbon::parse(DB::table("settings")->where("setting_name", "pricing_".$next_letter."_since")->value("value_str"));
                if($client_since->lt($this_pricing_since)) return $letter;
            }
        }
        return CURRENT_PRICING();
    }
}

if(!function_exists("quests_unpaid")){
    function quests_unpaid($client_id, $all = false){
        $allowed_statuses = ($all) ? array_diff(range(11, 26), [18]) : [19];

        $quests_val = Quest::where("paid", 0)->whereIn("status_id", $allowed_statuses);
        if($client_id != 1){
            $quests_val = $quests_val->where("client_id", $client_id);
        }
        $quests_val = $quests_val->sum("price");

        return $quests_val;
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
?>
