<?php

use Illuminate\Support\Facades\DB;

if(!function_exists("price_calc")){
    function price_calc($labels, $price_schema = "b", $veteran_discount = false){
        if(is_numeric($labels)) return $labels;

        $price = 0; $multiplier = 1;

        $price_list = DB::table("prices")
            ->select(["indicator", "price_$price_schema AS price"])
            ->get();

        foreach($price_list as $cat){
            preg_match_all("/$cat->indicator/", $labels, $matches);
            switch($cat->operation){
                case "+":
                    $price += $cat->price * count($matches[0]);
                    break;
                case "*":
                    $multiplier += $cat->price * count($matches[0]);
                    break;
            }
        }

        $price *= $multiplier;

        if($veteran_discount){
            if($price_schema == "A"){
                $price *= (1 - 0.3);
            }else{
                $price *= (1 - 0.15);
            }
        }

        return $price;
    }
}

?>
