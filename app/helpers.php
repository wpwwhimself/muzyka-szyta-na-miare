<?php

use Illuminate\Support\Facades\DB;

if(!function_exists("price_calc")){
    function price_calc($labels, $price_schema = "B", $veteran_discount = false){
        if(is_numeric($labels)) return $labels;

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

        if($veteran_discount){
            $discount_value = ($price_schema == "A") ? 0.3 : 0.15;
            $price *= (1 - $discount_value);
            array_push($positions, ["zniżka stałego klienta", "-".($discount_value*100)."%"]);
        }

        return [$price, $positions];
    }
}

?>
