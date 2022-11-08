<?php

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

if(!function_exists("price_calc")){
    function price_calc($labels, $client_id){
        if($client_id == null) $client_id = $_POST['client_id']; //odczyt tak, bo nie chce złapać argumentu
        $price_schema = pricing($client_id);

        $price = 0; $multiplier = 1; $positions = [];

        $price_list = DB::table("prices")
            ->select(["indicator", "service", "operation", "price_$price_schema AS price"])
            ->get();

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

        if(is_veteran($client_id)){
            $discount_value = ($price_schema == "A") ? 0.3 : 0.15;
            $price *= (1 - $discount_value);
            array_push($positions, ["zniżka stałego klienta", "-".($discount_value*100)."%"]);
        }

        // price override
        $override = false;
        if(preg_match_all("/\d+[\.\,]?\d+/", $labels, $matches)){
            $price = floatval(str_replace(",",".",$matches[0][0]));
            $override = true;
        }

        return [$price, $positions, $override];
    }
}

if(!function_exists("is_veteran")){
    function is_veteran($client_id){
        if($client_id == "") return false;
        $veteran_from = DB::table("settings")->where("setting_name", "veteran_from")->value("value_str");
        $quest_count = Client::find($client_id)->quests->where("status_id", 19)->count();
        return $quest_count >= $veteran_from;
    }
}

if(!function_exists("pricing")){
    function pricing($client_id){
        $current_pricing = DB::table("settings")->where("setting_name", "current_pricing")->value("value_str");
        if($client_id == "") return $current_pricing;
        else{
            $client_since = Client::find($client_id)->value("created_at");
            //loop for cycling through pricing schemas
            for($letter = "A"; $letter != $current_pricing; $letter = $next_letter){
                $next_letter = chr(ord($letter) + 1);
                $this_pricing_since = Carbon::parse(DB::table("settings")->where("setting_name", "pricing_".$next_letter."_since")->value("value_str"));
                if($client_since->lt($this_pricing_since)) return $letter;
            }
        }
        return $current_pricing;
    }
}

if(!function_exists("to_base36")){
    function to_base36($number){
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
        return $code;
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
